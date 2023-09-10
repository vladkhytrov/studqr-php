<?php

namespace App\Http\Controllers;

use App\Http\Services\PresenceService;
use Illuminate\Http\Request;

class PresencesController extends Controller
{
    private PresenceService $presenceService;

    public function __construct(PresenceService $presenceService)
    {
        $this->presenceService = $presenceService;
    }

    public function getPresences(Request $request)
    {
        return $this->presenceService->getPresences($request);
    }

}
