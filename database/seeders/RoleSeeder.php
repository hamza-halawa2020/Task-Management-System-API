<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'Manager',
            'slug' => 'manager',
            'description' => 'Can create, update, delete tasks and manage dependencies',
        ]);

        Role::create([
            'name' => 'User',
            'slug' => 'user',
            'description' => 'Can only view assigned tasks and update their status',
        ]);
    }
}
