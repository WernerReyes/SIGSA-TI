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
        Schema::create('assets_histories', function (Blueprint $table) {
            $table->id();
             $table->text('action');
            $table->unsignedBigInteger('asset_id');
            $table->foreign('asset_id')->references('id')->on('assets');
            
            $table->unsignedInteger('performed_by');

            $table->foreign('performed_by')->references('staff_id')->on('ost_staff');
            $table->timestamp('performed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets_histories');
    }
};
