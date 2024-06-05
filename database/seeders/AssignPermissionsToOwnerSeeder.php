<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AssignPermissionsToOwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ownerRole = Role::where('name', 'owner')->first();

        // Check if the 'owner' role exists
        if ($ownerRole) {
            // Get all permissions
            $permissions = Permission::pluck('id')->all();

            // Sync permissions to the 'owner' role
            $ownerRole->syncPermissions($permissions);
        }
    }
}
