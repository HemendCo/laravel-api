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

        $service_name = 'Client';
        
        Artisan::call('make:api-basic', [
            'name' => $service_name,
            'version' => '1'
        ]);

        Artisan::call('api:acl-collect', [
            'service' => $service_name
        ]);

        $publicPermissions = [
            "AuthHandshake",
            "AuthSendCode",
            "AuthSignIn",
            "AuthSignUp",
            "AuthRoles",
        ];
        foreach ($publicPermissions as $permission) {
            $publicRole->givePermissionTo($service_name . $permission);
        }

        $adminPermissions = [
            // Public
            "AuthHandshake",
            "AuthSendCode",
            "AuthSignIn",
            "AuthSignUp",
            "AuthRoles",

            // All Roles (without public)
            "AccountChangeMobileNumber",
            "AccountPermissions",
            "AccountRoles",
            "AccountSignOut",
            "AccountTokensDelete",
            "AccountTokensGet",
            "AccountTokensRefresh",
            "AccountTokensRevoke",
            "AccountUserInfo",

            // Admin
            "AclGroupsChangePosition",
            "AclGroupsGet",
            "AclGroupsGetPermissions",
            "AclGroupsUpdate",
            "AclPermissionsChangePosition",
            "AclPermissionsCollect",
            "AclPermissionsGet",
            "AclPermissionsUpdate",
            "AclRolesActive",
            "AclRolesAdd",
            "AclRolesGet",
            "AclRolesGetPermissions",
            "AclRolesGivePermission",
            "AclRolesInactive",
            "AclRolesRevokePermission",
            "AclRolesSetDefault",
            "AclRolesUpdate"
        ];
        foreach ($adminPermissions as $permission) {
            $adminRole->givePermissionTo($service_name . $permission);
        }

        $userPermissions = [
            // Public
            "AuthHandshake",
            "AuthSendCode",
            "AuthSignIn",
            "AuthSignUp",
            "AuthRoles",

            // All Roles (without public)
            "AccountChangeMobileNumber",
            "AccountPermissions",
            "AccountRoles",
            "AccountSignOut",
            "AccountTokensDelete",
            "AccountTokensGet",
            "AccountTokensRefresh",
            "AccountTokensRevoke",
            "AccountUserInfo"
        ];
        foreach ($userPermissions as $permission) {
            $userRole->givePermissionTo($service_name . $permission);
        }
    }
}
