<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RoleDashboardTest extends TestCase
{
    use LazilyRefreshDatabase;

    public static function rolesAndDashboardViews(): array
    {
        return [
            'administrator' => [UserRole::Administrador, 'dashboard.administrador', 'Panel de administración'],
            'staff member' => [UserRole::Funcionario, 'dashboard.funcionario', 'Panel del funcionario'],
            'student' => [UserRole::Estudiante, 'dashboard.estudiante', 'Panel del estudiante'],
        ];
    }

    #[DataProvider('rolesAndDashboardViews')]
    public function test_authenticated_user_sees_the_dashboard_for_their_role(
        UserRole $role,
        string $view,
        string $heading,
    ): void {
        $user = User::factory()->create(['role' => $role]);

        $response = $this->withoutVite()
            ->actingAs($user)
            ->get(route('dashboard'));

        $response->assertOk()
            ->assertViewIs($view)
            ->assertSee($heading);
    }

    public function test_guest_is_redirected_to_login_from_the_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_dashboard_guest_page_explains_the_three_profiles(): void
    {
        $response = $this->withoutVite()->get(route('home'));

        $response->assertOk()
            ->assertSee('Administración')
            ->assertSee('Funcionario')
            ->assertSee('Estudiante')
            ->assertSee(route('login'));
    }
}
