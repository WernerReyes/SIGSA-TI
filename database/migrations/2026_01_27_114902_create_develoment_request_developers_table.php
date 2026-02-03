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
        Schema::create('development_request_developers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('development_request_id');
            $table->unsignedInteger('developer_id');

            $table->foreign('development_request_id')
                ->references('id')
                ->on('development_requests');

            $table->foreign('developer_id')
                ->references('staff_id')
                ->on('ost_staff');
            
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('development_request_developers');
    }
};
