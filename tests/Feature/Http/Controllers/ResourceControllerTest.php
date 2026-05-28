<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\TaxonomyVocabularyEnum;
use App\Models\Resource;
use App\Models\ResourceAttachment;
use App\Models\ResourceComment;
use App\Models\ResourceCopyrightHolder;
use App\Models\ResourceEducationalResource;
use App\Models\ResourceEducationalUse;
use App\Models\ResourceFavorite;
use App\Models\ResourceFile;
use App\Models\ResourceFlag;
use App\Models\ResourceIamAuthor;
use App\Models\ResourceKeyword;
use App\Models\ResourceSharePermission;
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
    public function create_step_one_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('step1'));

        $response->assertOk();
        $response->assertViewIs('resources.resources_modify_step1');
        $response->assertViewHas('resource');
    }

    #[Test]
    public function create_step_one_edit_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $resource = Resource::factory()->create();

        $response = $this->get(route('edit1', ['resourceId' => $resource->id]));

        $response->assertOk();
        $response->assertViewIs('resources.resources_modify_step1');
    }

    #[Test]
    public function create_step_three_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('step3'));

        $response->assertRedirect('/resources/add/step1');
    }

    #[Test]
    public function create_step_three_edit_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        // Create a resource
        $resource = Resource::factory()->create();

        // Prepare resource1 data
        $resource1 = [
            'title' => $resource->title,
            'author' => 'Author',
            'publisher' => 'Publisher',
            'translator' => 'Translator',
            'language' => $resource->language,
            'abstract' => $resource->abstract,
            'status' => 1,
        ];

        Session::put('edit_resource_step_1', $resource1);
        Session::put('edit_resource_step_2', [
            'subject_areas' => [],
            'learning_resources_types' => [],
            'keywords' => '',
            'educational_use' => [],
            'level' => [],
            'attc' => [],
        ]);

        $response = $this->get(route('edit3', ['resourceId' => $resource->id]));

        $response->assertOk();

        $response->assertViewIs('resources.resources_modify_step3');

        $response->assertViewHas('resource');
    }

    #[Test]
    public function create_step_two_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $response = $this->get(route('step2'));

        $response->assertRedirect('/resources/add/step1');
    }

    #[Test]
    public function create_step_two_edit_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        $user->roles()->attach(5);
        $this->actingAs($user);

        $resource = Resource::factory()->create();

        $resource1 = [
            'title' => $resource->title,
            'author' => 'Author',
            'publisher' => 'Publisher',
            'translator' => 'Translator',
            'language' => $resource->language,
            'abstract' => $resource->abstract,
            'status' => 1,
        ];

        Session::put('edit_resource_step_1', $resource1);

        $response = $this->get("en/resources/edit/step2/$resource->id");

        $response->assertOk();
        $response->assertViewIs('resources.resources_modify_step2');
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
    public function edit_step_one_get_displays_correct_resource_values_from_database(): void
    {
        $this->refreshApplicationWithLocale('en');
        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $author1 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceAuthor->value, 'name' => 'Author One']);
        $author2 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceAuthor->value, 'name' => 'Author Two']);
        $publisher = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourcePublisher->value, 'name' => 'Publisher']);
        $translator1 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceTranslator->value, 'name' => 'Translator One']);
        $translator2 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceTranslator->value, 'name' => 'Translator Two']);
        $resourceFile = ResourceFile::create(['name' => 'test-file.pdf', 'label' => 'Test File', 'language' => 'en', 'resource_id' => null]);

        $resource = Resource::factory()->create([
            'title' => 'Original Title',
            'abstract' => 'Original Abstract',
            'language' => 'en',
            'resource_file_id' => $resourceFile->id,
        ]);
        $resource->authors()->attach([$author1->id, $author2->id]);
        $resource->publishers()->attach($publisher->id);
        $resource->translators()->attach([$translator1->id, $translator2->id]);
        $resourceFile->update(['resource_id' => $resource->id]);

        $response = $this->get(route('edit1', ['resourceId' => $resource->id]));
        $response->assertOk();
        $response->assertViewIs('resources.resources_modify_step1');
        $response->assertViewHas('resource');

        $viewResource = $response->viewData('resource');
        $this->assertEquals('Original Title', $viewResource->title);
        $this->assertEquals('Original Abstract', $viewResource->abstract);
        $this->assertEquals('en', $viewResource->language);
        $this->assertEquals($resourceFile->id, $viewResource->resource_file_id);
        $this->assertTrue($viewResource->authors->contains($author1->id) && $viewResource->authors->contains($author2->id));
        $this->assertCount(2, $viewResource->authors);
        $this->assertContains('Author One', $viewResource->authors->pluck('name')->toArray());
        $this->assertContains('Author Two', $viewResource->authors->pluck('name')->toArray());
        $this->assertTrue($viewResource->publishers->contains($publisher->id));
        $this->assertContains('Publisher', $viewResource->publishers->pluck('name')->toArray());
        $this->assertTrue($viewResource->translators->contains($translator1->id) && $viewResource->translators->contains($translator2->id));
        $this->assertCount(2, $viewResource->translators);
        $this->assertContains('Translator One', $viewResource->translators->pluck('name')->toArray());
        $this->assertContains('Translator Two', $viewResource->translators->pluck('name')->toArray());
        $this->assertFalse($viewResource->translators->isEmpty(), 'This is a work of translation checkbox should be checked');
        $this->assertNotNull($viewResource->resourceFile);
        $this->assertEquals($resourceFile->id, $viewResource->resourceFile->id);
    }

    #[Test]
    public function edit_step_one_post_stores_data_in_session_not_database(): void
    {
        $this->refreshApplicationWithLocale('en');
        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $oldAuthor1 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceAuthor->value, 'name' => 'Old Author One']);
        $oldAuthor2 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceAuthor->value, 'name' => 'Old Author Two']);
        $oldPublisher = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourcePublisher->value, 'name' => 'Old Publisher']);
        $oldTranslator = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceTranslator->value, 'name' => 'Old Translator']);
        $oldResourceFile = ResourceFile::create(['name' => 'old-file.pdf', 'label' => 'Old File', 'language' => 'en', 'resource_id' => null]);

        $resource = Resource::factory()->create([
            'title' => 'Original Title',
            'abstract' => 'Original Abstract',
            'language' => 'en',
            'resource_file_id' => $oldResourceFile->id,
        ]);
        $resource->authors()->attach([$oldAuthor1->id, $oldAuthor2->id]);
        $resource->publishers()->attach($oldPublisher->id);
        $resource->translators()->attach($oldTranslator->id);
        $oldResourceFile->update(['resource_id' => $resource->id]);

        $newResourceFile = ResourceFile::create(['name' => 'new-file.pdf', 'label' => 'New File', 'language' => 'en', 'resource_id' => null]);
        $newValues = [
            'title' => 'Updated Title',
            'abstract' => 'Updated Abstract',
            'language' => 'fa',
            'author' => 'New Author',
            'publisher' => 'New Publisher',
            'has_translator' => 1,
            'translator' => 'New Translator',
            'resource_file_id' => $newResourceFile->id,
        ];
        $response = $this->post(route('edit1', ['resourceId' => $resource->id]), $newValues);
        $response->assertRedirect('/resources/edit/step2/'.$resource->id);

        // Verify data is stored in session
        $sessionData = Session::get('edit_resource_step_1');
        $this->assertNotNull($sessionData);
        $this->assertEquals('Updated Title', $sessionData['title']);
        $this->assertEquals('Updated Abstract', $sessionData['abstract']);
        $this->assertEquals('fa', $sessionData['language']);
        $this->assertEquals($resource->id, $sessionData['id']);

        // Verify data is NOT saved to database (original values should remain)
        $resource->refresh();
        $this->assertEquals('Original Title', $resource->title);
        $this->assertEquals('Original Abstract', $resource->abstract);
        $this->assertEquals('en', $resource->language);
        $this->assertEquals($oldResourceFile->id, $resource->resource_file_id);
        $this->assertTrue($resource->authors->contains($oldAuthor1->id) && $resource->authors->contains($oldAuthor2->id));
        $this->assertTrue($resource->publishers->contains($oldPublisher->id));
        $this->assertTrue($resource->translators->contains($oldTranslator->id));
    }

    #[Test]
    public function edit_step_one_get_displays_correctly_when_no_author_only_publisher(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $publisherTerm = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourcePublisher->value, 'name' => 'Only Publisher']);
        $resource = Resource::factory()->create([
            'title' => 'Resource Without Author',
            'abstract' => 'Abstract',
            'language' => 'en',
        ]);
        $resource->publishers()->attach($publisherTerm->id);

        $response = $this->get(route('edit1', ['resourceId' => $resource->id]));

        $response->assertOk();
        $response->assertViewIs('resources.resources_modify_step1');
        $viewResource = $response->viewData('resource');
        $this->assertEquals(0, $viewResource->authors->count());
        $this->assertEquals(1, $viewResource->publishers->count());
        $this->assertTrue($viewResource->publishers->contains($publisherTerm->id));
    }

    #[Test]
    public function edit_step_one_get_displays_correctly_when_no_publisher_only_author(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $authorTerm = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceAuthor->value, 'name' => 'Only Author']);
        $resource = Resource::factory()->create([
            'title' => 'Resource Without Publisher',
            'abstract' => 'Abstract',
            'language' => 'en',
        ]);
        $resource->authors()->attach($authorTerm->id);

        $response = $this->get(route('edit1', ['resourceId' => $resource->id]));

        $response->assertOk();
        $response->assertViewIs('resources.resources_modify_step1');
        $viewResource = $response->viewData('resource');
        $this->assertEquals(1, $viewResource->authors->count());
        $this->assertTrue($viewResource->authors->contains($authorTerm->id));
        $this->assertEquals(0, $viewResource->publishers->count());
    }

    #[Test]
    public function edit_step_one_get_displays_correctly_when_no_translator(): void
    {
        $this->refreshApplicationWithLocale('en');
        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $authorTerm = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceAuthor->value, 'name' => 'Author']);
        $resource = Resource::factory()->create([
            'title' => 'Resource Without Translator',
            'abstract' => 'Abstract',
            'language' => 'en',
        ]);
        $resource->authors()->attach($authorTerm->id);
        $resource->translators()->detach();

        $response = $this->get(route('edit1', ['resourceId' => $resource->id]));

        $response->assertOk();
        $response->assertViewIs('resources.resources_modify_step1');
        $viewResource = $response->viewData('resource');
        $this->assertEquals(0, $viewResource->translators->count());
        $this->assertTrue($viewResource->translators->isEmpty(), 'This is a work of translation checkbox should be unchecked when no translator');
    }

    #[Test]
    public function edit_step_one_get_displays_correctly_when_no_resource_file(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $authorTerm = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceAuthor->value, 'name' => 'Author']);
        $resource = Resource::factory()->create([
            'title' => 'Resource Without File',
            'abstract' => 'Abstract',
            'language' => 'en',
            'resource_file_id' => null,
        ]);
        $resource->authors()->attach($authorTerm->id);

        $response = $this->get(route('edit1', ['resourceId' => $resource->id]));

        $response->assertOk();
        $response->assertViewIs('resources.resources_modify_step1');
        $viewResource = $response->viewData('resource');
        $this->assertNull($viewResource->resource_file_id);
        $this->assertNull($viewResource->resourceFile);
    }

    #[Test]
    public function edit_step_one_post_accepts_when_no_author_only_publisher(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $resource = Resource::factory()->create();

        $response = $this->post(route('edit1', ['resourceId' => $resource->id]), [
            'title' => 'Title',
            'abstract' => 'Abstract',
            'language' => 'en',
            'author' => '',
            'publisher' => 'Publisher Only',
            'has_translator' => 0,
            'translator' => '',
            'resource_file_id' => null,
        ]);

        $response->assertRedirect('/resources/edit/step2/'.$resource->id);
        $sessionData = Session::get('edit_resource_step_1');
        $this->assertEquals('Publisher Only', $sessionData['publisher']);
        $this->assertEmpty($sessionData['author'] ?? '');
    }

    #[Test]
    public function edit_step_one_post_accepts_when_no_publisher_only_author(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $resource = Resource::factory()->create();

        $response = $this->post(route('edit1', ['resourceId' => $resource->id]), [
            'title' => 'Title',
            'abstract' => 'Abstract',
            'language' => 'en',
            'author' => 'Author Only',
            'publisher' => '',
            'has_translator' => 0,
            'translator' => '',
            'resource_file_id' => null,
        ]);

        $response->assertRedirect('/resources/edit/step2/'.$resource->id);
        $sessionData = Session::get('edit_resource_step_1');
        $this->assertEquals('Author Only', $sessionData['author']);
    }

    #[Test]
    public function edit_step_one_post_accepts_when_no_translator(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $resource = Resource::factory()->create();

        $response = $this->post(route('edit1', ['resourceId' => $resource->id]), [
            'title' => 'Title',
            'abstract' => 'Abstract',
            'language' => 'en',
            'author' => 'Author',
            'publisher' => 'Publisher',
            'has_translator' => 0,
            'translator' => '',
            'resource_file_id' => null,
        ]);

        $response->assertRedirect('/resources/edit/step2/'.$resource->id);
        $sessionData = Session::get('edit_resource_step_1');
        $this->assertNull($sessionData['translator'] ?? null);
    }

    #[Test]
    public function edit_step_one_post_accepts_when_no_resource_file(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $resource = Resource::factory()->create();

        $response = $this->post(route('edit1', ['resourceId' => $resource->id]), [
            'title' => 'Title',
            'abstract' => 'Abstract',
            'language' => 'en',
            'author' => 'Author',
            'publisher' => 'Publisher',
            'has_translator' => 0,
            'translator' => '',
            'resource_file_id' => null,
        ]);

        $response->assertRedirect('/resources/edit/step2/'.$resource->id);
        $sessionData = Session::get('edit_resource_step_1');
        $this->assertArrayHasKey('resource_file_id', $sessionData);
        $this->assertTrue($sessionData['resource_file_id'] === '' || $sessionData['resource_file_id'] === null);
    }

    #[Test]
    public function edit_step_one_post_fails_when_both_author_and_publisher_empty(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $resource = Resource::factory()->create();

        $response = $this->post(route('edit1', ['resourceId' => $resource->id]), [
            'title' => 'Title',
            'abstract' => 'Abstract',
            'language' => 'en',
            'author' => '',
            'publisher' => '',
            'has_translator' => 0,
            'translator' => '',
            'resource_file_id' => null,
        ]);

        $response->assertSessionHasErrors('publisher');
    }

    // Step 2
    #[Test]
    public function edit_step_two_get_displays_correct_resource_values_from_database(): void
    {
        $this->refreshApplicationWithLocale('en');
        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $subjectArea1 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceSubject->value, 'name' => 'Mathematics']);
        $subjectArea2 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceSubject->value, 'name' => 'Physics']);
        $learningType1 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceType->value, 'name' => 'Book']);
        $learningType2 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceType->value, 'name' => 'Video']);
        $educationalUse1 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::EducationalUse->value, 'name' => 'Assignment']);
        $educationalUse2 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::EducationalUse->value, 'name' => 'Lecture']);
        $level1 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceLevels->value, 'name' => 'High School']);
        $level2 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceLevels->value, 'name' => 'University']);
        $keywordTerm1 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::Keywords->value, 'name' => 'algebra']);
        $keywordTerm2 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::Keywords->value, 'name' => 'geometry']);

        $resource = Resource::factory()->create();
        $resource->subjects()->attach([$subjectArea1->id, $subjectArea2->id]);
        $resource->LearningResourceTypes()->attach([$learningType1->id, $learningType2->id]);
        $resource->levels()->attach([$level1->id, $level2->id]);
        ResourceKeyword::factory()->create(['resource_id' => $resource->id, 'tid' => $keywordTerm1->id]);
        ResourceKeyword::factory()->create(['resource_id' => $resource->id, 'tid' => $keywordTerm2->id]);
        ResourceEducationalUse::factory()->create(['resource_id' => $resource->id, 'tid' => $educationalUse1->id]);
        ResourceEducationalUse::factory()->create(['resource_id' => $resource->id, 'tid' => $educationalUse2->id]);
        ResourceAttachment::factory()->create([
            'resource_id' => $resource->id,
            'file_name' => 'test-attachment.pdf',
            'file_size' => 1024,
            'file_mime' => 'application/pdf',
        ]);

        Session::put('edit_resource_step_1', [
            'id' => $resource->id,
            'title' => 'Step 1 Title',
            'author' => 'Author',
            'publisher' => 'Publisher',
            'language' => 'en',
            'abstract' => 'Abstract',
        ]);

        $response = $this->get("en/resources/edit/step2/{$resource->id}");

        $response->assertOk();
        $response->assertViewIs('resources.resources_modify_step2');
        $response->assertViewHas('resource');
        $response->assertViewHas('edit', true);
        $response->assertViewHas('resourceSubjectAreas');
        $response->assertViewHas('resourceLearningResourceTypes');
        $response->assertViewHas('editEducationalUse');
        $response->assertViewHas('resourceLevels');
        $response->assertViewHas('resourceKeywords');
        $response->assertViewHas('resourceAttachments');
        $response->assertViewHas('subjects');
        $response->assertViewHas('learningResourceTypes');
        $response->assertViewHas('educationalUse');
        $response->assertViewHas('levels');

        $resourceArr = $response->viewData('resource');
        $this->assertEquals($resource->id, $resourceArr['id']);

        $resourceSubjectAreas = $response->viewData('resourceSubjectAreas');
        $this->assertContains($subjectArea1->id, $resourceSubjectAreas);
        $this->assertContains($subjectArea2->id, $resourceSubjectAreas);
        $this->assertCount(2, $resourceSubjectAreas);

        $resourceLearningResourceTypes = $response->viewData('resourceLearningResourceTypes');
        $this->assertContains($learningType1->id, $resourceLearningResourceTypes);
        $this->assertContains($learningType2->id, $resourceLearningResourceTypes);
        $this->assertCount(2, $resourceLearningResourceTypes);

        $editEducationalUse = $response->viewData('editEducationalUse');
        $this->assertContains($educationalUse1->id, $editEducationalUse);
        $this->assertContains($educationalUse2->id, $editEducationalUse);
        $this->assertCount(2, $editEducationalUse);

        $resourceLevels = $response->viewData('resourceLevels');
        $this->assertContains($level1->id, $resourceLevels);
        $this->assertContains($level2->id, $resourceLevels);
        $this->assertCount(2, $resourceLevels);

        $resourceKeywords = $response->viewData('resourceKeywords');
        $this->assertStringContainsString('algebra', $resourceKeywords);
        $this->assertStringContainsString('geometry', $resourceKeywords);

        $resourceAttachments = $response->viewData('resourceAttachments');
        $this->assertCount(1, $resourceAttachments);
        $this->assertArrayHasKey('file_name', $resourceAttachments[0]);
        $this->assertArrayHasKey('file_size', $resourceAttachments[0]);
        $this->assertArrayHasKey('file_mime', $resourceAttachments[0]);
        $this->assertEquals('test-attachment.pdf', $resourceAttachments[0]['file_name']);
        $this->assertEquals(1024, $resourceAttachments[0]['file_size']);
        $this->assertEquals('application/pdf', $resourceAttachments[0]['file_mime']);
    }

    #[Test]
    public function edit_step_two_post_stores_data_in_session_not_database(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        // Create taxonomy terms
        $subjectArea = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceSubject->value, 'name' => 'Mathematics']);
        $learningResourceType = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceType->value, 'name' => 'Book']);
        $educationalUse = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::EducationalUse->value, 'name' => 'Assignment']);
        $level = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceLevels->value, 'name' => 'High School']);

        // Create resource in database
        $resource = Resource::factory()->create();

        // Store original values in database
        $resource->subjects()->attach($subjectArea->id);
        $originalSubjectCount = $resource->subjects()->count();

        // Set up step 1 in session
        Session::put('edit_resource_step_1', [
            'id' => $resource->id,
            'title' => 'Step 1 Title',
            'author' => 'Author',
            'publisher' => 'Publisher',
            'language' => 'en',
            'abstract' => 'Abstract',
        ]);

        // POST edit step 2 with new data
        $response = $this->post("en/resources/edit/step2/{$resource->id}", [
            'subject_areas' => [$subjectArea->id],
            'learning_resources_types' => [$learningResourceType->id],
            'educational_use' => [$educationalUse->id],
            'level' => [$level->id],
            'keywords' => 'new keyword',
        ]);

        // Should redirect to step 3
        $response->assertRedirect('/resources/edit/step3/'.$resource->id);

        // Verify data is stored in session
        $this->assertNotNull(Session::get('edit_resource_step_2'));
        $sessionData = Session::get('edit_resource_step_2');
        $this->assertContains($subjectArea->id, $sessionData['subject_areas']);
        $this->assertContains($learningResourceType->id, $sessionData['learning_resources_types']);
        $this->assertContains($educationalUse->id, $sessionData['educational_use']);
        $this->assertContains($level->id, $sessionData['level']);

        // Verify data is NOT saved to database
        $resource->refresh();
        $this->assertEquals($originalSubjectCount, $resource->subjects()->count());
        $this->assertEquals(0, $resource->LearningResourceTypes()->count());
    }

    #[Test]
    public function edit_step_two_get_uses_session_data_if_available(): void
    {
        $this->refreshApplicationWithLocale('en');
        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $subjectArea1 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceSubject->value, 'name' => 'Mathematics']);
        $subjectArea2 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceSubject->value, 'name' => 'Physics']);
        $learningType1 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceType->value, 'name' => 'Book']);
        $learningType2 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceType->value, 'name' => 'Video']);
        $educationalUse1 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::EducationalUse->value, 'name' => 'Assignment']);
        $educationalUse2 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::EducationalUse->value, 'name' => 'Lecture']);
        $level1 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceLevels->value, 'name' => 'High School']);
        $level2 = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceLevels->value, 'name' => 'University']);

        $resource = Resource::factory()->create();
        $resource->subjects()->attach($subjectArea1->id);
        $resource->LearningResourceTypes()->attach($learningType1->id);
        $keywordTerm = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::Keywords->value, 'name' => 'db-keyword']);
        ResourceKeyword::factory()->create(['resource_id' => $resource->id, 'tid' => $keywordTerm->id]);
        ResourceEducationalUse::factory()->create(['resource_id' => $resource->id, 'tid' => $educationalUse1->id]);
        $resource->levels()->attach($level1->id);
        ResourceAttachment::factory()->create([
            'resource_id' => $resource->id,
            'file_name' => 'db-attachment.pdf',
            'file_size' => 512,
            'file_mime' => 'application/pdf',
        ]);

        Session::put('edit_resource_step_1', [
            'id' => $resource->id,
            'title' => 'Step 1 Title',
            'author' => 'Author',
            'publisher' => 'Publisher',
            'language' => 'en',
            'abstract' => 'Abstract',
        ]);

        Session::put('edit_resource_step_2', [
            'id' => $resource->id,
            'subject_areas' => [$subjectArea2->id],
            'learning_resources_types' => [$learningType2->id],
            'educational_use' => [$educationalUse1->id, $educationalUse2->id],
            'level' => [$level1->id, $level2->id],
            'keywords' => 'session-keyword-one,session-keyword-two',
            'attc' => [
                ['file_name' => 'session-file.pdf', 'file_size' => 1024, 'file_mime' => 'application/pdf'],
            ],
        ]);

        $response = $this->get("en/resources/edit/step2/{$resource->id}");

        $response->assertOk();
        $response->assertViewIs('resources.resources_modify_step2');
        $response->assertViewHas('resourceSubjectAreas');
        $response->assertViewHas('resourceLearningResourceTypes');
        $response->assertViewHas('editEducationalUse');
        $response->assertViewHas('resourceLevels');
        $response->assertViewHas('resourceKeywords');
        $response->assertViewHas('resourceAttachments');

        $this->assertContains($subjectArea2->id, $response->viewData('resourceSubjectAreas'));
        $this->assertNotContains($subjectArea1->id, $response->viewData('resourceSubjectAreas'));
        $this->assertCount(1, $response->viewData('resourceSubjectAreas'));

        $this->assertContains($learningType2->id, $response->viewData('resourceLearningResourceTypes'));
        $this->assertNotContains($learningType1->id, $response->viewData('resourceLearningResourceTypes'));
        $this->assertCount(1, $response->viewData('resourceLearningResourceTypes'));

        $editEducationalUse = $response->viewData('editEducationalUse');
        $this->assertContains($educationalUse1->id, $editEducationalUse);
        $this->assertContains($educationalUse2->id, $editEducationalUse);
        $this->assertCount(2, $editEducationalUse);

        $resourceLevels = $response->viewData('resourceLevels');
        $this->assertContains($level1->id, $resourceLevels);
        $this->assertContains($level2->id, $resourceLevels);
        $this->assertCount(2, $resourceLevels);

        $resourceKeywords = $response->viewData('resourceKeywords');
        $this->assertStringContainsString('session-keyword-one', $resourceKeywords);
        $this->assertStringContainsString('session-keyword-two', $resourceKeywords);
        $this->assertStringNotContainsString('db-keyword', $resourceKeywords);

        $resourceAttachments = $response->viewData('resourceAttachments');
        $this->assertCount(1, $resourceAttachments);
        $this->assertEquals('session-file.pdf', $resourceAttachments[0]['file_name']);
        $this->assertEquals(1024, $resourceAttachments[0]['file_size']);
    }

    #[Test]
    public function edit_step_two_get_redirects_to_step_one_if_step_one_not_in_session(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $resource = Resource::factory()->create();

        // Without setting step 1 in session
        $response = $this->get("en/resources/edit/step2/{$resource->id}");

        $response->assertRedirect('/resources/edit/step1');
    }

    // Step 3
    #[Test]
    public function edit_step_three_get_displays_correct_resource_values_from_database(): void
    {
        $this->refreshApplicationWithLocale('en');
        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $creativeCommons = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::CreativeCommons->value, 'name' => 'CC BY']);
        $creativeCommonsOther = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::SharePermission->value, 'name' => 'CC BY-SA']);

        $resource = Resource::factory()->create(['status' => 1]);

        ResourceTranslationRight::factory()->create([
            'resource_id' => $resource->id,
            'value' => 1,
        ]);
        ResourceEducationalResource::factory()->create([
            'resource_id' => $resource->id,
            'value' => 1,
        ]);
        ResourceIamAuthor::factory()->create([
            'resource_id' => $resource->id,
            'value' => 1,
        ]);
        ResourceCopyrightHolder::factory()->create([
            'resource_id' => $resource->id,
            'value' => 'Original Copyright Holder',
        ]);
        ResourceSharePermission::factory()->create([
            'resource_id' => $resource->id,
            'tid' => $creativeCommonsOther->id,
        ]);
        $resource->creativeCommons()->attach($creativeCommons->id);

        Session::put('edit_resource_step_1', [
            'id' => $resource->id,
            'title' => 'Step 1 Title',
            'author' => 'Author',
            'publisher' => 'Publisher',
            'language' => 'en',
            'abstract' => 'Abstract',
        ]);
        Session::put('edit_resource_step_2', [
            'id' => $resource->id,
            'subject_areas' => [],
            'learning_resources_types' => [],
            'keywords' => '',
            'educational_use' => [],
            'level' => [],
        ]);

        $response = $this->get("en/resources/edit/step3/{$resource->id}");

        $response->assertOk();
        $response->assertViewIs('resources.resources_modify_step3');
        $response->assertViewHas('dbRecords');
        $response->assertViewHas('resource');
        $response->assertViewHas('edit', true);
        $response->assertViewHas('creativeCommons');
        $response->assertViewHas('creativeCommonsOther');

        $dbRecords = $response->viewData('dbRecords');
        $this->assertEquals($resource->id, $dbRecords->id);
        $this->assertEquals(1, $dbRecords->status);

        $this->assertNotNull($dbRecords->TranslationRights);
        $this->assertEquals(1, $dbRecords->TranslationRights->value);

        $this->assertTrue($dbRecords->educationalResources->isNotEmpty());
        $this->assertEquals(1, $dbRecords->educationalResources->first()->value);

        $this->assertNotNull($dbRecords->iamAuthors);
        $this->assertEquals(1, $dbRecords->iamAuthors->value);

        $this->assertNotNull($dbRecords->CopyrightHolder);
        $this->assertEquals('Original Copyright Holder', $dbRecords->CopyrightHolder->value);

        $this->assertTrue($dbRecords->creativeCommons->contains($creativeCommons->id));

        $this->assertNotNull($dbRecords->SharePermissions);
        $this->assertEquals($creativeCommonsOther->id, $dbRecords->SharePermissions->tid);

        $resourceView = $response->viewData('resource');
        $this->assertEquals(1, $resourceView['status']);
    }

    #[Test]
    public function edit_step_three_post_saves_all_three_steps_to_database(): void
    {
        $this->refreshApplicationWithLocale('en');
        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $subjectArea = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceSubject->value, 'name' => 'Mathematics']);
        $learningType = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceType->value, 'name' => 'Book']);
        $educationalUse = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::EducationalUse->value, 'name' => 'Assignment']);
        $level = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::ResourceLevels->value, 'name' => 'High School']);
        $creativeCommons = TaxonomyTerm::factory()->create(['vid' => TaxonomyVocabularyEnum::CreativeCommons->value, 'name' => 'CC BY']);
        $resourceFile = ResourceFile::create(['name' => 'test-file.pdf', 'label' => 'Test File', 'language' => 'en', 'resource_id' => null]);

        $resource = Resource::factory()->create([
            'title' => 'Original Title',
            'abstract' => 'Original Abstract',
            'language' => 'en',
            'status' => 0,
        ]);

        $step1 = [
            'id' => $resource->id,
            'title' => 'Updated Title Step 1',
            'abstract' => 'Updated Abstract Step 1',
            'language' => 'fa',
            'author' => 'Author One,Author Two',
            'publisher' => 'Publisher Name',
            'translator' => 'Translator Name',
            'resource_file_id' => $resourceFile->id,
        ];
        $step2 = [
            'id' => $resource->id,
            'subject_areas' => [$subjectArea->id],
            'learning_resources_types' => [$learningType->id],
            'educational_use' => [$educationalUse->id],
            'level' => [$level->id],
            'keywords' => 'keyword1,keyword2',
        ];

        $response = $this->withSession([
            'edit_resource_step_1' => $step1,
            'edit_resource_step_2' => $step2,
        ])->post("en/resources/edit/step3/{$resource->id}", [
            'translation_rights' => 1,
            'educational_resource' => 1,
            'iam_author' => 0,
            'copyright_holder' => 'Updated Copyright Holder',
            'creative_commons' => $creativeCommons->id,
            'published' => 1,
        ]);

        $response->assertRedirect();

        $resource->refresh();
        $this->assertEquals('Updated Title Step 1', $resource->title);
        $this->assertEquals('Updated Abstract Step 1', $resource->abstract);
        $this->assertEquals('fa', $resource->language);
        $this->assertEquals($resourceFile->id, $resource->resource_file_id);
        $this->assertEquals(1, $resource->status);

        $this->assertGreaterThan(0, $resource->authors()->count());
        $this->assertGreaterThan(0, $resource->publishers()->count());
        $this->assertGreaterThan(0, $resource->translators()->count());

        $this->assertTrue($resource->subjects->contains($subjectArea->id));
        $this->assertTrue($resource->LearningResourceTypes->contains($learningType->id));
        $this->assertTrue($resource->levels->contains($level->id));
        $this->assertGreaterThan(0, ResourceKeyword::where('resource_id', $resource->id)->count());
        $this->assertTrue(ResourceEducationalUse::where('resource_id', $resource->id)->where('tid', $educationalUse->id)->exists());

        $this->assertNotNull($resource->TranslationRights);
        $this->assertEquals(1, $resource->TranslationRights->value);
        $this->assertTrue($resource->educationalResources->isNotEmpty());
        $this->assertEquals(1, $resource->educationalResources->first()->value);
        $this->assertNotNull($resource->CopyrightHolder);
        $this->assertEquals('Updated Copyright Holder', $resource->CopyrightHolder->value);
        $this->assertTrue($resource->creativeCommons->contains($creativeCommons->id));
        $this->assertNotNull($resource->iamAuthors);
        $this->assertEquals(0, $resource->iamAuthors->value);
    }

    #[Test]
    public function edit_step_three_get_redirects_to_step_one_if_steps_not_in_session(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);
        $this->actingAs($admin);

        $resource = Resource::factory()->create();

        // Without setting step 1 and step 2 in session
        $response = $this->get("en/resources/edit/step3/{$resource->id}");

        $response->assertRedirect('/resources/edit/step1');
    }
}
