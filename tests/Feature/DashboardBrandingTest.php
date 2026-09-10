<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardBrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_website_name_is_sarinah_street(): void
    {
        $this->assertEquals('Sarinah Street', config('app.name'));

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('<title>Sarinah Street</title>', false);
    }

    public function test_admin_dashboard_has_sarinah_street_and_no_filament_welcome_widgets(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::firstOrCreate(
            ['email' => 'admin_test@test.com'],
            ['name' => 'Admin Test', 'password' => bcrypt('password')]
        );
        $admin->syncRoles(['admin']);

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Sarinah Street');

        // Verify Filament documentation card & Welcome card are not present
        $response->assertDontSee('filamentphp.com');
        $response->assertDontSee('Filament documentation');
        $response->assertDontSee('https://filamentphp.com/docs');
    }
}

