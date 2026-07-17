<?php

use App\Enums\Ticket\TicketCategory;
use App\Enums\Ticket\TicketHistoryAction;
use App\Enums\Ticket\TicketImpact;
use App\Enums\Ticket\TicketPriority;
use App\Enums\Ticket\TicketStatus;
use App\Enums\Ticket\TicketType;
use App\Enums\Ticket\TicketUrgency;
use App\Mail\TicketAssignedMail;
use App\Models\Ticket;
use App\Models\User;
use App\Services\BusinessHoursService;
use App\Services\TicketService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

uses(Tests\TestCase::class);

beforeEach(function () {
    Schema::create('ost_staff', function (Blueprint $table) {
        $table->increments('staff_id');
        $table->unsignedInteger('dept_id')->nullable();
        $table->string('firstname')->nullable();
        $table->string('lastname')->nullable();
        $table->string('email')->nullable();
        $table->boolean('activo')->default(true);
    });

    Schema::create('ost_department', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
    });

    Schema::create('system_tickets', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('type');
        $table->text('description');
        $table->string('category')->nullable();
        $table->string('status');
        $table->string('impact');
        $table->string('urgency');
        $table->string('priority');
        $table->json('images')->nullable();
        $table->unsignedInteger('requester_id');
        $table->unsignedInteger('responsible_id')->nullable();
        $table->timestamp('sla_response_due_at')->nullable();
        $table->timestamp('sla_resolution_due_at')->nullable();
        $table->timestamp('first_response_at')->nullable();
        $table->timestamp('resolved_at')->nullable();
        $table->timestamp('sla_paused_at')->nullable();
        $table->decimal('sla_paused_duration', 8, 2)->default(0);
        $table->boolean('sla_breached')->default(false);
        $table->timestamps();
    });

    Schema::create('ticket_histories', function (Blueprint $table) {
        $table->id();
        $table->string('action');
        $table->text('description');
        $table->unsignedBigInteger('ticket_id');
        $table->unsignedInteger('performed_by');
        $table->timestamp('performed_at');
    });

    DB::table('ost_staff')->insert([
        [
            'staff_id' => 1,
            'dept_id' => 10,
            'firstname' => 'Ana',
            'lastname' => 'Asignadora',
            'email' => 'ana@example.com',
        ],
        [
            'staff_id' => 2,
            'dept_id' => 10,
            'firstname' => 'Diego',
            'lastname' => 'Inicial',
            'email' => 'diego@example.com',
        ],
        [
            'staff_id' => 3,
            'dept_id' => 10,
            'firstname' => 'Rosa',
            'lastname' => 'Nueva',
            'email' => 'rosa@example.com',
        ],
        [
            'staff_id' => 4,
            'dept_id' => 10,
            'firstname' => 'Luis',
            'lastname' => 'Solicitante',
            'email' => 'luis@example.com',
        ],
    ]);

    DB::table('ost_department')->insert([
        'id' => 10,
        'name' => 'Sistemas',
    ]);

    DB::table('system_tickets')->insert([
        'id' => 20,
        'title' => 'Sin acceso al sistema',
        'type' => TicketType::INCIDENT->value,
        'description' => 'El usuario no puede iniciar sesión.',
        'category' => TicketCategory::ACCESS->value,
        'status' => TicketStatus::OPEN->value,
        'impact' => TicketImpact::MEDIUM->value,
        'urgency' => TicketUrgency::HIGH->value,
        'priority' => TicketPriority::HIGH->value,
        'requester_id' => 4,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs(User::query()->findOrFail(1));
    $this->ticket = Ticket::query()->findOrFail(20);
    $this->service = new TicketService(Mockery::mock(BusinessHoursService::class));

    Mail::fake();
});

it('envia un correo al responsable cuando se le asigna el ticket', function () {
    $result = $this->service->assignTicket($this->ticket, 2);

    Mail::assertSent(TicketAssignedMail::class, function (TicketAssignedMail $mail) {
        expect($mail->render())
            ->toContain('Ana Asignadora')
            ->toContain('Diego Inicial')
            ->toContain('Nuevo ticket asignado');

        return $mail->hasTo('diego@example.com')
            && $mail->responsible->staff_id === 2
            && $mail->assignedBy->staff_id === 1
            && $mail->previousResponsible === null;
    });
    Mail::assertSentCount(1);

    expect($result['responsible']->staff_id)->toBe(2)
        ->and($this->ticket->fresh()->responsible_id)->toBe(2)
        ->and(DB::table('ticket_histories')->where([
            'ticket_id' => 20,
            'action' => TicketHistoryAction::RESPONSIBLE_CHANGED->value,
            'performed_by' => 1,
        ])->exists())->toBeTrue();
});

it('envia un correo al nuevo responsable cuando el ticket se reasigna', function () {
    $this->service->assignTicket($this->ticket, 2);
    $ticket = $this->ticket->fresh();
    Mail::fake();

    $result = $this->service->assignTicket($ticket, 3);

    Mail::assertSent(TicketAssignedMail::class, function (TicketAssignedMail $mail) {
        expect($mail->render())
            ->toContain('Ana Asignadora')
            ->toContain('Rosa Nueva')
            ->toContain('Diego Inicial')
            ->toContain('Ticket reasignado');

        return $mail->hasTo('rosa@example.com')
            && $mail->responsible->staff_id === 3
            && $mail->assignedBy->staff_id === 1
            && $mail->previousResponsible?->staff_id === 2;
    });
    Mail::assertSentCount(1);

    expect($result['responsible']->staff_id)->toBe(3)
        ->and($ticket->fresh()->responsible_id)->toBe(3);
});
