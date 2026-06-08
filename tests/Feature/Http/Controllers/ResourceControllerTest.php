<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\TaxonomyVocabularyEnum;
use App\Models\Resource;
use App\Models\ResourceAttachment;
use App\Models\ResourceAuthor;
use App\Models\ResourceComment;
use App\Models\ResourceCopyrightHolder;
use App\Models\ResourceEducationalResource;
use App\Models\ResourceEducationalUse;
use App\Models\ResourceFavorite;
use App\Models\ResourceFile;
use App\Models\ResourceFlag;
use App\Models\ResourceIamAuthor;
use App\Models\ResourceKeyword;
use App\Models\ResourceLearningResourceType;
use App\Models\ResourceLevel;
use App\Models\ResourcePublisher;
use App\Models\ResourceSharePermission;
use App\Models\ResourceSubjectArea;
use App\Models\ResourceTranslationRight;
use App\Models\TaxonomyTerm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ResourceController
 */
class ResourceControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function index_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $response = $this->get('en/admin/resources');

        $response->assertOk();
        $response->assertViewIs('admin.resources.resources');
        $response->assertViewHas('resources');
        $response->assertViewHas('filters');
        $response->assertViewHas('languages');
    }

    #[Test]
    public function attributes_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $resource = Resource::factory()->create();

        // Test for authors
        $response = $this->get('en/resources/attributes/authors?term=sample');
        $response->assertOk();
        $response->assertJsonStructure([
            '*' => ['id', 'name'],
        ]);

        // Test for publishers
        $response = $this->get('en/resources/attributes/publishers?term=sample');
        $response->assertOk();
        $response->assertJsonStructure([
            '*' => ['id', 'name'],
        ]);

        // Test for translators
        $response = $this->get('en/resources/attributes/translators?term=sample');
        $response->assertOk();
        $response->assertJsonStructure([
            '*' => ['id', 'name'],
        ]);

        // Test for keywords
        $response = $this->get('en/resources/attributes/keywords?term=sample');
        $response->assertOk();
        $response->assertJsonStructure([
            '*' => ['id', 'name'],
        ]);
    }

    #[Test]
    public function comment_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $this->actingAs($user);

        $resource = Resource::factory()->create();

        $response = $this->post(route('comment'), [
            'userid' => $user->id,
            'resource_id' => $resource->id,
            'comment' => 'This is a test comment.',
        ]);

        $response->assertRedirect('resource/'.$resource->id);
        $this->assertDatabaseHas('resource_comments', [
            'resource_id' => $resource->id,
            'user_id' => $user->id,
            'comment' => 'This is a test comment.',
        ]);
    }

    #[Test]
    public function create_resource_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('resource.form'));

        $response->assertOk();
        $response->assertViewIs('resources.resource_form');
    }

    #[Test]
    public function create_resource_stores_resource(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $user->roles()->attach(5);
        $this->actingAs($user);

        $resourceFile = ResourceFile::factory()->create();
        $subjectArea = TaxonomyTerm::where('vid', 8)->first();
        $learningType = TaxonomyTerm::where('vid', 7)->first();
        $educationalUse = TaxonomyTerm::where('vid', 25)->first();
        $level = TaxonomyTerm::where('vid', 13)->first();

        $response = $this->post('en/resources/save', [
            'title' => 'Test Resource',
            'author' => 'Test Author',
            'publisher' => 'Test Publisher',
            'language' => 'en',
            'abstract' => 'Test abstract content',
            'resource_file_id' => $resourceFile->id,
            'subject_areas' => [$subjectArea->id],
            'learning_resources_types' => [$learningType->id],
            'educational_use' => [$educationalUse->id],
            'level' => [$level->id],
            'resource_rights' => 'author',
            'copyright_holder' => 'Test Copyright Holder',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('resources', [
            'title' => 'Test Resource',
            'language' => 'en',
            'user_id' => $user->id,
        ]);

        $resource = Resource::where('title', 'Test Resource')->first();

        $this->assertNotNull($resource);
        $this->assertDatabaseHas('resource_authors', ['resource_id' => $resource->id]);
        $this->assertDatabaseHas('resource_subject_areas', ['resource_id' => $resource->id]);
        $this->assertDatabaseHas('resource_levels', ['resource_id' => $resource->id]);
        $this->assertDatabaseHas('resource_iam_authors', ['resource_id' => $resource->id]);
    }

    #[Test]
    public function edit_resource_returns_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $user->roles()->attach(5);
        $this->actingAs($user);

        $resource = Resource::factory()->create();

        $response = $this->get("en/resources/edit/$resource->id");

        $response->assertOk();
        $response->assertViewIs('resources.resource_form');
        $response->assertViewHas('edit', true);
        $response->assertViewHas('resource');
    }


    #[Test]
    public function edit_resource_updates_resource(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $user->roles()->attach(5);
        $this->actingAs($user);

        $resource = Resource::factory()->create();
        $resourceFile = ResourceFile::factory()->create();
        $subjectArea = TaxonomyTerm::where('vid', 8)->first();
        $learningType = TaxonomyTerm::where('vid', 7)->first();
        $educationalUse = TaxonomyTerm::where('vid', 25)->first();
        $level = TaxonomyTerm::where('vid', 13)->first();

        $response = $this->post("en/resources/save/$resource->id", [
            'title' => 'Updated Resource Title',
            'author' => 'Updated Author',
            'publisher' => 'Updated Publisher',
            'language' => 'en',
            'abstract' => 'Updated abstract content',
            'resource_file_id' => $resourceFile->id,
            'subject_areas' => [$subjectArea->id],
            'learning_resources_types' => [$learningType->id],
            'educational_use' => [$educationalUse->id],
            'level' => [$level->id],
            'resource_rights' => 'translation',
            'copyright_holder' => 'Updated Copyright Holder',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('resources', [
            'id' => $resource->id,
            'title' => 'Updated Resource Title',
            'language' => 'en',
        ]);

        $this->assertDatabaseHas('resource_translation_rights', [
            'resource_id' => $resource->id,
            'value' => 1,
        ]);

        $this->assertDatabaseHas('resource_copyright_holders', [
            'resource_id' => $resource->id,
            'value' => 'Updated Copyright Holder',
        ]);
    }

    #[Test]
    public function delete_resource_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $resource = Resource::factory()->create();

        $response = $this->get('en/admin/resource/delete/'.$resource->id);

        $response->assertRedirect();
        $this->assertEquals(0, Resource::whereId($resource->id)->count());
    }

    #[Test]
    public function download_file_aborts_with_a_404(): void
    {
        $this->refreshApplicationWithLocale('en');

        Resource::factory()->create();

        $key = encrypt(time()); // or any other value you need to encrypt

        $response = $this->get("en/resource/view/99999/{$key}");

        $response->assertNotFound();
    }

    #[Test]
    public function flag_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $this->actingAs($user);

        $resource = Resource::factory()->create();

        $response = $this->post(route('flag'), [
            'userid' => $user->id,
            'resource_id' => $resource->id,
            'type' => 3, // Spam
            'details' => 'This is a spam resource.',
        ]);

        $response->assertRedirect('resource/'.$resource->id);
        $this->assertEquals('This is a spam resource.', ResourceFlag::where('resource_id', $resource->id)->value('details'));
    }

    #[Test]
    public function list_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('resourceList'));

        $response->assertOk();
        $response->assertViewIs('resources.resources_list');
        $response->assertViewHas('resources');
    }

    #[Test]
    public function post_step_one_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('en/resources/add/step1', [
            'title' => 'Resource Title',
            'author' => 'Author Name',
            'publisher' => 'Publisher Name',
            'translator' => 'Translator Name',
            'language' => 'en',
            'abstract' => 'This is an abstract.',
        ]);

        $response->assertRedirect('/resources/add/step2');
    }

    #[Test]
    public function translator_field_is_required_when_has_translator_is_checked(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('en/resources/add/step1', [
            'title' => 'Resource Title',
            'author' => 'Author Name',
            'publisher' => 'Publisher Name',
            'has_translator' => 1,
            'translator' => '',
            'language' => 'en',
            'abstract' => 'This is an abstract.',
        ]);

        // Assert: Check that validation fails
        $response->assertSessionHasErrors('translator');
    }

    #[Test]
    public function translator_field_is_nullable_when_has_translator_is_not_checked(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('en/resources/add/step1', [
            'title' => 'Resource Title',
            'author' => 'Author Name',
            'publisher' => 'Publisher Name',
            'has_translator' => 0,
            'translator' => '',
            'language' => 'en',
            'abstract' => 'This is an abstract.',
        ]);

        // Assert: Check there are no validation errors
        $response->assertSessionDoesntHaveErrors('translator');
    }

    #[Test]
    public function at_least_one_of_author_or_publisher_is_required(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('en/resources/add/step1', [
            'title' => 'Resource Title',
            'author' => null,
            'publisher' => null,
            'has_translator' => 1,
            'translator' => 'Translator',
            'language' => 'en',
            'abstract' => 'This is an abstract.',
        ]);

        $response->assertSessionHasErrors(['publisher']);
    }

    #[Test]
    public function post_step_one_edit_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $resource = Resource::factory()->create();

        $response = $this->post('en/resources/edit/step1/'.$resource->id, [
            'title' => 'Updated Resource',
            'author' => 'Updated Author',
            'publisher' => 'Updated Publisher',
            'translator' => 'Updated Translator',
            'language' => 'en',
            'abstract' => 'Updated abstract.',
        ]);

        $response->assertRedirect('/resources/edit/step2/'.$resource->id);
    }

    #[Test]
    public function post_step_three_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $resource = Resource::factory()->create();
        $taxonomyTerm = TaxonomyTerm::factory()->create();

        $step1 = [
            'title' => 'nice',
            'author' => 'wow',
            'publisher' => 'wow',
            'translator' => 'great',
            'language' => 'en',
            'abstract' => '<p>abstract</p>',
        ];

        $step2 = [
            'subject_areas' => [],
            'keywords' => 'keyword',
            'learning_resources_types' => [],
            'educational_use' => [],
            'level' => [],
        ];

        Session::put('new_resource_step_1', $step1);
        Session::put('new_resource_step_2', $step2);

        $response = $this->post('en/resources/add/step3', [
            'translation_rights' => 1,
            'educational_resource' => 1,
            'copyright_holder' => null,
        ]);

        $response->assertRedirect('/home');
    }

    #[Test]
    public function post_step_three_edit_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $resource = Resource::factory()->create();
        $taxonomyTerm = TaxonomyTerm::factory()->create();

        $step1 = [
            'title' => 'updated title',
            'author' => 'updated wow',
            'publisher' => 'updated wow',
            'translator' => 'updated great',
            'language' => 'en',
            'abstract' => '<p>updated abstract</p>',
        ];

        $step2 = [
            'subject_areas' => [],
            'keywords' => 'keyword',
            'learning_resources_types' => [],
            'educational_use' => [],
            'level' => [],
        ];

        Session::put('edit_resource_step_1', $step1);
        Session::put('edit_resource_step_2', $step2);

        $resource = Resource::factory()->create();
        $taxonomyTerm = TaxonomyTerm::factory()->create();

        $response = $this->post('en/resources/edit/step3/'.$resource->id, [
            'translation_rights' => 1,
            'educational_resource' => 1,
            'copyright_holder' => null,
        ]);

        $response->assertRedirect('/resource/'.$resource->id);

        $this->assertEquals('updated title', Resource::whereId($resource->id)->value('title'));
    }

    #[Test]
    public function post_step_two_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $step1 = [
            'title' => 'nice',
            'author' => 'wow',
            'publisher' => 'wow',
            'translator' => 'great',
            'language' => 'en',
            'abstract' => '<p>abstract</p>',
        ];

        Session::put('resource1', $step1);

        // learning_resources_types with vid 7
        TaxonomyTerm::factory()->create(['vid' => 7, 'name' => 'Book']);
        TaxonomyTerm::factory()->create(['vid' => 7, 'name' => 'Media']);

        // subject_areas with vid 8
        TaxonomyTerm::factory()->create(['vid' => 8, 'name' => 'Computer']);
        TaxonomyTerm::factory()->create(['vid' => 8, 'name' => 'History']);

        // educational_use with vid 25
        TaxonomyTerm::factory()->create(['vid' => 25, 'name' => 'Information Education']);
        TaxonomyTerm::factory()->create(['vid' => 25, 'name' => 'Professional Development']);

        // level with vid 13
        TaxonomyTerm::factory()->create(['vid' => 13, 'name' => 'Preschool']);
        TaxonomyTerm::factory()->create(['vid' => 13, 'name' => 'Literacy']);

        $response = $this->post('en/resources/add/step2', [
            'subject_areas' => TaxonomyTerm::where('vid', 8)->pluck('id'),
            'keywords' => 'keyword',
            'learning_resources_types' => TaxonomyTerm::where('vid', 7)->pluck('id'),
            'educational_use' => TaxonomyTerm::where('vid', 25)->pluck('id'),
            'level' => TaxonomyTerm::where('vid', 13)->pluck('id'),
        ]);

        $response->assertRedirect('/resources/add/step3');
    }

    #[Test]
    public function post_step_two_edit_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $step1 = [
            'title' => 'nice',
            'author' => 'wow',
            'publisher' => 'wow',
            'translator' => 'great',
            'language' => 'en',
            'abstract' => '<p>abstract</p>',
        ];

        Session::put('resource1', $step1);

        // learning_resources_types with vid 7
        TaxonomyTerm::factory()->create(['vid' => 7, 'name' => 'Learning resource type']);

        // subject_areas with vid 8
        TaxonomyTerm::factory()->create(['vid' => 8, 'name' => 'Subject area']);

        // educational_use with vid 25
        TaxonomyTerm::factory()->create(['vid' => 25, 'name' => 'Educatinal use']);

        // level with vid 13
        TaxonomyTerm::factory()->create(['vid' => 13, 'name' => 'Level']);

        $resource = Resource::factory()->create();

        $response = $this->post("en/resources/edit/step2/$resource->id", [
            'subject_areas' => TaxonomyTerm::where('vid', 8)->orderBy('id', 'desc')->take(1)->pluck('id'),
            'keywords' => 'keyword',
            'learning_resources_types' => TaxonomyTerm::where('vid', 7)->orderBy('id', 'desc')->take(1)->pluck('id'),
            'educational_use' => TaxonomyTerm::where('vid', 25)->orderBy('id', 'desc')->take(1)->pluck('id'),
            'level' => TaxonomyTerm::where('vid', 13)->orderBy('id', 'desc')->take(1)->pluck('id'),
        ]);

        $response->assertRedirect('/resources/edit/step3/'.$resource->id);
    }

    #[Test]
    public function view_file_aborts_with_a_404(): void
    {
        $this->refreshApplicationWithLocale('en');

        Resource::factory()->create();

        $key = encrypt(time());

        $response = $this->get("en/resource/view/789/$key");

        $response->assertNotFound();
    }

    #[Test]
    public function published_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5); // Ensure this is the correct role for admin access
        $this->actingAs($admin);

        // Create a resource
        $resource = Resource::factory()->create(['status' => 0]); // Start with an unpublished status

        // Make the request to the published route
        $response = $this->get('en/admin/resource/published/'.$resource->id);

        // Assert that the response is a redirect (302)
        $response->assertRedirect();

        // Verify the resource's status was updated correctly
        $resource->refresh(); // Refresh the resource model to get the latest data
        $this->assertEquals(1, $resource->status);
    }

    #[Test]
    public function resource_favorite_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $this->actingAs($user);

        $resource = Resource::factory()->create();

        $response = $this->post('resources/favorite', [
            'resourceId' => $resource->id,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('resource_favorites', [
            'resource_id' => $resource->id,
            'user_id' => $user->id, // Check for the authenticated user
        ]);
    }

    #[Test]
    public function resource_favorite_returns_not_logged_in_if_user_is_not_authenticated(): void
    {
        $this->refreshApplicationWithLocale('en');

        $resource = Resource::factory()->create();

        $response = $this->post('resources/favorite', [
            'resourceId' => $resource->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['status' => 'notloggedin']);
    }

    #[Test]
    public function resource_favorite_adds_favorite_when_it_does_not_exist(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $secondUser = User::factory()->create();
        $resource = Resource::factory()->create();

        // Insert existing favorite for another user
        ResourceFavorite::create([
            'resource_id' => $resource->id,
            'user_id' => $secondUser->id,
        ]);

        $this->actingAs($user);
        $response = $this->post('resources/favorite', [
            'resourceId' => $resource->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['action' => 'added', 'favorite_count' => 2]);

        $this->assertDatabaseHas('resource_favorites', [
            'resource_id' => $resource->id,
            'user_id' => $user->id, // Ensure it's added for the authenticated user
        ]);
    }

    #[Test]
    public function deletes_resource_favorite_when_it_exists(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $resource = Resource::factory()->create();

        // Create a favorite for the user
        ResourceFavorite::create([
            'resource_id' => $resource->id,
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);
        $response = $this->post('resources/favorite', [
            'resourceId' => $resource->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['action' => 'deleted', 'favorite_count' => 0]);

        $this->assertDatabaseMissing('resource_favorites', [
            'resource_id' => $resource->id,
            'user_id' => $user->id, // Ensure it's deleted for the authenticated user
        ]);
    }

    #[Test]
    public function update_tid_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        // Create resources
        $resource = Resource::factory()->create(['primary_tnid' => false]);
        $resource->tnid = $resource->id;
        $resource->save();

        $translatedResource = Resource::factory()->create(['primary_tnid' => false]);
        $translatedResource->tnid = $translatedResource->id;
        $translatedResource->save();

        // Make the POST request to update the tnid
        $response = $this->post(route('updatetid', ['resourceId' => $resource->id]), [
            'link' => $translatedResource->id,
        ]);

        $response->assertRedirect();

        // Assert that the tnid has been updated correctly
        $this->assertDatabaseHas('resources', [
            'id' => $resource->id,
            'tnid' => $resource->id,
        ]);
    }

    #[Test]
    public function view_public_resource_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $resource = Resource::factory()->create();
        $resourceComments = ResourceComment::factory()
            ->times(3)
            ->create(['resource_id' => $resource->id]);

        $response = $this->get('en/resource/'.$resource->id);

        $response->assertOk();
        $response->assertViewIs('resources.resources_view');
        $response->assertViewHas('resource', $resource);
        $response->assertViewHas('relatedItems');
        $response->assertViewHas('comments', $resourceComments);
        $response->assertViewHas('translations');
    }

    #[Test]
    public function view_public_resource_aborts_with_a_403(): void
    {
        $this->refreshApplicationWithLocale('en');

        $resource = Resource::factory()->create(['status' => 0]); // Assuming status 0 means unpublished

        $response = $this->get('en/resource/'.$resource->id);

        $response->assertForbidden();
    }

    // Step One
    #[Test]
    public function edit_resource_form_displays_existing_resource_data(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $user->roles()->attach(5);
        $this->actingAs($user);

        $resource = Resource::factory()->create([
            'title' => 'Existing Resource Title',
            'abstract' => 'Existing abstract content',
            'language' => 'en',
        ]);

        // Create related data
        $subjectArea = TaxonomyTerm::where('vid', 8)->first();
        $learningType = TaxonomyTerm::where('vid', 7)->first();
        $educationalUse = TaxonomyTerm::where('vid', 25)->first();
        $level = TaxonomyTerm::where('vid', 13)->first();
        $author = TaxonomyTerm::where('vid', 24)->first();
        $publisher = TaxonomyTerm::where('vid', 9)->first();

        // Attach relationships
        ResourceSubjectArea::create(['resource_id' => $resource->id, 'tid' => $subjectArea->id]);
        ResourceLearningResourceType::create(['resource_id' => $resource->id, 'tid' => $learningType->id]);
        ResourceEducationalUse::create(['resource_id' => $resource->id, 'tid' => $educationalUse->id]);
        ResourceLevel::create(['resource_id' => $resource->id, 'tid' => $level->id]);
        ResourceAuthor::create(['resource_id' => $resource->id, 'tid' => $author->id]);
        ResourcePublisher::create(['resource_id' => $resource->id, 'tid' => $publisher->id]);
        ResourceIamAuthor::create(['resource_id' => $resource->id, 'value' => 1]);
        ResourceCopyrightHolder::create(['resource_id' => $resource->id, 'value' => 'Existing Copyright Holder']);

        $response = $this->get("en/resources/edit/$resource->id");

        $response->assertOk();
        $response->assertViewIs('resources.resource_form');

        // Assert view data
        $response->assertViewHas('edit', true);
        $response->assertViewHas('resource', fn($r) => $r->id === $resource->id);
        $response->assertViewHas('resourceSubjectAreas', fn($areas) => in_array($subjectArea->id, $areas));
        $response->assertViewHas('resourceLearningResourceTypes', fn($types) => in_array($learningType->id, $types));
        $response->assertViewHas('editEducationalUse', fn($uses) => in_array($educationalUse->id, $uses));
        $response->assertViewHas('resourceLevels', fn($levels) => in_array($level->id, $levels));
        $response->assertViewHas('resourceRights', 'author');
        $response->assertViewHas('resourceCopyrightHolder', 'Existing Copyright Holder');

        // Assert response contains resource data
        $response->assertSee('Existing Resource Title');
        $response->assertSee('Existing abstract content');
    }

    #[Test]
    public function create_resource_fails_without_required_fields(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $user->roles()->attach(5);
        $this->actingAs($user);

        $response = $this->post('en/resources/save', []);

        $response->assertSessionHasErrors([
            'title',
            'language',
            'resource_file_id',
            'abstract',
            'subject_areas',
            'learning_resources_types',
            'educational_use',
            'level',
        ]);
    }

    #[Test]
    public function create_resource_fails_with_invalid_language(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $user->roles()->attach(5);
        $this->actingAs($user);

        $response = $this->post('en/resources/save', [
            'language' => str_repeat('a', 11), // exceeds max:10
        ]);

        $response->assertSessionHasErrors(['language']);
    }

    #[Test]
    public function create_resource_fails_with_invalid_resource_file_id(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $user->roles()->attach(5);
        $this->actingAs($user);

        $response = $this->post('en/resources/save', [
            'resource_file_id' => 99999, // does not exist
        ]);

        $response->assertSessionHasErrors(['resource_file_id']);
    }

    #[Test]
    public function create_resource_fails_with_invalid_subject_areas(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $user->roles()->attach(5);
        $this->actingAs($user);

        $response = $this->post('en/resources/save', [
            'subject_areas' => [99999], // does not exist
        ]);

        $response->assertSessionHasErrors(['subject_areas.0']);
    }

    #[Test]
    public function create_resource_fails_with_invalid_learning_resources_types(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $user->roles()->attach(5);
        $this->actingAs($user);

        $response = $this->post('en/resources/save', [
            'learning_resources_types' => [99999],
        ]);

        $response->assertSessionHasErrors(['learning_resources_types.0']);
    }

    #[Test]
    public function create_resource_fails_with_invalid_educational_use(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $user->roles()->attach(5);
        $this->actingAs($user);

        $response = $this->post('en/resources/save', [
            'educational_use' => [99999],
        ]);

        $response->assertSessionHasErrors(['educational_use.0']);
    }

    #[Test]
    public function create_resource_fails_with_invalid_level(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $user->roles()->attach(5);
        $this->actingAs($user);

        $response = $this->post('en/resources/save', [
            'level' => [99999],
        ]);

        $response->assertSessionHasErrors(['level.0']);
    }

    #[Test]
    public function create_resource_fails_with_invalid_resource_rights(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $user->roles()->attach(5);
        $this->actingAs($user);

        $response = $this->post('en/resources/save', [
            'resource_rights' => 'invalid_value',
        ]);

        $response->assertSessionHasErrors(['resource_rights']);
    }

    #[Test]
    public function create_resource_fails_with_invalid_creative_commons(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $user->roles()->attach(5);
        $this->actingAs($user);

        $response = $this->post('en/resources/save', [
            'creative_commons' => 99999,
        ]);

        $response->assertSessionHasErrors(['creative_commons']);
    }

    #[Test]
    public function create_resource_fails_with_invalid_attachment_type(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $user->roles()->attach(5);
        $this->actingAs($user);

        $response = $this->post('en/resources/save', [
            'attachments' => [
                UploadedFile::fake()->create('test.exe', 100),
            ],
        ]);

        $response->assertSessionHasErrors(['attachments.0']);
    }

    #[Test]
    public function create_resource_fails_with_attachment_too_large(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $user->roles()->attach(5);
        $this->actingAs($user);

        $response = $this->post('en/resources/save', [
            'attachments' => [
                UploadedFile::fake()->create('test.pdf', 131073), // exceeds 128MB
            ],
        ]);

        $response->assertSessionHasErrors(['attachments.0']);
    }

    #[Test]
    public function create_resource_requires_translator_when_has_translator_is_set(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $user->roles()->attach(5);
        $this->actingAs($user);

        $response = $this->post('en/resources/save', [
            'has_translator' => 1,
            'translator' => null,
        ]);

        $response->assertSessionHasErrors(['translator']);
    }

    #[Test]
    public function create_resource_fails_with_title_too_long(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $user->roles()->attach(5);
        $this->actingAs($user);

        $response = $this->post('en/resources/save', [
            'title' => str_repeat('a', 256), // exceeds max:255
        ]);

        $response->assertSessionHasErrors(['title']);
    }
}
