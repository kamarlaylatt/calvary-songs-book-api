<?php

namespace Database\Seeders;

use App\Enums\RoleType;
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
                'id' => RoleType::Superadmin->value,
                'name' => RoleType::Superadmin->name(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => RoleType::Admin->value,
                'name' => RoleType::Admin->name(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => RoleType::Guest->value,
                'name' => RoleType::Guest->name(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
