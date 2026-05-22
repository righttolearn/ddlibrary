<?php

namespace Database\Seeders;

use App\Models\Resource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResourceViewsCommentsFavoritesCountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Resource::each(function ($resource) {
            $resource->update([
                'views_count'     => $resource->views()->count(),
                'comments_count'  => $resource->comments()->count(),
                'favorites_count' => $resource->favorites()->count(),
            ]);
        });
    }
}
