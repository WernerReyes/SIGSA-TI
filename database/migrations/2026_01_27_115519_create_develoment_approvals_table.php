<?php

use App\Enums\DevelopmentRequest\DevelopmentApprovalLevel;
use App\Enums\DevelopmentRequest\DevelopmentApprovalStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('development_approvals', function (Blueprint $table) {
            $table->id();
            $table->enum('level', DevelopmentApprovalLevel::values());
            $table->enum('status', DevelopmentApprovalStatus::values());
            $table->text('comments')->nullable();
            $table->unsignedBigInteger('development_request_id');
            $table->foreign('development_request_id')->references('id')->on('development_requests');
            $table->unsignedInteger('approved_by_id');
            $table->foreign('approved_by_id')->references('staff_id')->on('ost_staff');
            $table->timestamps();
            $table->unique(['level', 'development_request_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('development_approvals');
    }
};
