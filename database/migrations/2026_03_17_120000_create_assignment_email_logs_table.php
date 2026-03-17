<?php

use App\Enums\DeliveryRecord\DeliveryRecordType;
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
        Schema::create('assignment_email_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('assignment_id');
            $table->foreign('assignment_id')->references('id')->on('assets_assignments');

            $table->unsignedBigInteger('delivery_record_id');
            $table->foreign('delivery_record_id')->references('id')->on('delivery_records');

            $table->unsignedInteger('sent_by');
            $table->foreign('sent_by')->references('staff_id')->on('ost_staff');

            $table->enum('document_type', DeliveryRecordType::values());
            $table->string('recipient_email');
            $table->string('subject');
            $table->longText('message');
            $table->text('document_path');
            $table->json('extra_image_names')->nullable();
            $table->timestamp('sent_at');

            $table->index(['assignment_id', 'sent_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_email_logs');
    }
};
