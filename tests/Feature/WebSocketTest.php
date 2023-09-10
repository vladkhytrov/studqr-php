<?php

namespace Tests\Feature;

use App\Enum\MsgType;
use App\Http\Websocket\WebSocket;
use App\Models\Lecture;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ratchet\ConnectionInterface;
use Tests\TestCase;

class WebSocketTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Lecture start, send new QR.
     *
     * @return void
     * @throws Exception
     */
    public function test_lecture_start() : void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $lecture = Lecture::factory()->create(
            [
                'teacher_id' => $teacher->id,
                'name'       => 'Ingegneria del Software',
            ]
        );

        $conn = $this->createMock(ConnectionInterface::class);

        $conn->expects(self::once())
            ->method('send')
            ->willThrowException(new Exception('ageage'));

        $msg = [
            'type'       => MsgType::LECTURE_START->value,
            'teacher_id' => $teacher->id,
            'lecture_id' => $lecture->id,
        ];

        $webSocket = new WebSocket();

        $webSocket->onMessage($conn, json_encode($msg));
    }

    // lecture stop

    // qr refresh

    // qr scanned
}
