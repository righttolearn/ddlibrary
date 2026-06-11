<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectAreaIconsSeeder extends Seeder
{
    public function run(): void
    {
        $icons = [
            'mathematics-icon.png' => 'ph-light ph-math-operations',
            'social-sciences-icon.png' => 'ph-light ph-users-three',
            'earth-sciences-icon.png' => 'ph-light ph-globe',
            'life-sciences-icon.png' => 'ph-light ph-leaf',
            'language-arts-icon.png' => 'ph-light ph-book-open-text',
            'applied-sciences-icon-2.png' => 'ph-light ph-wrench',
            'arts-and-humanities-icon.png' => 'ph-light ph-palette',
            'business-and-communication-icon.png' => 'ph-light ph-briefcase',
            'career-and-technical-education-icon.png' => 'ph-light ph-graduation-cap',
            'education-icon.png' => 'ph-light ph-chalkboard-teacher',
            'history-icon.png' => 'ph-light ph-hourglass',
            'physical-sciences-icon.png' => 'ph-light ph-atom',
        ];

        foreach ($icons as $fileName => $icon) {
            DB::table('static_subject_area_icons')
                ->where('file_name', 'like', str_replace('.png', '%', $fileName))
                ->update(['phosphor_icon' => $icon]);
        }
    }
}
