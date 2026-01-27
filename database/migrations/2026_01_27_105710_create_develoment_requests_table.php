<?php

use App\Enums\DevelopmentRequest\DevelopmentRequestPriority;
use App\Enums\DevelopmentRequest\DevelopmentRequestStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('development_requests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('priority', DevelopmentRequestPriority::values());
            $table->enum('status', DevelopmentRequestStatus::values());
            $table->text('description')->nullable();
            $table->text('impact')->nullable();


            $table->integer('estimated_hours')->nullable();
            $table->date('estimated_end_date')->nullable();

            $table->string('requirement_path')->nullable();

            $table->unsignedInteger('area_id');
            $table->foreign('area_id')->references('id_area')->on('area');

            $table->unsignedInteger('requested_by_id');
            $table->foreign('requested_by_id')->references('staff_id')->on('ost_staff');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('develoment_requests');
    }
};
