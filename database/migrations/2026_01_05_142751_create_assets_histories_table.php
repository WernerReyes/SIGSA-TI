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

            $table->text('invoice_path')->nullable();

            $table->unsignedBigInteger('asset_id');
            $table->foreign('asset_id')->references('id')->on('assets');

            $table->unsignedBigInteger('related_delivery_record_id')->nullable();
            $table->foreign('related_delivery_record_id')->references('id')->on('delivery_records');

            $table->unsignedInteger('performed_by');
            $table->foreign('performed_by')->references('staff_id')->on('ost_staff');

            $table->string('performed_by_name')->nullable();
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
