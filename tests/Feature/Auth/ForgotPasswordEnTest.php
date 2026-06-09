<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ForgotPasswordEnTest extends TestCase
{
    use RefreshDatabase;

    protected string $defaultLocale = 'en';

    #[Test]
    public function en_user_can_view_forgot_password_page(): void
    {
        $response = $this->get('/en/password/reset');

        $response->assertSuccessful();
        $response->assertViewIs('auth.passwords.email');

        $response->assertSee('Reset your password');
        $response->assertSee('Email');
        $response->assertSee('Send password reset link');
    }

    #[Test]
    public function en_getting_error_if_email_does_not_exist(): void
    {
        User::factory()->create();

        $response = $this->post('/en/password/email', ['email' => 'invalid@mail.com']);

        $response->assertSessionHasErrors(['email' => 'if the user exists in our system, we will send an email']);
    }
}
