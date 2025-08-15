<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $role1;
    protected $role2;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test admin user for authentication
        $this->admin = Admin::factory()->create([
            'status' => 'active',
            'password' => Hash::make('password')
        ]);
        
        // Create some roles
        $this->role1 = Role::factory()->create(['name' => 'admin']);
        $this->role2 = Role::factory()->create(['name' => 'editor']);
    }

    public function test_can_list_admins()
    {
        // Create additional admins for testing
        Admin::factory()->count(3)->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->getJson('/api/admin/admins');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'current_page',
                'data' => [
                    '*' => ['id', 'name', 'email', 'status', 'roles']
                ],
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total'
            ]);
    }

    public function test_can_create_admin()
    {
        $data = [
            'name' => 'New Admin',
            'email' => 'newadmin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'status' => 'active',
            'roles' => [$this->role1->id]
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->postJson('/api/admin/admins', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id', 'name', 'email', 'status', 'roles'
            ])
            ->assertJson([
                'name' => 'New Admin',
                'email' => 'newadmin@example.com',
                'status' => 'active'
            ]);

        $this->assertDatabaseHas('admins', [
            'name' => 'New Admin',
            'email' => 'newadmin@example.com',
            'status' => 'active'
        ]);

        $newAdmin = Admin::where('email', 'newadmin@example.com')->first();
        $this->assertNotNull($newAdmin);
        $this->assertTrue($newAdmin->roles->contains('id', $this->role1->id));
    }

    public function test_can_show_admin()
    {
        $admin = Admin::factory()->create([
            'status' => 'active'
        ]);
        $admin->roles()->attach($this->role1);

        $response = $this->actingAs($this->admin, 'admin')
            ->getJson("/api/admin/admins/{$admin->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'status' => $admin->status
            ])
            ->assertJsonStructure([
                'id', 'name', 'email', 'status', 'roles'
            ]);
    }

    public function test_can_update_admin()
    {
        $admin = Admin::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'status' => 'active'
        ]);
        $admin->roles()->attach($this->role1);

        $data = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'status' => 'inactive',
            'roles' => [$this->role2->id]
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->putJson("/api/admin/admins/{$admin->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'status' => 'inactive'
            ])
            ->assertJsonStructure([
                'id', 'name', 'email', 'status', 'roles'
            ]);

        $this->assertDatabaseHas('admins', [
            'id' => $admin->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'status' => 'inactive'
        ]);

        $updatedAdmin = Admin::find($admin->id);
        $this->assertTrue($updatedAdmin->roles->contains('id', $this->role2->id));
        $this->assertFalse($updatedAdmin->roles->contains('id', $this->role1->id));
    }

    public function test_can_delete_admin()
    {
        $admin = Admin::factory()->create([
            'status' => 'active'
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->deleteJson("/api/admin/admins/{$admin->id}");

        $response->assertStatus(204);

        // Check that the admin was soft deleted
        $this->assertDatabaseHas('admins', [
            'id' => $admin->id
        ]);
        
        $deletedAdmin = Admin::withTrashed()->find($admin->id);
        $this->assertNotNull($deletedAdmin->deleted_at);
    }

    public function test_cannot_create_admin_with_invalid_data()
    {
        $data = [
            'name' => '', // Required field
            'email' => 'invalid-email', // Invalid format
            'password' => 'short', // Too short
            'status' => 'invalid-status' // Invalid status
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->postJson('/api/admin/admins', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password', 'status']);
    }

    public function test_cannot_update_admin_with_invalid_data()
    {
        $admin = Admin::factory()->create();

        $data = [
            'email' => 'invalid-email', // Invalid format
            'status' => 'invalid-status' // Invalid status
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->putJson("/api/admin/admins/{$admin->id}", $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'status']);
    }
}