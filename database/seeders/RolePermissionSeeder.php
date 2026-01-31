<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'view allocation',
            'create allocation',
            'edit allocation',
            'delete allocation',
            'approve allocation',
        ];


        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $guest = Role::firstOrCreate(['name' => 'guest']);
        $user = Role::firstOrCreate(['name' => 'user']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $admin = Role::firstOrCreate(['name' => 'admin']);

        $guest->givePermissionTo([
            'view allocation',
        ]);

        $user->givePermissionTo([
            'view allocation',
            'create allocation',
        ]);

        $manager->givePermissionTo([
            'view allocation',
            'create allocation',
            'approve allocation',
        ]);

        $admin->givePermissionTo([
            'view allocation',
            'create allocation',
            'edit allocation',
            'delete allocation',
            'approve allocation',
        ]);

    }
}
