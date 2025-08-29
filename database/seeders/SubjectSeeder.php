<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            [
                'name' => 'ภาษาไทย',
                'description' => 'ข้อสอบวิชาภาษาไทยระดับประถมศึกษา',
                'max_questions' => 50,
                'status' => 'active',
            ],
            [
                'name' => 'คณิตศาสตร์',
                'description' => 'ข้อสอบวิชาคณิตศาสตร์พื้นฐาน',
                'max_questions' => 40,
                'status' => 'active',
            ],
            [
                'name' => 'วิทยาศาสตร์',
                'description' => 'ข้อสอบวิทยาศาสตร์ทั่วไป',
                'max_questions' => 45,
                'status' => 'active',
            ],
            [
                'name' => 'สังคมศึกษา',
                'description' => 'ข้อสอบสังคมศึกษา ศาสนาและวัฒนธรรม',
                'max_questions' => 35,
                'status' => 'active',
            ],
            [
                'name' => 'ภาษาอังกฤษ',
                'description' => 'ข้อสอบภาษาอังกฤษพื้นฐาน',
                'max_questions' => 30,
                'status' => 'active',
            ],
        ];

        foreach ($subjects as $subject) {
            \App\Models\Subject::create($subject);
        }
    }
}
