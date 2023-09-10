<?php

namespace App\Http\Controllers;

use App\Http\Services\LectureService;
use Illuminate\Http\Request;

class LectureController extends Controller
{
    protected LectureService $lectureService;

    public function __construct(LectureService $lectureService)
    {
        $this->lectureService = $lectureService;
    }

    public function create(Request $request)
    {
        return $this->lectureService->create($request);
    }

    public function start(Request $request)
    {
        return $this->lectureService->start($request);
    }

    public function stop(Request $request)
    {
        return $this->lectureService->stop($request);
    }

    public function getLectures(Request $request)
    {
        return $this->lectureService->getLectures($request);
    }

    public function getStudents(Request $request)
    {
        return $this->lectureService->getStudents($request);
    }

}
