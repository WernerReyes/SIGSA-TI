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
        Schema::create('assets_assignments', function (Blueprint $table) {
            $table->id();
            
            $table->date( 'assigned_at');
            $table->date( 'returned_at')->nullable();
            $table->text( 'comment')->nullable();

            $table->foreignId('asset_id')->unique()->references('id')->on('assets');

            
            $table->unsignedInteger( 'responsible_id' )->nullable();
            $table->foreign('responsible_id')->references('staff_id')->on('ost_staff');
            $table->index('responsible_id');

            $table->unsignedInteger( 'assigned_to_id' );
            $table->foreign('assigned_to_id')->references('staff_id')->on('ost_staff');
            $table->index('assigned_to_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets_assignments');
    }
};
