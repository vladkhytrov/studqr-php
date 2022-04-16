<?php

namespace App\Http\Services;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventService
{

    /**
     *
     *
     * @param Request $request
     *
     * @return array
     */
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $event = Event::query()->create(
            [
                'name' => $request->input('name'),
                'owner_id' => $user->id,
            ]
        );
        $user->events()->save($event);

        return [
            'eventId' => $event->id
        ];
    }

    public function start(Request $request)
    {

    }

}
