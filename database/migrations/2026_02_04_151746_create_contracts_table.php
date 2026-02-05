<?php

use App\Enums\Contract\ContractPeriod;
use App\Enums\Contract\ContractStatus;
use App\Enums\Contract\ContractType;
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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('provider');
            // $table->decimal('total_amount', 10, 2)->nullable();
            $table->enum('type', ContractType::values());
            $table->enum('period', ContractPeriod::values());
            $table->enum('status', ContractStatus::values());
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
