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
        Schema::table('assets_assignments', function (Blueprint $table) {
                $table->boolean('returned_together')->nullable()->after('returned_at')->comment('Indica si el activo fue devuelto junto con su asignación principal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets_assignments', function (Blueprint $table) {
            $table->dropColumn('returned_together');
        });
    }
};
