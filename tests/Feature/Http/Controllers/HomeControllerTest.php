<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\News;
use App\Models\Resource;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyQuestionOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\HomeController
 */
class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function index_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        Survey::factory()->create();
        SurveyQuestion::factory()->create();
        News::factory(3)->create(['status' => 1]);
        Resource::factory()->count(3)->create();
        SurveyQuestionOption::factory()->count(10)->create();

        $response = $this->get('/en');

        $response->assertOk();

        $response->assertViewIs('home');

        $response->assertViewHas('subjectAreas');
        $response->assertViewHas('featured');
    }
}
