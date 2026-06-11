<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeaturedCollectionIconsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $icons = [
            'far fa-file-alt'       => 'ph-light ph-file-text',
            'fas fa-book'           => 'ph-light ph-book-open',
            'far fa-newspaper'      => 'ph-light ph-newspaper',
            'fas fa-child'          => 'ph-light ph-baby',
            'fas fa-school'         => 'ph-light ph-building',
            'fas fa-graduation-cap' => 'ph-light ph-graduation-cap',
            'fas fa-video'          => 'ph-light ph-video',
        ];

        foreach ($icons as $old => $new) {
            DB::table('featured_collections')
                ->where('icon', $old)
                ->update(['phosphor_icon' => $new]);
        }
    }
}
