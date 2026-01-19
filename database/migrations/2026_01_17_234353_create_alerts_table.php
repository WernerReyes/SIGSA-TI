<?php

use App\Enums\Alert\AlertStatus;
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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->string('entity');
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('type');
            $table->text('message')->nullable();
            $table->enum('status', AlertStatus::values())->default(AlertStatus::ACTIVE->value);
            $table->timestamp('last_notified_at')->nullable();
            $table->json('metadata')->nullable();
            // $table->boolean('is_read')->default(false);

            $table->unique(['entity', 'type']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
