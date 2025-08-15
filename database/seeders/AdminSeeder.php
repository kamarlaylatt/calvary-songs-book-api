<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Admin::updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Test Admin', 'password' => bcrypt('password')]
        );

        // Attach role id 1 (Superadmin) to the admin user
        $role = Role::find(1);
        if ($role) {
            $admin->roles()->syncWithoutDetaching([$role->id]);
        }
    }
}