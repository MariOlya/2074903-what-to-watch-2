<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'usual',
            'moderator',
            'admin'
        ];

        foreach ($roles as $role) {
            DB::table('user_roles')->insert([
                'role' => $role
            ]);
        }
    }
}
