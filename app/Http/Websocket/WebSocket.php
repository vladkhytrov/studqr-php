<?php

namespace App\Http\Websocket;

use App\Enum\MsgType;
use App\Models\Presence;
use App\Models\QrCode;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class WebSocket implements MessageComponentInterface
{

    private array $connections = [];

    private function sendNewQr(string $lectureId)
    {
        // create unique id
        $qrId = (string)Uuid::uuid4();
        QrCode::create([
            'qr_id' => $qrId,
            'lecture_id' => $lectureId
        ]);

        $presences = Presence::whereLectureId($lectureId)->count();
        Log::info('pre: ' . $presences);

        $msg = json_encode(
            [
                'type'  => MsgType::QR_NEW->value,
                'qr_id' => $qrId,
                'presences' => $presences,
            ]
        );

        Log::info("WS sending new QR code");
        $this->connections[$lectureId]['connection']->send($msg);
    }

    /**
     * Student scanned QR code
     */
    private function onQrScanned(ConnectionInterface $from, mixed $jsonMsg)
    {
        $studentId = $jsonMsg['student_id'];
        $qrId = $jsonMsg['qr_id'];

        // find qr in db
        $qr = QrCode::whereQrId($qrId)->first();
        // get associated lecture
        $lectureId = $qr->lecture_id;

        if (!is_null($qr)) {
            // create presence

            // a single qr_id mus be used only by one student
            // qr_id is unique in presences table, so only the first qr scan will work
            try {
                DB::beginTransaction();

                Presence::create([
                    'qr_id'      => $qrId,
                    'lecture_id' => $lectureId,
                    'user_id' => $studentId,
                ]);

                DB::commit();

                Log::info('Presence registered successfully');
                $this->sendNewQr($lectureId);

                $this->sendQrScanSuccessMsg($from);
            } catch (Exception $e) {
                Log::error($e->getMessage());
                Log::info('Presence registration error, used duplicate QR code');

                DB::rollBack();

                $this->sendQrScanErrorMsg($from, 'Codice è stato già utilizzato, riprova');
            }
        } else {
            Log::info('Presence registration error, used not existing QR code');
            $this->sendQrScanErrorMsg($from, 'Codice non esistente');
        }
    }

    private function sendQrScanSuccessMsg(ConnectionInterface $to)
    {
        $to->send(json_encode(['type' => MsgType::QR_SCAN_SUCCESS->value]));
    }

    private function sendQrScanErrorMsg(ConnectionInterface $to, string $msg)
    {
        $to->send(json_encode(['type' => MsgType::QR_SCAN_ERROR->value, 'msg' => $msg]));
    }

    public function onOpen(ConnectionInterface $conn)
    {
        Log::info("New connection! ({$conn->resourceId})\n");

        $conn->send('hellooo');
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        //Log::info("onMessage from: " . $from->resourceId . "\n");

        $jsonMsg = json_decode($msg, true);

        print_r($jsonMsg);

        // teacher starts the lecture
        // here we bind the teacher's connection to the lecture_id
        // we will use this connection to send the messages to teacher's client
        if ($jsonMsg['type'] == MsgType::LECTURE_START->value) {
            Log::info("WS received LECTURE_START msg");
            $teacherId = $jsonMsg['teacher_id'];
            $lectureId = $jsonMsg['lecture_id'];
            $this->connections[$lectureId] = [
                'connection' => $from,
                'teacher_id' => $teacherId,
            ];

            $this->sendNewQr($lectureId);
            //$this->connections[$lectureId]['connection']->send('Connection registered');
        }

        // clicked refresh button
        if ($jsonMsg['type'] == MsgType::QR_REFRESH->value) {
            Log::info("WS received QR_REFRESH msg");
            $lectureId = $jsonMsg['lecture_id'];
            $this->sendNewQr($lectureId);
        }

        // teacher stops the lecture
        if ($jsonMsg['type'] == MsgType::LECTURE_STOP->value) {
            Log::info("WS received LECTURE_STOP msg");
            $lectureId = $jsonMsg['lecture_id'];

            unset($this->connections[$lectureId]);
        }

        // student scanned qr
        if ($jsonMsg['type'] == MsgType::QR_SCANNED->value) {
            Log::info("WS received QR_SCANNED msg");
            $this->onQrScanned($from, $jsonMsg);
        }

    }

    public function onClose(ConnectionInterface $conn)
    {
        Log::info("WS onClose\n");
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        Log::info("WS onError: " . $e->getMessage() . "\n");
    }

}
