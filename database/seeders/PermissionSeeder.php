<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // User permissions
            ['name' => 'view users', 'group_name' => 'users'],
            ['name' => 'create users', 'group_name' => 'users'],
            ['name' => 'edit users', 'group_name' => 'users'],
            ['name' => 'delete users', 'group_name' => 'users'],
            
            // Page permissions
            ['name' => 'view pages', 'group_name' => 'pages'],
            ['name' => 'create pages', 'group_name' => 'pages'],
            ['name' => 'edit pages', 'group_name' => 'pages'],
            ['name' => 'delete pages', 'group_name' => 'pages'],
            ['name' => 'publish pages', 'group_name' => 'pages'],
            
            // Menu permissions
            ['name' => 'view menus', 'group_name' => 'menus'],
            ['name' => 'create menus', 'group_name' => 'menus'],
            ['name' => 'edit menus', 'group_name' => 'menus'],
            ['name' => 'delete menus', 'group_name' => 'menus'],
            
            // Route permissions
            ['name' => 'view routes', 'group_name' => 'routes'],
            ['name' => 'create routes', 'group_name' => 'routes'],
            ['name' => 'edit routes', 'group_name' => 'routes'],
            ['name' => 'delete routes', 'group_name' => 'routes'],
            
            // Content permissions
            ['name' => 'view contents', 'group_name' => 'contents'],
            ['name' => 'create contents', 'group_name' => 'contents'],
            ['name' => 'edit contents', 'group_name' => 'contents'],
            ['name' => 'delete contents', 'group_name' => 'contents'],
            
            // Setting permissions
            ['name' => 'view settings', 'group_name' => 'settings'],
            ['name' => 'edit settings', 'group_name' => 'settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}