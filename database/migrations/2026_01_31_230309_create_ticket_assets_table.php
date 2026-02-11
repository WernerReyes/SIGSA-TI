<?php

use App\Enums\TicketAsset\TicketAssetAction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::create('ticket_assets', function (Blueprint $table) {
        //     $table->id();

        //     // Ticket que justifica la acción
        //     $table->unsignedBigInteger('ticket_id');
        //     $table->foreign('ticket_id')
        //         ->references('id')
        //         ->on('tickets_sistema')
        //         ->cascadeOnDelete();

        //     // Activo involucrado
        //     $table->unsignedBigInteger('asset_id');
        //     $table->foreign('asset_id')
        //         ->references('id')
        //         ->on('assets');

        //     // Assignment real que ocurrió
        //     $table->unsignedBigInteger('asset_assignment_id');
        //     $table->foreign('asset_assignment_id')
        //         ->references('id')
        //         ->on('assets_assignments');

        //     // Tipo de acción
        //     $table->enum('action', TicketAssetAction::values());

        //     // Técnico que ejecutó
        //     $table->unsignedInteger('performed_by');
        //     $table->foreign('performed_by')
        //         ->references('staff_id')
        //         ->on('ost_staff');

        //     // Notas ITIL
        //     $table->text('notes')->nullable();

        //     $table->timestamp('created_at')->useCurrent();

        //     // Evita duplicar el mismo evento
        //     $table->unique(['ticket_id', 'asset_assignment_id', 'action'], 'unique_ticket_asset_action');
        // });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_assets');
    }
};
