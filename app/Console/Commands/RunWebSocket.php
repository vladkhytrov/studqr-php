<?php

namespace App\Console\Commands;

use App\Http\Websocket\WebSocket;
use App\Http\Websocket\WebSocketServer;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;

class RunWebSocket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start Websocket';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $server = WebSocketServer::getServer();
        $server->run();
    }
}
