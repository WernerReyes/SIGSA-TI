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
            $table->timestamps();
            $table->string('titulo', 255);
            $table->enum('type', ['INCIDENT', 'REQUEST']);
            $table->text('descripcion');
            $table->enum('status', ['OPEN', 'IN_PROGRESS', 'RESOLVED'])->default('OPEN');
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();


            //* Foreign key to users table
            $table->unsignedInteger('requester_id');
            $table->foreign('requester_id')->references('staff_id')->on('ost_staff');

            $table->unsignedInteger('technician_id')->nullable();
            $table->foreign('technician_id')->references('staff_id')->on('ost_staff');
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
