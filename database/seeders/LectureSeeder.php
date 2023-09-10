<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LectureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacherId = User::whereEmail('teacher@email.com')->first()->id;

        DB::table('lectures')->insert([
            'name'       => 'Analisi matematica',
            'teacher_id' => $teacherId,
        ]);

        DB::table('lectures')->insert([
            'name'       => 'Ingegneria del Software',
            'teacher_id' => $teacherId,
        ]);
    }
}
