<?php
use App\Enums\Ticket\TicketCategory;
use App\Enums\Ticket\TicketImpact;
use App\Enums\Ticket\TicketPriority;
use App\Enums\Ticket\TicketStatus;
use App\Enums\Ticket\TicketType;
use App\Enums\Ticket\TicketUrgency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_tickets', function (Blueprint $table) {
             $table->id();
            $table->string('title', 255);

            $table->enum('type', TicketType::values());
            $table->text('description');
         

            $table->enum('category', TicketCategory::values())->nullable()->default(null);

            $table->enum('status', TicketStatus::values())->default('OPEN');

            $table->enum('impact', TicketImpact::values());
            $table->enum('urgency', TicketUrgency::values());
            $table->enum('priority', TicketPriority::values());
            
            $table->json('images')->nullable();

            //* Foreign key to users table
            $table->unsignedInteger('requester_id');
            $table->foreign('requester_id')->references(columns: 'staff_id')->on('ost_staff');

            $table->unsignedInteger('responsible_id')->nullable();
            $table->foreign('responsible_id')->references('staff_id')->on('ost_staff');


            $table->timestamp('sla_response_due_at')->nullable();
            $table->timestamp('sla_resolution_due_at')->nullable();

            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('resolved_at')->nullable();

            $table->timestamp('sla_paused_at')->nullable();
            $table->decimal('sla_paused_duration', 8, 2)->default(0); // Duración total en horas

            $table->boolean('sla_breached')->default(false);

            $table->timestamps();
            // $table->id();
            // $table->string('title', 255);
            // $table->enum('type', TicketType::values());
            // $table->text('description');
            // // $table->timestamp('opened_at')->useCurrent();
            // $table->timestamp('closed_at')->nullable();


            // $table->enum('status', TicketStatus::values())->default('OPEN');
            // $table->enum('priority', TicketPriority::values())->default('MEDIUM');
            // $table->enum('request_type', TicketRequestType::values())->nullable()->default(null);

            // //* Foreign key to users table
            // $table->unsignedInteger('requester_id');
            // $table->foreign('requester_id')->references(columns: 'staff_id')->on('ost_staff');

            // $table->unsignedInteger('responsible_id')->nullable();
            // $table->foreign('responsible_id')->references('staff_id')->on('ost_staff');


    //          $table->timestamp('sla_response_due_at')->nullable();
    // $table->timestamp('sla_resolution_due_at')->nullable();

    // $table->timestamp('first_response_at')->nullable();
    // $table->timestamp('resolved_at')->nullable();


            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_tickets');
    }
};


// Schema::create('tickets', function (Blueprint $table) {
//     $table->id();

//     $table->string('code')->unique();

//     $table->foreignId('user_id');

//     $table->enum('type', ['INCIDENT', 'SERVICE_REQUEST']);

//     $table->string('title');
//     $table->text('description');

//     $table->foreignId('service_id')->nullable();
//     $table->foreignId('category_id')->nullable();

//     $table->enum('impact', ['LOW', 'MEDIUM', 'HIGH']);
//     $table->enum('urgency', ['LOW', 'MEDIUM', 'HIGH']);
//     $table->enum('priority', ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL']);

//     $table->enum('status', [
//         'OPEN',
//         'IN_PROGRESS',
//         'ON_HOLD',
//         'RESOLVED',
//         'CLOSED'
//     ]);

//     $table->boolean('is_service_down')->default(false);
//     $table->timestamp('affected_since')->nullable();

//     $table->timestamp('sla_response_due_at')->nullable();
//     $table->timestamp('sla_resolution_due_at')->nullable();

//     $table->timestamps();
// });
