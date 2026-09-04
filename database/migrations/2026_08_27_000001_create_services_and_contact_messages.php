<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
                $table->id();
                $table->string('service_type', 30)->index();
                $table->string('title', 180);
                $table->string('slug', 220)->unique();
                $table->string('short_description', 500)->nullable();
                $table->longText('description')->nullable();
                $table->string('icon', 100)->nullable();
                $table->string('image_path', 500)->nullable();
                $table->json('features')->nullable();
                $table->string('price_note', 255)->nullable();
                $table->unsignedInteger('sort_order')->default(0)->index();
                $table->boolean('status')->default(true)->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('contact_messages')) {
            Schema::create('contact_messages', function (Blueprint $table) {
                $table->id();
                $table->string('name', 120);
                $table->string('email', 190);
                $table->string('phone', 50)->nullable();
                $table->string('subject', 190)->nullable();
                $table->text('message');
                $table->string('status', 20)->default('new')->index();
                $table->text('admin_note')->nullable();
                $table->timestamps();
            });
        }

        // Default services are inserted only when the table is empty, so existing data is never overwritten.
        if (Schema::hasTable('services') && DB::table('services')->count() === 0) {
            $now = now();
            $defaults = [
                ['hospital', 'Medical Equipment Installation', 'medical-equipment-installation', 'Professional installation and setup of hospital and biomedical equipment.', 'We provide safe, structured installation and commissioning support for medical equipment, helping hospitals start using critical systems with confidence.', 'fa-screwdriver-wrench', null, ['Site assessment and installation planning', 'Equipment setup and commissioning', 'Basic user orientation', 'Post-installation support'], null, 10, true],
                ['hospital', 'Preventive Maintenance', 'preventive-maintenance', 'Scheduled maintenance to keep biomedical equipment reliable and ready.', 'Our preventive maintenance service is designed to reduce unexpected downtime through planned inspection, cleaning, functional checks and service documentation.', 'fa-shield-heart', null, ['Routine inspection and cleaning', 'Functional and safety checks', 'Maintenance reporting', 'Service scheduling'], null, 20, true],
                ['hospital', 'Medical Equipment Repair', 'medical-equipment-repair', 'Troubleshooting and repair support for hospital biomedical equipment.', 'Our technical service team supports diagnosis and repair of eligible biomedical equipment with a focus on safe restoration and clear service reporting.', 'fa-screwdriver', null, ['Fault diagnosis', 'Repair and replacement support', 'Performance verification', 'Service report'], null, 30, true],
                ['hospital', 'Equipment Calibration Support', 'equipment-calibration-support', 'Calibration coordination and performance verification for biomedical equipment.', 'We help hospitals maintain dependable measurements by coordinating calibration support and documenting verification activities for applicable equipment.', 'fa-gauge-high', null, ['Calibration planning', 'Measurement verification', 'Documentation support', 'Follow-up scheduling'], null, 40, true],
                ['hospital', 'Hospital Biomedical Consultancy', 'hospital-biomedical-consultancy', 'Practical biomedical guidance for hospital equipment and service planning.', 'From equipment planning to maintenance workflows, we provide practical consultancy tailored to hospital biomedical operations.', 'fa-user-doctor', null, ['Equipment planning', 'Maintenance workflow guidance', 'Technical assessment', 'Procurement support'], null, 50, true],
                ['other', 'Medical Equipment Supply', 'medical-equipment-supply', 'Reliable supply support for medical and biomedical equipment.', 'We support hospitals, clinics and healthcare organizations with product sourcing and supply coordination based on their operational requirements.', 'fa-boxes-stacked', null, ['Product sourcing', 'Quotation support', 'Supply coordination', 'After-sales assistance'], null, 10, true],
                ['other', 'Spare Parts Supply', 'spare-parts-supply', 'Biomedical spare parts sourcing and supply assistance.', 'We help customers source suitable spare parts and accessories for supported medical and biomedical equipment.', 'fa-gears', null, ['Part identification', 'Sourcing assistance', 'Compatibility checking', 'Delivery coordination'], null, 20, true],
                ['other', 'Biomedical Training', 'biomedical-training', 'Practical training support for equipment users and technical teams.', 'We can arrange practical orientation and training focused on safe operation, basic care and maintenance practices for supported equipment.', 'fa-chalkboard-user', null, ['Equipment orientation', 'Safe-use guidance', 'Basic maintenance practices', 'Q&A support'], null, 30, true],
                ['other', 'Hospital Setup Support', 'hospital-setup-support', 'Support for planning and organizing biomedical equipment requirements.', 'We provide practical support for healthcare facilities that are planning new departments, expanding capacity or reorganizing equipment requirements.', 'fa-hospital', null, ['Requirement planning', 'Equipment selection support', 'Department setup guidance', 'Implementation coordination'], null, 40, true],
            ];

            foreach ($defaults as $row) {
                DB::table('services')->insert([
                    'service_type' => $row[0], 'title' => $row[1], 'slug' => $row[2],
                    'short_description' => $row[3], 'description' => $row[4], 'icon' => $row[5],
                    'image_path' => $row[6], 'features' => json_encode($row[7]), 'price_note' => $row[8],
                    'sort_order' => $row[9], 'status' => $row[10], 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        // Add the new permissions to an already-running RBAC database as well as fresh installs.
        if (Schema::hasTable('permissions')) {
            $permissions = [
                ['View Services', 'services.view', 'Services'],
                ['Create Services', 'services.create', 'Services'],
                ['Edit Services', 'services.edit', 'Services'],
                ['Delete Services', 'services.delete', 'Services'],
                ['Change Service Status', 'services.status', 'Services'],
                ['View Contact Messages', 'contact-messages.view', 'Contact'],
                ['Manage Contact Messages', 'contact-messages.manage', 'Contact'],
                ['Delete Contact Messages', 'contact-messages.delete', 'Contact'],
            ];

            foreach ($permissions as [$name, $slug, $group]) {
                DB::table('permissions')->updateOrInsert(
                    ['slug' => $slug],
                    ['name' => $name, 'group' => $group, 'description' => null, 'updated_at' => now(), 'created_at' => now()]
                );
            }

            if (Schema::hasTable('roles') && Schema::hasTable('permission_role')) {
                $permissionIds = DB::table('permissions')->whereIn('slug', array_column($permissions, 1))->pluck('id');
                $roleIds = DB::table('roles')->whereIn('slug', ['super-admin', 'admin'])->pluck('id');
                foreach ($roleIds as $roleId) {
                    foreach ($permissionIds as $permissionId) {
                        DB::table('permission_role')->updateOrInsert([
                            'permission_id' => $permissionId,
                            'role_id' => $roleId,
                        ], []);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('permission_role') && Schema::hasTable('permissions')) {
            $ids = DB::table('permissions')->whereIn('slug', [
                'services.view','services.create','services.edit','services.delete','services.status',
                'contact-messages.view','contact-messages.manage','contact-messages.delete',
            ])->pluck('id');
            DB::table('permission_role')->whereIn('permission_id', $ids)->delete();
            DB::table('permissions')->whereIn('id', $ids)->delete();
        }

        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('services');
    }
};
