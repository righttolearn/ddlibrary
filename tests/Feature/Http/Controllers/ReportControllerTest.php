<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Resource;
use App\Models\ResourceLevel;
use App\Models\ResourceSubjectArea;
use App\Models\TaxonomyTerm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ReportController
 */
class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function resource_language_report_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $resources = Resource::factory()->times(3)->create();
        $taxonomyTerms = TaxonomyTerm::factory()->times(3)->create();
        $resourceLevels = ResourceLevel::factory()->times(3)->create();

        $response = $this->get('en/admin/reports/languages');

        $response->assertOk();
    }

    #[Test]
    public function resource_priorities_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $taxonomyTerms = TaxonomyTerm::factory()->times(3)->create();

        $response = $this->get('en/resources/priorities');

        $response->assertOk();
        $response->assertViewIs('reports.priorities');
        $response->assertViewHas('subjects_list');
    }

    #[Test]
    public function resource_priorities_exclusion_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $taxonomyTerms = TaxonomyTerm::factory()->times(3)->create();

        $response = $this->get('en/resources/priorities/exclusion');

        $response->assertOk();
        $response->assertViewIs('reports.exclusion');
        $response->assertViewHas('subjects_list');
    }

    #[Test]
    public function resource_report_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $response = $this->get('en/admin/reports/resources');

        $response->assertOk();
        $response->assertViewIs('admin.reports.resource_reports');
        $response->assertViewHas('supported_locales');
    }

    #[Test]
    public function resource_review_report_can_be_filtered(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        Resource::factory()->create([
            'title' => 'Dari Mathematics',
            'language' => 'fa',
            'reviewed_at' => '2026-10-10 09:00:00',
            'review_hours' => 2.25,
            'reviewed_by' => $admin->id,
        ]);

        Resource::factory()->create([
            'title' => 'English Science',
            'language' => 'en',
            'reviewed_at' => '2026-09-01 09:00:00',
            'review_hours' => 4.50,
        ]);

        $response = $this->get(
            'en/admin/reports/resource-reviews?title=Mathematics&language=fa&date_from=2026-10-01&date_to=2026-10-31&review_status=reviewed'
        );

        $response->assertOk();
        $response->assertViewIs('admin.reports.resource_reviews');
        $response->assertSee('Dari Mathematics');
        $response->assertSee($admin->username);
        $response->assertDontSee('English Science');
        $response->assertViewHas('total_review_hours', fn ($hours) => (float) $hours === 2.25);
    }

    #[Test]
    public function admin_can_add_and_update_resource_review_hours(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $resource = Resource::factory()->create();

        $response = $this->post("en/admin/reports/resource-reviews/{$resource->id}", [
            'review_hours' => '3.75',
        ]);

        $response->assertRedirect();

        $resource->refresh();
        $reviewedAt = $resource->reviewed_at;

        $this->assertSame('3.75', $resource->review_hours);
        $this->assertSame($admin->id, $resource->reviewed_by);
        $this->assertNotNull($reviewedAt);

        $this->post("en/admin/reports/resource-reviews/{$resource->id}", [
            'review_hours' => '5.50',
        ])->assertRedirect();

        $resource->refresh();

        $this->assertSame('5.50', $resource->review_hours);
        $this->assertTrue($reviewedAt->equalTo($resource->reviewed_at));
    }

    #[Test]
    public function resource_review_hours_are_limited_to_one_hundred_and_two_decimal_places(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $resource = Resource::factory()->create();

        $this->post("en/admin/reports/resource-reviews/{$resource->id}", [
            'review_hours' => '100.01',
        ])->assertSessionHasErrors('review_hours');

        $this->post("en/admin/reports/resource-reviews/{$resource->id}", [
            'review_hours' => '1.234',
        ])->assertSessionHasErrors('review_hours');

        $this->assertNull($resource->fresh()->reviewed_at);
    }

    #[Test]
    public function resource_subject_report_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $taxonomyTerms = TaxonomyTerm::factory()->times(3)->create();
        $resourceSubjectAreas = ResourceSubjectArea::factory()->times(3)->create();
        $resourceLevels = ResourceLevel::factory()->times(3)->create();

        $response = $this->get('en/admin/reports/resources/subjects');

        $response->assertOk();
    }
}
