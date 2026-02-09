<?php

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
        Schema::create('contract_renewals', function (Blueprint $table) {
            $table->id();
            $table->date('old_end_date');
            $table->date('new_end_date');

                        $table->text('notes')->nullable();

            
            $table->unsignedBigInteger('contract_id');
            $table->foreign('contract_id')
                ->references('id')
                ->on('contracts');

            $table->unsignedInteger('renewed_by_id')->nullable();
            $table->foreign('renewed_by_id')
                ->references('staff_id')
                ->on('ost_staff');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_renewals');
    }
};
