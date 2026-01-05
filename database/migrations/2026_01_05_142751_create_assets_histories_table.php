<?php

use App\Enums\AssetHistory\AssetHistoryAction;
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
       Schema::create('assets_histories', function (Blueprint $table) {
            $table->id();
            $table->enum('action', AssetHistoryAction::values());
            $table->text('description')->nullable();

            $table->unsignedBigInteger('asset_id');
            $table->foreign('asset_id')->references('id')->on('assets');

            $table->unsignedBigInteger('related_assignment_id')->nullable();
            $table->foreign('related_assignment_id')->references('id')->on('assets_assignments');

            $table->unsignedInteger('performed_by');

            $table->foreign('performed_by')->references('staff_id')->on('ost_staff');
            $table->timestamp('performed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets_histories');
    }
};
