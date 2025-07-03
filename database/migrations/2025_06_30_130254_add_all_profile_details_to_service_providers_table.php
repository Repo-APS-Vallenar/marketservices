<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_providers', function (Blueprint $table) {
            // Verifica y añade 'status' si no existe
            if (!Schema::hasColumn('service_providers', 'status')) {
                $table->string('status')->default('pending')->after('user_id');
            }
            // Verifica y añade 'phone_number' si no existe
            if (!Schema::hasColumn('service_providers', 'phone_number')) {
                $table->string('phone_number')->nullable()->after('status');
            }
            // Verifica y añade 'bio' si no existe
            if (!Schema::hasColumn('service_providers', 'bio')) {
                $table->text('bio')->nullable()->after('phone_number');
            }
            // Verifica y añade 'service_areas' si no existe
            if (!Schema::hasColumn('service_providers', 'service_areas')) {
                $table->string('service_areas')->nullable()->after('bio');
            }
            // Verifica y añade 'profile_picture' if it doesn't exist
            if (!Schema::hasColumn('service_providers', 'profile_picture')) {
                $table->string('profile_picture')->nullable()->after('service_areas');
            }
            // Verifica y añade 'certification' si no existe (el nuevo campo opcional)
            if (!Schema::hasColumn('service_providers', 'certification')) {
                $table->string('certification')->nullable()->after('profile_picture'); 
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_providers', function (Blueprint $table) {
            // Elimina las columnas solo si existen para evitar errores al hacer rollback
            if (Schema::hasColumn('service_providers', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('service_providers', 'phone_number')) {
                $table->dropColumn('phone_number');
            }
            if (Schema::hasColumn('service_providers', 'bio')) {
                $table->dropColumn('bio');
            }
            if (Schema::hasColumn('service_providers', 'service_areas')) {
                $table->dropColumn('service_areas');
            }
            if (Schema::hasColumn('service_providers', 'profile_picture')) {
                $table->dropColumn('profile_picture');
            }
            if (Schema::hasColumn('service_providers', 'certification')) {
                $table->dropColumn('certification');
            }
        });
    }
};
