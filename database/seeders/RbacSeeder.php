<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['Dashboard', 'dashboard.view', 'Dashboard'],
            ['Admin Access', 'admin.access', 'System'],

            ['View Users', 'users.view', 'Users'],
            ['Create Users', 'users.create', 'Users'],
            ['Edit Users', 'users.edit', 'Users'],
            ['Delete Users', 'users.delete', 'Users'],
            ['Change User Status', 'users.status', 'Users'],
            ['Reset User Password', 'users.password.reset', 'Users'],
            ['Assign User Roles', 'users.roles.assign', 'Users'],

            ['View Roles', 'roles.view', 'Roles'],
            ['Create Roles', 'roles.create', 'Roles'],
            ['Edit Roles', 'roles.edit', 'Roles'],
            ['Delete Roles', 'roles.delete', 'Roles'],
            ['Manage Role Permissions', 'roles.permissions', 'Roles'],

            ['View Products', 'products.view', 'Products'],
            ['Create Products', 'products.create', 'Products'],
            ['Edit Products', 'products.edit', 'Products'],
            ['Delete Products', 'products.delete', 'Products'],

            ['View Orders', 'orders.view', 'Orders'],
            ['View Order Details', 'orders.show', 'Orders'],
            ['Update Orders', 'orders.update', 'Orders'],

            ['View Catalog', 'catalog.view', 'Catalog'],
            ['Manage Catalog', 'catalog.manage', 'Catalog'],

            ['View Company', 'company.view', 'Company'],
            ['Edit Company', 'company.edit', 'Company'],

            ['View Sales', 'sales.view', 'Sales'],
            ['Create Sales', 'sales.create', 'Sales'],
            ['View Sales History', 'sales.history', 'Sales'],
            ['View Invoices', 'sales.invoice', 'Sales'],
            ['View Tracking', 'tracking.view', 'Orders'],

            ['View General Settings', 'settings.general.view', 'Settings'],
            ['Edit General Settings', 'settings.general.edit', 'Settings'],
            ['View Layout Settings', 'settings.layout.view', 'Settings'],
            ['Edit Layout Settings', 'settings.layout.edit', 'Settings'],
            ['View Product Display Settings', 'settings.product-display.view', 'Settings'],
            ['Edit Product Display Settings', 'settings.product-display.edit', 'Settings'],

            ['View Gallery', 'gallery.view', 'Gallery'],
            ['Manage Gallery', 'gallery.manage', 'Gallery'],

            ['View Services', 'services.view', 'Services'],
            ['Create Services', 'services.create', 'Services'],
            ['Edit Services', 'services.edit', 'Services'],
            ['Delete Services', 'services.delete', 'Services'],
            ['Change Service Status', 'services.status', 'Services'],
            ['View Contact Messages', 'contact-messages.view', 'Contact'],
            ['Manage Contact Messages', 'contact-messages.manage', 'Contact'],
            ['Delete Contact Messages', 'contact-messages.delete', 'Contact'],

            ['View Audit Logs', 'audit.view', 'System'],
        ];

        foreach ($permissions as [$name, $slug, $group]) {
            Permission::updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'group' => $group,
            ]);
        }

        $super = Role::updateOrCreate(['slug' => 'super-admin'], [
            'name' => 'Super Admin',
            'description' => 'Full system access.',
            'is_system' => true,
        ]);
        $super->permissions()->sync(Permission::pluck('id'));

        $admin = Role::updateOrCreate(['slug' => 'admin'], [
            'name' => 'Admin',
            'description' => 'Full operational administration except role/system ownership.',
            'is_system' => true,
        ]);
        $admin->permissions()->sync(Permission::whereNotIn('slug', [
            'roles.delete', 'roles.permissions', 'users.delete', 'audit.view',
        ])->pluck('id'));

        $manager = Role::updateOrCreate(['slug' => 'manager'], [
            'name' => 'Manager',
            'description' => 'Manage products, orders, sales and catalog.',
            'is_system' => false,
        ]);
        $manager->permissions()->sync(Permission::whereIn('slug', [
            'admin.access', 'dashboard.view', 'products.view', 'products.create', 'products.edit',
            'orders.view', 'orders.show', 'orders.update', 'catalog.view', 'catalog.manage',
            'sales.view', 'sales.create', 'sales.history', 'sales.invoice', 'tracking.view',
        ])->pluck('id'));

        $customer = Role::updateOrCreate(['slug' => 'customer'], [
            'name' => 'Customer',
            'description' => 'Website customer. No admin access.',
            'is_system' => true,
        ]);
        $customer->permissions()->detach();

        $legacyAdmins = User::where('is_admin', true)->get();
        foreach ($legacyAdmins as $user) {
            $user->roles()->syncWithoutDetaching([$super->id]);
            if (!$user->status) $user->update(['status' => 'active']);
        }

        User::where('is_admin', false)->get()->each(function (User $user) use ($customer) {
            if ($user->roles()->count() === 0) {
                $user->roles()->syncWithoutDetaching([$customer->id]);
            }
            if (!$user->status) $user->update(['status' => 'active']);
        });
    }
}
