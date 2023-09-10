<?php

namespace App\Http\Websocket;

use Illuminate\Support\Facades\Log;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

class WebSocketServer
{

    private static IoServer $server;

    public static function getServer(): IoServer
    {
        if (!isset(self::$server)) {
            Log::info('creating new ws');
            self::$server = IoServer::factory(
                new HttpServer(
                    new WsServer(
                        new WebSocket()
                    )
                ),
                8071
            );
        }
        Log::info('returning ws');
        return self::$server;
    }

}
