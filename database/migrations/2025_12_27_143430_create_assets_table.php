<?php

use App\Enums\Asset\AssetStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
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
            
            $table->unsignedBigInteger('brand_id');
            $table->foreign('brand_id')->references('id')->on('brands');


            $table->unsignedBigInteger('model_id')->nullable();
            $table->foreign('model_id')->references('id')->on('models');

            $table->string('serial_number')->nullable();
            $table->string('processor')->nullable();
            $table->string('ram')->nullable();
            $table->string('storage')->nullable();
            $table->string('phone')->nullable();
            $table->string('imei')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiration')->nullable();
            $table->string('invoice_path')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_new')->default(true);

            // $table->boolean('is_accessory')->default(false);

            $table->unsignedBigInteger('type_id')->nullable();
            $table->foreign('type_id')->references('id')->on('assets_type');

            $table->unique('serial_number');
            $table->unique('imei');


            // $table->unsignedInteger('assigned_to_id')->nullable();
            // $table->foreign('assigned_to_id')->references('staff_id')->on('ost_staff');

            $table->timestamps();

            $table->index(['type_id', 'status']);
            $table->index(['type_id', 'warranty_expiration']);
            $table->index(['type_id', 'brand_id']);
            


//             CREATE INDEX idx_assets_type_status ON assets(type_id, status);
// CREATE INDEX idx_assets_warranty ON assets(type_id, warranty_expiration);
// CREATE INDEX idx_assets_brand ON assets(type_id, brand);
// CREATE INDEX idx_assignments_active ON assets_assignments(asset_id, returned_at);
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
