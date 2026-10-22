<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ImpactController
 */
class ImpactControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function index_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $response = $this->get(url('en/impact'));

        $response->assertOk();
        $response->assertViewIs('impact.impact_page');
        $response->assertViewHas('totalResources');
        $response->assertViewHas('totalSubjects');

        $this->assertNotNull($response->viewData('totalResources'));
        $this->assertNotNull($response->viewData('totalSubjects'));

    }
}
