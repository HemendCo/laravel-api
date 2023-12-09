<?php

namespace Database\Seeders;

use App\Models\AclRoles as Roles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $publicRole = Roles::where('name', 'public')->first();
        if (!Roles::where('name', 'public')->exists()) {
            $publicRole = Roles::create([
                'activated' => '1',
                'name' => 'public',
                'title' => 'Public',
                'guard_name' => config('auth.defaults.guard'),
            ]);
        }

        if (!Roles::where('name', 'super-admin')->exists()) {
            Roles::create([
                'activated' => '1',
                'name' => 'super-admin',
                'title' => 'Super Admin',
                'guard_name' => config('auth.defaults.guard'),
            ]);
        }

        $adminRole = Roles::where('name', 'admin')->first();
        if (!Roles::where('name', 'admin')->exists()) {
            $adminRole = Roles::create([
                'activated' => '1',
                'name' => 'admin',
                'title' => 'Admin',
                'guard_name' => config('auth.defaults.guard'),
            ]);
        }

        $userRole = Roles::where('name', 'user')->first();
        if (!$userRole) {
            $userRole = Roles::create([
                'activated' => '1',
                'is_default' => '1',
                'name' => 'user',
                'title' => 'User',
                'guard_name' => config('auth.defaults.guard'),
            ]);
        }

        if (!\App\Models\Users::where('mobile', '09356449579')->exists()) {
            $admin = \App\Models\Users::create([
                'not_deleted' => '1',
                'activated' => '1',
                'first_name' => 'بلال',
                'last_name' => 'آرست',
                'gender' => 'M',
                'mobile' => '09356449579',
            ]);

            $admin->assignRole('super-admin');
        }

        $admin_service_name = 'Admin';
        $client_service_name = 'Client';

        Artisan::call('make:api-basic', [
            'name' => $admin_service_name,
            'version' => '1',
            '--mode' => 'admin',
        ]);
        Artisan::call('make:api-basic', [
            'name' => $client_service_name,
            'version' => '1',
            '--mode' => 'client',
        ]);

        Artisan::call('api:acl-collect', [
            'service' => $admin_service_name
        ]);
        Artisan::call('api:acl-collect', [
            'service' => $client_service_name
        ]);

        $accessMember = [
            [$admin_service_name, $adminRole],
            [$client_service_name, $adminRole],
            [$client_service_name, $userRole],
        ];
        foreach ($accessMember as $acc) {
            \Hemend\Api\Libraries\Permissions::roleGivePermissionTo($acc[0], $acc[1], [
                \Hemend\Api\Libraries\Service::PERMISSION_FLAG_PUBLIC,
                \Hemend\Api\Libraries\Service::PERMISSION_FLAG_PRIVATE,
                \Hemend\Api\Libraries\Service::PERMISSION_FLAG_PRIVATE_ONLY
            ]);
        }

        $accessPublic = [
            [$admin_service_name, $publicRole],
            [$client_service_name, $publicRole],
        ];
        foreach ($accessPublic as $acc) {
            \Hemend\Api\Libraries\Permissions::roleGivePermissionTo($acc[0], $acc[1], [
                \Hemend\Api\Libraries\Service::PERMISSION_FLAG_PUBLIC,
                \Hemend\Api\Libraries\Service::PERMISSION_FLAG_PUBLIC_ONLY
            ]);
        }
    }
}
