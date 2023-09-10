<?php

namespace App\Enum;

enum MsgType: string
{
    case LECTURE_START = 'lecture_start';
    case LECTURE_STOP = 'lecture_stop';
    case QR_REFRESH = 'qr_refresh';
    case QR_NEW = 'qr_new';
    case QR_SCANNED = 'qr_scanned';
    case QR_SCAN_SUCCESS = 'qr_scan_success';
    case QR_SCAN_ERROR = 'qr_scan_error';
}
