<?php

use App\Enums\InfrastructureEvents\InfrastructureEventsType;
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
        Schema::create('infrastructure_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', InfrastructureEventsType::values());
            $table->text('description');
            $table->dateTime('date');
            $table->unsignedInteger('responsible_id');
           $table->foreign('responsible_id')->references('staff_id')->on('ost_staff');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infrastructure_events');
    }
};
