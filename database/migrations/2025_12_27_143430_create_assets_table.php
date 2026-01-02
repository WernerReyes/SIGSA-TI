<?php

use App\Enums\Asset\AssetStatus;
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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable();
            $table->enum('status', AssetStatus::values());
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('processor')->nullable();
            $table->string('ram')->nullable();
            $table->string('storage')->nullable();
            $table->string('phone')->nullable();
            $table->string('imei')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiration')->nullable();
            $table->boolean('is_new')->default(true);

            $table->unsignedBigInteger('type_id')->nullable();
            $table->foreign('type_id')->references('id')->on('assets_type');

            // $table->unsignedInteger('assigned_to_id')->nullable();
            // $table->foreign('assigned_to_id')->references('staff_id')->on('ost_staff');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
