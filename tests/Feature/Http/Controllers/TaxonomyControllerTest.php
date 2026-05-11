<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\TaxonomyVocabularyEnum;
use App\Models\TaxonomyHierarchy;
use App\Models\TaxonomyTerm;
use App\Models\TaxonomyVocabulary;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\TaxonomyController
 */
class TaxonomyControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function create_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $taxonomyVocabularies = TaxonomyVocabulary::factory()->times(3)->create();
        $response = $this->actingAs($admin)->get(route('taxonomycreate'));

        $response->assertOk();
        $response->assertViewIs('admin.taxonomy.taxonomy_create');
        $response->assertViewHas('vocabulary');
    }

    #[Test]
    public function create_translate_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $taxonomyVocabulary = TaxonomyVocabulary::factory()->create();
        $taxonomyTerm = TaxonomyTerm::factory()->create();
        $taxonomyHierarchy = TaxonomyHierarchy::factory()->create(['tid' => $taxonomyTerm->id, 'aux_id' => $taxonomyTerm->id]);
        $taxonomyTerms = TaxonomyTerm::factory()->times(3)->create();

        $response = $this->actingAs($admin)->get("en/admin/taxonomy/create-translate/$taxonomyTerm->id/null/en");

        $response->assertOk();
        $response->assertViewIs('admin.taxonomy.taxonomy_create_translate');
        $response->assertViewHas('vocabulary');
        $response->assertViewHas('tnid');
        $response->assertViewHas('vid');
        $response->assertViewHas('lang');
        $response->assertViewHas('weight');
    }

    #[Test]
    public function edit_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $taxonomyTerm = TaxonomyTerm::factory()->create();
        TaxonomyHierarchy::factory()->create();
        $taxonomyVocabularies = TaxonomyVocabulary::factory()->times(3)->create();
        TaxonomyTerm::factory()->times(3)->create();

        $response = $this->actingAs($admin)->get('en/admin/taxonomy/edit/'.$taxonomyVocabularies->first()->vid."/$taxonomyTerm->id");

        $response->assertOk();
        $response->assertViewIs('admin.taxonomy.taxonomy_edit');
        $response->assertViewHas('term');
        $response->assertViewHas('vocabulary');
        $response->assertViewHas('parents');
        $response->assertViewHas('theParent');
    }

    #[Test]
    public function index_returns_an_ok_response(): void
    {
        TaxonomyTerm::factory()->times(3)->create();
        TaxonomyVocabulary::factory()->times(3)->create();

        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $response = $this->actingAs($admin)->get(route('gettaxonomylist'));

        $response->assertOk();
        $response->assertViewIs('admin.taxonomy.taxonomy_list');
        $response->assertViewHas('terms');
        $response->assertViewHas('searchBar');
    }

    #[Test]
    public function store_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        TaxonomyVocabulary::factory()->times(3)->create();
        $vocabulary = TaxonomyVocabulary::factory()->create();
        TaxonomyVocabulary::factory()->times(5)->create();

        $response = $this->actingAs($admin)->post('en/admin/taxonomy/store', [
            'vid' => $vocabulary->vid,
            'name' => 'New taxonomy',
            'weight' => 1,
            'language' => 'en',
        ]);

        $response->assertRedirect();
        $this->assertEquals(1, TaxonomyTerm::where('vid', $vocabulary->vid)->count());
        $this->assertEquals('New taxonomy', TaxonomyTerm::where('vid', $vocabulary->vid)->value('name'));
    }

    #[Test]
    public function store_translate_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $taxonomyTerm = TaxonomyTerm::factory()->create();
        TaxonomyVocabulary::factory()->times(3)->create();
        $vocabulary = TaxonomyVocabulary::factory()->create();

        $response = $this->actingAs($admin)->post(route('taxonomytranslatestore', ['tnid' => $taxonomyTerm->id]), [
            'vid' => $vocabulary->vid,
            'name' => 'New taxonomy translate',
            'weight' => 1,
            'language' => 'en',
        ]);

        $response->assertRedirect();
    }

    #[Test]
    public function translate_returns_an_ok_response(): void
    {
        $taxonomyTerm = TaxonomyTerm::factory()->create();
        $taxonomyTerms = TaxonomyTerm::factory()->times(3)->create();

        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $response = $this->actingAs($admin)->get("en/admin/taxonomy/translate/{$taxonomyTerm->id}");

        $response->assertOk();
        $response->assertViewIs('admin.taxonomy.taxonomy_translate');
        $response->assertViewHas('translations');
        $response->assertViewHas('supportedLocals');
        $response->assertViewHas('tnid');
        $response->assertViewHas('tid');
    }

    #[Test]
    public function update_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $taxonomyTerm = TaxonomyTerm::factory()->create();
        TaxonomyVocabulary::factory()->times(3)->create();
        $vocabulary = TaxonomyVocabulary::factory()->create();

        $response = $this->actingAs($admin)->get(route('taxonomyedit', ['vid' => $vocabulary->vid, 'tid' => $taxonomyTerm->id]));

        $response->assertOk();
        $response->assertViewIs('admin.taxonomy.taxonomy_edit');
        $response->assertViewHas('term');
        $response->assertViewHas('vocabulary');
        $response->assertViewHas('parents');
        $response->assertViewHas('theParent');
    }

    // Subject areas tests

    #[Test]
    public function subject_areas_index_route_returns_ok(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $vid = TaxonomyVocabularyEnum::ResourceSubject->value;
        TaxonomyTerm::factory()->create([
            'vid' => $vid,
            'name' => 'Science',
            'tnid' => 0,
            'language' => 'en',
        ]);

        $response = $this->actingAs($admin)->get(route('subject_areas.index',['vid' => $vid]));

        $response->assertOk();
        $response->assertViewIs('admin.taxonomy.subject-area.index');
        $response->assertViewHas('subjectAreas');
        $response->assertViewHas('languages');
    }

    #[Test]
    public function subject_area_edit_or_create_route_without_tnid_returns_ok(): void
    {
        $this->refreshApplicationWithLocale('en');

        $vid = TaxonomyVocabularyEnum::ResourceSubject->value;
        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $response = $this->actingAs($admin)->get(route('subject_area.edit_or_create', ['vid' => $vid]));

        $response->assertOk();
        $response->assertViewIs('admin.taxonomy.subject-area.edit');
        $response->assertViewHas('parents');
        $response->assertViewHas('terms');
        $response->assertViewHas('languages');
        $response->assertViewHas('tnid');
    }

    #[Test]
    public function subject_area_edit_or_create_route_with_valid_tnid_returns_ok(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $vid = TaxonomyVocabularyEnum::ResourceSubject->value;
        $term = TaxonomyTerm::factory()->create([
            'vid' => $vid,
            'name' => 'Science',
            'tnid' => 0,
            'language' => 'en',
        ]);
        $term->update(['tnid' => $term->id]);
        $vid = TaxonomyVocabularyEnum::ResourceSubject->value;

        $response = $this->actingAs($admin)->get(route('subject_area.edit_or_create', ['vid' => $vid,'tnid' => $term->tnid]));

        $response->assertOk();
        $response->assertViewIs('admin.taxonomy.subject-area.edit');
        $response->assertViewHas('parents');
        $response->assertViewHas('terms');
        $response->assertViewHas('languages');
        $response->assertViewHas('tnid');
    }

    #[Test]
    public function subject_area_edit_or_create_route_returns_404_when_tnid_not_found(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $vid = TaxonomyVocabularyEnum::ResourceSubject->value;

        $response = $this->actingAs($admin)->get(route('subject_area.edit_or_create', ['vid' => $vid, 'tnid' => 99999]));

        $response->assertStatus(404);
    }

    #[Test]
    public function subject_area_store_or_update_route_create_success(): void
    {
        $this->refreshApplicationWithLocale('en');
        $this->modifyTaxonomyHierarchy();

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $vid = TaxonomyVocabularyEnum::ResourceSubject->value;
        $initialCount = TaxonomyTerm::where('vid', $vid)->count();

        $response = $this->actingAs($admin)->post(route('subject_area.store_or_update', ['vid' => $vid]), [
            'name' => ['en' => 'Mathematics', 'fa' => ''],
            'weight' => ['en' => 1, 'fa' => 1],
            'parent' => ['en' => 0, 'fa' => 0],
        ]);

        $response->assertRedirect(route('subject_areas.index', ['vid' => $vid]));
        $response->assertSessionHas('success');

        $this->assertGreaterThan($initialCount, TaxonomyTerm::where('vid', $vid)->count());
        $term = TaxonomyTerm::where('vid', $vid)->where('name', 'Mathematics')->first();
        $this->assertNotNull($term);
        $this->assertGreaterThan(0, $term->tnid);
    }

    #[Test]
    public function subject_area_store_or_update_route_update_success(): void
    {
        $this->refreshApplicationWithLocale('en');
        $this->modifyTaxonomyHierarchy();

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $vid = TaxonomyVocabularyEnum::ResourceSubject->value;
        $termEn = TaxonomyTerm::factory()->create([
            'vid' => $vid,
            'name' => 'Science',
            'tnid' => 0,
            'language' => 'en',
            'weight' => 1,
        ]);
        $termEn->update(['tnid' => $termEn->id]);

        $termFa = TaxonomyTerm::factory()->create([
            'vid' => $vid,
            'name' => 'علوم',
            'tnid' => $termEn->id,
            'language' => 'fa',
            'weight' => 1,
        ]);

        TaxonomyHierarchy::factory()->create(['tid' => $termEn->id, 'parent' => 0, 'aux_id' => $termEn->id]);
        TaxonomyHierarchy::factory()->create(['tid' => $termFa->id, 'parent' => 0, 'aux_id' => $termFa->id]);

        $tnid = $termEn->tnid;

        $response = $this->actingAs($admin)->post(route('subject_area.store_or_update', ['vid' => $vid]), [
            'tnid' => $tnid,
            'name' => ['en' => 'Science Updated', 'fa' => 'علوم به‌روز'],
            'weight' => ['en' => 2, 'fa' => 2],
            'parent' => ['en' => 0, 'fa' => 0],
            'id' => ['en' => $termEn->id, 'fa' => $termFa->id],
        ]);

        $response->assertRedirect(route('subject_areas.index', ['vid' => $vid]));
        $response->assertSessionHas('success');

        $termEn->refresh();
        $termFa->refresh();
        $this->assertSame('Science Updated', $termEn->name);
        $this->assertSame('علوم به‌روز', $termFa->name);
        $this->assertSame(2, $termEn->weight);
        $this->assertSame(2, $termFa->weight);
    }

    #[Test]
    public function subject_area_store_or_update_validation_fails_when_all_names_empty(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $vid = TaxonomyVocabularyEnum::ResourceSubject->value;

        $response = $this->actingAs($admin)->post(route('subject_area.store_or_update', ['vid' => $vid]), [
            'name' => ['en' => '', 'fa' => '  '],
            'weight' => ['en' => 1, 'fa' => 1],
            'parent' => ['en' => 0, 'fa' => 0],
        ]);

        $response->assertSessionHasErrors('name');
    }

    #[Test]
    public function subject_area_store_or_update_validation_fails_when_tnid_does_not_exist(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $vid = TaxonomyVocabularyEnum::ResourceSubject->value;

        $response = $this->actingAs($admin)->post(route('subject_area.store_or_update', ['vid' => $vid]), [
            'tnid' => 99999,
            'name' => ['en' => 'Some Subject'],
            'weight' => ['en' => 1],
            'parent' => ['en' => 0],
            'id' => ['en' => 99999],
        ]);

        $response->assertSessionHasErrors('tnid');
    }

    #[Test]
    public function subject_area_store_or_update_validation_fails_when_weight_not_array(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $vid = TaxonomyVocabularyEnum::ResourceSubject->value;

        $response = $this->actingAs($admin)->post(route('subject_area.store_or_update', ['vid' => $vid]), [
            'name' => ['en' => 'Mathematics'],
            'weight' => 1,
            'parent' => ['en' => 0],
        ]);

        $response->assertSessionHasErrors('weight');
    }

    private function modifyTaxonomyHierarchy()
    {
        if (DB::getDriverName() === 'sqlite') {
            // Test-only: drop then recreate table with nullable aux_id and trigger for auto value
            DB::statement('DROP TABLE IF EXISTS taxonomy_term_hierarchy');
            DB::statement('CREATE TABLE taxonomy_term_hierarchy (id TEXT NOT NULL PRIMARY KEY, tid INTEGER NOT NULL, parent INTEGER NOT NULL, aux_id INTEGER NULL)');
            DB::statement('CREATE UNIQUE INDEX taxonomy_term_hierarchy_tid_parent_unique ON taxonomy_term_hierarchy (tid, parent)');
            DB::statement('CREATE TRIGGER taxonomy_term_hierarchy_aux_id_trigger AFTER INSERT ON taxonomy_term_hierarchy WHEN NEW.aux_id IS NULL BEGIN UPDATE taxonomy_term_hierarchy SET aux_id = (SELECT COALESCE(MAX(aux_id), 0) + 1 FROM taxonomy_term_hierarchy) WHERE rowid = NEW.rowid; END');
        }
    }
}
