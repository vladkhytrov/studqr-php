<?php

namespace Tests\Feature;

use App\Enum\LectureStatus;
use App\Models\Lecture;
use App\Models\Presence;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class LecturesTest extends TestCase
{

    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'teacher']);
    }

    public function test_create_lecture()
    {
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->actingAs($this->user)
            ->post(
                '/api/lecture/create',
                ['name' => 'Lezione di Programmazione']
            );

        self::assertNotEmpty($response->json('id'));
        $response->assertStatus(201);
        self::assertEquals('Lezione di Programmazione', $response->json('name'));

        $lecture = Lecture::whereId($response->json('id'))->first();
        self::assertEquals(LectureStatus::IDLE->value, $lecture->status);
    }

    public function test_get_lectures()
    {
        Lecture::factory(3)->create(['teacher_id' => $this->user->id]);

        $response = $this
            ->actingAs($this->user)
            ->withHeader('Accept', 'application/json')
            ->get(
                '/api/lectures'
            );

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_get_students()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $lecture = Lecture::factory()->create(
            [
                'teacher_id' => $teacher->id,
                'name'       => 'Ingegneria del Software',
            ]
        );

        // create 3 presences
        for ($i = 0; $i < 3; $i++) {
            $student = User::factory()->create(['role' => 'student']);
            Presence::create([
                'qr_id'      => (string)Uuid::uuid4(),
                'lecture_id' => $lecture->id,
                'user_id'    => $student->id,
            ]);
        }

        $response = $this
            ->actingAs($teacher)
            ->withHeader('Accept', 'application/json')
            ->get(
                '/api/lecture/presences?lecture_id=' . $lecture->id
            );

        Log::info(print_r($response->json()));

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

}
