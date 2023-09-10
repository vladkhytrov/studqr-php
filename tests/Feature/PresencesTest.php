<?php

namespace Tests\Feature;

use App\Models\Lecture;
use App\Models\Presence;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class PresencesTest extends TestCase
{

    use RefreshDatabase;

    public function test_get_presences()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);

        // create 3 presences
        for ($i = 0; $i < 3; $i++) {
            $lecture = Lecture::factory()->create(
                [
                    'teacher_id' => $teacher->id,
                    'name'       => 'Ingegneria del Software',
                ]
            );
            Presence::create([
                'qr_id'      => (string)Uuid::uuid4(),
                'lecture_id' => $lecture->id,
                'user_id'    => $student->id,
            ]);
        }

        $response = $this
            ->actingAs($student)
            ->withHeader('Accept', 'application/json')
            ->get(
                '/api/presences'
            );

        Log::info(print_r($response->json()));

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }
}
