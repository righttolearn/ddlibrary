<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\MenuController
 */
class MenuControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function ajax_get_parents_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $menus = Menu::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('ajax_get_parents', ['id' => $menus[0]->id, 'loc' => $menus[0]->location, 'lang' => 'en']));

        $response->assertOk();
    }

    #[Test]
    public function create_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $response = $this->actingAs($admin)->get('en/admin/menu/add/0'); // Assuming 0 for new menu

        $response->assertOk();
        $response->assertViewIs('admin.menu.menu_add');
        $response->assertViewHas('menu');
        $response->assertViewHas('new_menu', true);
        $response->assertViewHas('locations');
        $response->assertViewHas('parents');
    }

    #[Test]
    public function edit_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $menu = Menu::factory()->create();

        $response = $this->actingAs($admin)->get('en/admin/menu/edit/'.$menu->id);

        $response->assertOk();
        $response->assertViewIs('admin.menu.menu_edit');
        $response->assertViewHas('details');
        $response->assertViewHas('locations');
        $response->assertViewHas('parents');
    }

    #[Test]
    public function index_returns_an_ok_response(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $menus = Menu::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get('en/admin/menu');

        $response->assertOk();
        $response->assertViewIs('admin.menu.menu_list');
        $response->assertViewHas('menuRecords');
        $response->assertViewHas('searchBar');
        $response->assertViewHas('menuRecords', function ($menuRecords) use ($menus) {
            foreach ($menus as $menu) {
                if (! collect($menuRecords)->contains(fn ($item) => $item->id === $menu->id)) {
                    return false;
                }
            }

            return true;
        });
    }

    #[Test]
    public function sort_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $menus = Menu::factory()->count(3)->create();

        $response = $this->actingAs($admin)->post(route('sort_menu'), [
            'data' => $menus->map(fn($menu) => ['id' => $menu->id])->toArray(),
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
    }

    #[Test]
    public function store_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $response = $this->actingAs($admin)->post(route('store_menu'), $this->data(['title' => 'New Menu']));

        $response->assertRedirect('admin/menu');
        $this->assertDatabaseHas('menus', ['title' => 'New Menu']);
    }

    #[Test]
    public function title_field_is_required(): void
    {
        $this->refreshApplicationWithLocale('en');
        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $response = $this->actingAs($admin)->post(
            route('store_menu'),
            $this->data([
                'title' => '',
            ]),
        );
        $response->assertStatus(400);
    }

    #[Test]
    public function location_field_is_required(): void
    {
        $this->refreshApplicationWithLocale('en');
        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $response = $this->actingAs($admin)->post(route('store_menu'), $this->data(['location' => '']));

        $response->assertStatus(400);
    }

    #[Test]
    public function path_field_is_required(): void
    {
        $this->refreshApplicationWithLocale('en');
        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $response = $this->actingAs($admin)->post(route('store_menu'), $this->data(['path' => '']));

        $response->assertStatus(400);
    }

    #[Test]
    public function status_field_is_required(): void
    {
        $this->refreshApplicationWithLocale('en');
        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $response = $this->actingAs($admin)->post(route('store_menu'), $this->data(['status' => '']));

        $response->assertStatus(400);
    }

    #[Test]
    public function weight_field_is_required(): void
    {
        $this->refreshApplicationWithLocale('en');
        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $response = $this->actingAs($admin)->post(route('store_menu'), $this->data(['weight' => '']));

        $response->assertStatus(400);
    }

    #[Test]
    public function language_field_is_required(): void
    {
        $this->refreshApplicationWithLocale('en');
        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $response = $this->actingAs($admin)->post(route('store_menu'), $this->data(['language' => '']));

        $response->assertStatus(400);
    }

    #[Test]
    public function translate_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $menu = Menu::factory()->create();

        $response = $this->actingAs($admin)->get('en/admin/menu/translate/'.$menu->id);

        $response->assertOk();
        $response->assertViewIs('admin.menu.menu_translate');
        $response->assertViewHas('translations');
        $response->assertViewHas('locals');
        $response->assertViewHas('tnid');
        $response->assertViewHas('id', $menu->id);
    }

    #[Test]
    public function update_returns_an_ok_response(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $menu = Menu::factory()->create();

        $response = $this->actingAs($admin)->post(
            route('update_menu', ['menuId' => $menu->id]),
            $this->data([
                'title' => 'Updated Menu',
            ]),
        );

        $response->assertRedirect('admin/menu/edit/'.$menu->id);
        $this->assertDatabaseHas('menus', ['title' => 'Updated Menu']);
    }

    #[Test]
    public function destroy_deletes_single_menu_without_translations(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $menu = Menu::factory()->create([
            'tnid' => null,
            'parent' => 0,
        ]);

        $response = $this->actingAs($admin)->delete(route('delete_menu', $menu->id));

        $response->assertRedirect('admin/menu');
        $response->assertSessionHas('success', 'Menu deleted successfully!');
        $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
    }

    #[Test]
    public function destroy_deletes_menu_and_all_translations_with_same_tnid(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $tnid = Menu::max('tnid') + 1;

        // Create menu with translations
        $menuEn = Menu::factory()->create([
            'tnid' => $tnid,
            'language' => 'en',
            'parent' => 0,
        ]);

        $menuFa = Menu::factory()->create([
            'tnid' => $tnid,
            'language' => 'fa',
            'parent' => 0,
        ]);

        $menuPs = Menu::factory()->create([
            'tnid' => $tnid,
            'language' => 'ps',
            'parent' => 0,
        ]);

        $response = $this->actingAs($admin)->delete(route('delete_menu', $menuEn->id));

        $response->assertRedirect('admin/menu');
        $response->assertSessionHas('success', 'Menu and all translations deleted successfully!');

        // Verify all translations are deleted
        $this->assertDatabaseMissing('menus', ['id' => $menuEn->id]);
        $this->assertDatabaseMissing('menus', ['id' => $menuFa->id]);
        $this->assertDatabaseMissing('menus', ['id' => $menuPs->id]);
    }

    #[Test]
    public function destroy_deletes_all_translations_from_translate_page(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $tnid = Menu::max('tnid') + 1;

        // Create menu with translations
        $menuEn = Menu::factory()->create([
            'tnid' => $tnid,
            'language' => 'en',
            'parent' => 0,
        ]);

        $menuFa = Menu::factory()->create([
            'tnid' => $tnid,
            'language' => 'fa',
            'parent' => 0,
        ]);

        $menuPs = Menu::factory()->create([
            'tnid' => $tnid,
            'language' => 'ps',
            'parent' => 0,
        ]);

        // Delete all translations from translate page
        $response = $this->actingAs($admin)->delete(route('delete_menu', $menuEn->id));

        $response->assertRedirect('admin/menu');
        $response->assertSessionHas('success', 'Menu and all translations deleted successfully!');

        // Verify all translations are deleted
        $this->assertDatabaseMissing('menus', ['id' => $menuEn->id]);
        $this->assertDatabaseMissing('menus', ['id' => $menuFa->id]);
        $this->assertDatabaseMissing('menus', ['id' => $menuPs->id]);
    }

    #[Test]
    public function destroy_requires_authentication(): void
    {
        $this->refreshApplicationWithLocale('en');

        $menu = Menu::factory()->create();

        $response = $this->delete(route('delete_menu', $menu->id));

        // Should redirect to login (may or may not have locale prefix)
        $this->assertTrue(
            $response->isRedirect() &&
            (str_contains($response->getTargetUrl(), 'login') || str_contains($response->getTargetUrl(), '/en/login'))
        );
    }

    #[Test]
    public function destroy_requires_admin_role(): void
    {
        $this->refreshApplicationWithLocale('en');

        $user = User::factory()->create();
        // Don't attach admin role

        $menu = Menu::factory()->create();

        $response = $this->actingAs($user)->delete(route('delete_menu', $menu->id));

        // Should redirect or return 403
        $this->assertTrue(
            $response->isRedirect() || $response->status() === 403
        );
    }

    #[Test]
    public function destroy_returns_404_for_non_existent_menu(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $response = $this->actingAs($admin)->delete(route('delete_menu', 99999));

        $response->assertNotFound();
    }

    #[Test]
    public function destroy_deletes_sub_menu_without_children(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        // Create parent menu
        $parentMenu = Menu::factory()->create([
            'parent' => 0,
            'tnid' => null,
        ]);

        // Create sub-menu
        $subMenu = Menu::factory()->create([
            'parent' => $parentMenu->id,
            'tnid' => null,
        ]);

        // Delete sub-menu
        $response = $this->actingAs($admin)->delete(route('delete_menu', $subMenu->id));

        $response->assertRedirect('admin/menu');
        $response->assertSessionHas('success', 'Menu deleted successfully!');

        // Verify sub-menu is deleted
        $this->assertDatabaseMissing('menus', ['id' => $subMenu->id]);

        // Verify parent menu still exists
        $this->assertDatabaseHas('menus', ['id' => $parentMenu->id]);
    }

    #[Test]
    public function destroy_deletes_menu_with_translations_and_sub_menus(): void
    {
        $this->refreshApplicationWithLocale('en');

        $admin = User::factory()->create();
        $admin->roles()->attach(5);

        $tnid = Menu::max('tnid') + 1;

        // Create parent menu with translations
        $parentMenuEn = Menu::factory()->create([
            'tnid' => $tnid,
            'language' => 'en',
            'parent' => 0,
        ]);

        $parentMenuFa = Menu::factory()->create([
            'tnid' => $tnid,
            'language' => 'fa',
            'parent' => 0,
        ]);

        // Create sub-menus
        $subMenu1 = Menu::factory()->create([
            'parent' => $parentMenuEn->id,
            'tnid' => null,
        ]);

        $subMenu2 = Menu::factory()->create([
            'parent' => $parentMenuEn->id,
            'tnid' => null,
        ]);

        // Delete parent menu (should delete all translations)
        $response = $this->actingAs($admin)->delete(route('delete_menu', $parentMenuEn->id));

        $response->assertRedirect('admin/menu');
        $response->assertSessionHas('success', 'Menu and all translations deleted successfully!');

        // Verify parent menu translations are deleted
        $this->assertDatabaseMissing('menus', ['id' => $parentMenuEn->id]);
        $this->assertDatabaseMissing('menus', ['id' => $parentMenuFa->id]);
    }

    protected function data($merge = [])
    {
        return array_merge(
            [
                'title' => 'Home Page',
                'location' => 'header',
                'path' => '/new-menu',
                'status' => 1,
                'language' => 'en',
                'weight' => 1,
            ],
            $merge,
        );
    }
}
