<?php

namespace App\Http\Services;

use App\Models\Lecture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LectureService
{

    /**
     * Creates the lecture
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $lecture = Lecture::query()->create(
            [
                'name'       => $request->input('name'),
                'teacher_id' => $user->id,
            ]
        );

        //$user->events()->save($lecture);

        return response()->json($lecture->toArray(), 201);
    }

    /**
     * Get list of all lectures
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getLectures(Request $request): JsonResponse
    {
        $user = Auth::user();
        $lectures = Lecture::whereTeacherId($user->id)->get();

        return response()->json($lectures->toArray());
    }

    /**
     * Get all students present at lecture
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getStudents(Request $request): JsonResponse
    {
        $request->validate([
            'lecture_id' => 'required|int|max:255',
        ]);

        $user = Auth::user();

        $presences = DB::table('presences')
            ->join('users', 'presences.user_id', '=', 'users.id')
            ->select('presences.lecture_id', 'users.first_name', 'users.last_name', 'users.email')
            ->where('presences.lecture_id', '=', $request->input('lecture_id'))
            ->get();

        Log::info($presences->toArray());

        return response()->json($presences->toArray());
    }

    /**
     * Start the lecture which means open a websocket and start generating QR codes.
     *
     * @param Request $request
     *
     * @return array
     */
    public function start(Request $request)
    {
        $request->validate([
            'lecture_id' => 'required|string|max:255',
        ]);

        $lecture = Lecture::query();
    }

    /**
     * Stops the lecture
     *
     * @param Request $request
     *
     * @return array
     */
    public function stop(Request $request)
    {

    }

}
