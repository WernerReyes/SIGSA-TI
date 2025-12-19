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
        Schema::create('tickets_sistema', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->enum('type', ['INCIDENT', 'SERVICE_REQUEST']);
            $table->text('description');
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->enum('status', ['OPEN', 'IN_PROGRESS', 'RESOLVED','CLOSED'])->default('OPEN');
            $table->enum('priority', ['LOW', 'MEDIUM', 'HIGH', 'URGENT'])->default('MEDIUM');
            $table->enum('request_type', ['HARDWARE', 'SOFTWARE', 'NETWORK', 'OTHER'])->nullable()->default(null);
            //* Foreign key to users table
            $table->unsignedInteger('requester_id');
            $table->foreign('requester_id')->references('staff_id')->on('ost_staff');
            
            $table->unsignedInteger('technician_id')->nullable();
            $table->foreign('technician_id')->references('staff_id')->on('ost_staff');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets_sistema');
    }
};
