<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_can_view_the_login_form(): void
    {
        $this->withoutVite();

        $this->get(route('login'))
            ->assertOk()
            ->assertViewIs('auth.login')
            ->assertSee('Iniciar sesión')
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false);
    }

    public function test_valid_credentials_authenticate_the_user_and_redirect_to_dashboard(): void
    {
        $user = User::factory()->create();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->get(route('dashboard'))->assertRedirect(route('two-factor.setup'));
    }

    public function test_new_user_must_confirm_authenticator_before_accessing_the_system(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('oferta.index'))
            ->assertRedirect(route('two-factor.setup'));

        $this->get(route('two-factor.setup'))
            ->assertOk()
            ->assertSee('Generar código QR');

        $this->from(route('two-factor.setup'))
            ->post(route('two-factor.enable'))
            ->assertRedirect(route('two-factor.setup'));

        $user->refresh();
        $this->assertNotNull($user->two_factor_secret);
        $this->assertNull($user->two_factor_confirmed_at);
        $this->assertArrayNotHasKey('two_factor_secret', $user->toArray());
        $this->assertArrayNotHasKey('two_factor_recovery_codes', $user->toArray());

        $this->get(route('two-factor.setup'))
            ->assertOk()
            ->assertSee('<svg', false);

        $this->from(route('two-factor.setup'))
            ->post(route('two-factor.confirm'), ['code' => 'invalid-code'])
            ->assertRedirect(route('two-factor.setup'))
            ->assertSessionHasErrorsIn('confirmTwoFactorAuthentication', 'code');

        $this->assertNull($user->refresh()->two_factor_confirmed_at);

        $code = app(Google2FA::class)->getCurrentOtp(Crypt::decrypt($user->two_factor_secret));

        $this->from(route('two-factor.setup'))
            ->post(route('two-factor.confirm'), ['code' => $code])
            ->assertRedirect(route('two-factor.setup'));

        $this->assertNotNull($user->refresh()->two_factor_confirmed_at);
        $this->get(route('two-factor.setup'))
            ->assertOk()
            ->assertSee($user->recoveryCodes()[0]);
    }

    public function test_every_login_requires_a_code_and_two_factor_cannot_be_disabled(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->from(route('two-factor.setup'))
            ->post(route('two-factor.enable'))
            ->assertRedirect(route('two-factor.setup'));

        $user->refresh()->forceFill(['two_factor_confirmed_at' => now()])->save();

        $this->delete(route('two-factor.disable'))->assertForbidden();
        $this->assertNotNull($user->refresh()->two_factor_secret);

        $this->post(route('logout'));
        $this->assertGuest();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
            'remember' => '1',
        ])
            ->assertRedirect(route('two-factor.login'))
            ->assertSessionHas('login.remember', false);

        $this->get(route('two-factor.login'))
            ->assertOk()
            ->assertSee('Código de Authenticator');

        $code = app(Google2FA::class)->getCurrentOtp(Crypt::decrypt($user->two_factor_secret));

        $this->post(route('two-factor.login.store'), ['code' => $code])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->get(route('dashboard'))->assertOk();
    }

    public function test_invalid_credentials_return_an_error_without_authenticating_the_user(): void
    {
        $user = User::factory()->create();

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'incorrect-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_registration_is_not_available_while_only_login_is_enabled(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_guest_is_redirected_to_login_from_the_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }
}
