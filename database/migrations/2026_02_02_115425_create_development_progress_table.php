<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('development_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('percentage');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unsignedBigInteger('development_request_id');
            $table->foreign('development_request_id')->references('id')->on('development_requests');

            $table->unsignedInteger('performed_by');
            $table->foreign('performed_by')
                ->references('staff_id')
                ->on('ost_staff');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('development_progress');
    }
};
