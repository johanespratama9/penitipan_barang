<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $kasir;
    protected User $penitip;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup roles
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'penitip', 'guard_name' => 'web']);

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test@test.com'],
            ['name' => 'Admin Test', 'password' => bcrypt('password')]
        );
        $this->admin->syncRoles(['admin']);

        $this->kasir = User::firstOrCreate(
            ['email' => 'kasir_test@test.com'],
            ['name' => 'Kasir Test', 'password' => bcrypt('password')]
        );
        $this->kasir->syncRoles(['kasir']);

        $this->penitip = User::firstOrCreate(
            ['email' => 'penitip_test@test.com'],
            ['name' => 'Penitip Test', 'password' => bcrypt('password')]
        );
        $this->penitip->syncRoles(['penitip']);
    }

    public function test_category_can_be_created_and_slug_auto_generated(): void
    {
        $category = Category::create([
            'name' => 'Elektronik & Gadget',
            'description' => 'Barang elektronik',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Elektronik & Gadget',
            'slug' => 'elektronik-gadget',
            'is_active' => 1,
        ]);
    }

    public function test_category_name_must_be_unique(): void
    {
        Category::create([
            'name' => 'Pakaian Pria',
            'slug' => 'pakaian-pria',
        ]);

        $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);

        Category::create([
            'name' => 'Pakaian Pria',
            'slug' => 'pakaian-pria-2',
        ]);
    }

    public function test_category_slug_must_be_unique(): void
    {
        Category::create([
            'name' => 'Buku Novel',
            'slug' => 'buku-novel',
        ]);

        $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);

        Category::create([
            'name' => 'Buku Novel Baru',
            'slug' => 'buku-novel',
        ]);
    }

    public function test_admin_has_full_category_permissions(): void
    {
        $category = Category::create([
            'name' => 'Sepatu & Sandal',
        ]);

        $this->assertTrue($this->admin->can('viewAny', Category::class));
        $this->assertTrue($this->admin->can('view', $category));
        $this->assertTrue($this->admin->can('create', Category::class));
        $this->assertTrue($this->admin->can('update', $category));
        $this->assertTrue($this->admin->can('delete', $category));
    }

    public function test_kasir_can_only_view_categories(): void
    {
        $category = Category::create([
            'name' => 'Aksesoris',
        ]);

        $this->assertTrue($this->kasir->can('viewAny', Category::class));
        $this->assertTrue($this->kasir->can('view', $category));
        $this->assertFalse($this->kasir->can('create', Category::class));
        $this->assertFalse($this->kasir->can('update', $category));
        $this->assertFalse($this->kasir->can('delete', $category));
    }

    public function test_penitip_cannot_access_categories(): void
    {
        $category = Category::create([
            'name' => 'Tas & Dompet',
        ]);

        $this->assertFalse($this->penitip->can('viewAny', Category::class));
        $this->assertFalse($this->penitip->can('view', $category));
        $this->assertFalse($this->penitip->can('create', Category::class));
        $this->assertFalse($this->penitip->can('update', $category));
        $this->assertFalse($this->penitip->can('delete', $category));
    }
}
