<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Super Admin Role
        $superAdmin = Role::create([
            'name' => 'Super Admin',
            'description' => 'Has full access to all features',
            'is_super_admin' => true
        ]);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin Role
        $admin = Role::create([
            'name' => 'Admin',
            'description' => 'Has access to most admin features'
        ]);
        $admin->givePermissionTo([
            'view users', 'create users', 'edit users',
            'view pages', 'create pages', 'edit pages', 'delete pages', 'publish pages',
            'view menus', 'create menus', 'edit menus', 'delete menus',
            'view contents', 'create contents', 'edit contents', 'delete contents',
            'view settings', 'edit settings',
        ]);

        // Editor Role
        $editor = Role::create([
            'name' => 'Editor',
            'description' => 'Can manage content and pages'
        ]);
        $editor->givePermissionTo([
            'view pages', 'create pages', 'edit pages', 'publish pages',
            'view contents', 'create contents', 'edit contents',
        ]);

        // User Role
        $user = Role::create([
            'name' => 'User',
            'description' => 'Regular user with basic access'
        ]);
    }
}