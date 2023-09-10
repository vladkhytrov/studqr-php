<?php

namespace App\Http\Services;

use App\Models\Presence;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PresenceService
{

    /**
     * Registers the presence of student
     *
     * @param Request $request
     *
     * @return Response
     */
    public function add(Request $request): Response
    {
        $user = Auth::user();

        $request->validate([
            'lecture_id' => 'required|string|max:255',
        ]);

        Presence::query()->create(
            [
                'lecture_id' => $request->input('lecture_id'),
                'user_id'    => $user->id,
            ]
        );

        return new Response("Registered successfully", 200);
    }

    /**
     * Get all presences of a student
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getPresences(Request $request): JsonResponse
    {

        $user = Auth::user();

        $presences = DB::table('presences')
            ->join('lectures', 'presences.lecture_id', '=', 'lectures.id')
            ->select('presences.lecture_id', 'lectures.name')
            ->where('presences.user_id', '=', $user->id)
            ->get();

        return response()->json($presences->toArray());
    }

}
