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
    public function ddl_resource_report_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        Resource::factory()->create();

        $response = $this->get('en/admin/reports/ddl');

        $response->assertOk();
        $response->assertViewIs('admin.reports.ddl_resource_report');
        $response->assertSee('All languages');
        $response->assertSee('From date');
        $response->assertSee('To date');

        $dataResponse = $this->get('en/admin/reports/ddl/data?type=unique_resources&language=all', [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $dataResponse->assertOk();
        $dataResponse->assertViewIs('admin.reports.ddl_resource_report_data');
        $dataResponse->assertViewHasAll([
            'totalResources',
            'resourcesByLanguage',
            'resourcesBySubject',
            'resourcesByType',
        ]);

        $coverageResponse = $this->get('en/admin/reports/ddl/data?type=subject_coverage&language=all', [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $coverageResponse->assertOk();
        $coverageResponse->assertViewHas('coverageRows');

        $translatedResponse = $this->get('en/admin/reports/ddl/data?type=translated_adopted&language=all', [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $translatedResponse->assertOk();
        $translatedResponse->assertViewHasAll([
            'translatedCount',
            'adoptedCount',
            'translatedUtilization',
            'adoptedUtilization',
            'translatedLanguagePairs',
            'adoptedLanguagePairs',
        ]);

        $partnerResponse = $this->get('en/admin/reports/ddl/data?type=partner_contributions&language=all', [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $partnerResponse->assertOk();
        $partnerResponse->assertViewHasAll([
            'partnerCount',
            'partnerUtilization',
            'partners',
        ]);
    }

    #[Test]
    public function ddl_resource_report_can_be_filtered_by_date_range(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        Resource::factory()->create(['created_at' => '2026-01-15 12:00:00']);
        Resource::factory()->create(['created_at' => '2026-02-15 12:00:00']);
        Resource::factory()->create(['created_at' => '2026-03-15 12:00:00']);

        $response = $this->get('en/admin/reports/ddl/data?type=unique_resources&language=all&date_from=2026-02-01&date_to=2026-02-28', [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertOk();
        $response->assertViewHas('totalResources', 1);
        $response->assertViewHas('resourcesByLanguage', function ($resources) {
            return (int) $resources->sum('total') === 1;
        });
    }

    #[Test]
    public function ddl_resource_report_rejects_an_invalid_date_range(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $response = $this->getJson('en/admin/reports/ddl/data?type=unique_resources&language=all&date_from=2026-03-01&date_to=2026-02-01');

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('date_to');
    }

    #[Test]
    public function ddl_resource_report_requires_both_dates_when_filtering_by_date(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $missingToDate = $this->getJson('en/admin/reports/ddl/data?type=unique_resources&language=all&date_from=2026-02-01');
        $missingFromDate = $this->getJson('en/admin/reports/ddl/data?type=unique_resources&language=all&date_to=2026-02-28');

        $missingToDate->assertUnprocessable()->assertJsonValidationErrors('date_to');
        $missingFromDate->assertUnprocessable()->assertJsonValidationErrors('date_from');
    }

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
