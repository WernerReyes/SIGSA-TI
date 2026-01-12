<?php

use App\Enums\AssetAssignment\ReturnReason;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assets_assignments', function (Blueprint $table) {
            $table->id();

            $table->date('assigned_at');
            $table->dateTime('returned_at')->nullable();


            $table->text('comment')->nullable();
            $table->text('return_comment')->nullable();

            $table->enum('return_reason', ReturnReason::values())->nullable();

            $table->foreignId('asset_id')->references('id')->on('assets');

            $table->unsignedBigInteger('parent_assignment_id')->nullable();
            $table->foreign('parent_assignment_id')->references('id')->on('assets_assignments');
    
            $table->unsignedInteger('responsible_id')->nullable();
            $table->foreign('responsible_id')->references('staff_id')->on('ost_staff');
            $table->index('responsible_id');

            $table->unsignedInteger('assigned_to_id');
            $table->foreign('assigned_to_id')->references('staff_id')->on('ost_staff');
            $table->index('assigned_to_id');
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::dropIfExists('assets_assignments');
    }
};
