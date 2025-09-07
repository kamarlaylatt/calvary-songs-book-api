<?php

namespace Database\Seeders;

use App\Enums\AdminRoleType;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insertOrIgnore([
            [
                'id' => AdminRoleType::Superadmin->value,
                'name' => AdminRoleType::Superadmin->name(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => AdminRoleType::Admin->value,
                'name' => AdminRoleType::Admin->name(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => AdminRoleType::Guest->value,
                'name' => AdminRoleType::Guest->name(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
