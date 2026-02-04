<?php

use App\Enums\contract\BillingFrequency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contract_billing', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3);
            $table->enum('frequency', BillingFrequency::values())->nullable();
            $table->integer('billing_cycle_days')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->date('next_billing_date')->nullable();

            $table->unsignedBigInteger('contract_id');
            $table->foreign('contract_id')->references('id')->on('contracts');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_billing');
    }
};



