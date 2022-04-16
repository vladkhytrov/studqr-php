<?php

namespace App\Enum;

enum EventStatus: string
{
    case RUNNING = 'running';
    case FINISHED = 'finished';
}
