<?php

use App\Enums\Ticket\TicketHistoryAction;
use App\Enums\Ticket\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use App\Services\BusinessHoursService;
use App\Services\TicketService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

uses(Tests\TestCase::class);

beforeEach(function () {
    Schema::dropIfExists('ticket_histories');
    Schema::dropIfExists('system_tickets');
    Schema::dropIfExists('ost_staff');

    Schema::create('ost_staff', function (Blueprint $table) {
        $table->increments('staff_id');
        $table->string('firstname')->nullable();
        $table->string('lastname')->nullable();
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
        $table->text('description')->nullable();
        $table->unsignedBigInteger('ticket_id');
        $table->unsignedInteger('performed_by');
        $table->timestamp('performed_at');
    });

    DB::table('ost_staff')->insert([
        ['staff_id' => 1, 'firstname' => 'Luis', 'lastname' => 'Solicitante'],
        ['staff_id' => 2, 'firstname' => 'Ana', 'lastname' => 'Responsable'],
        ['staff_id' => 3, 'firstname' => 'Diego', 'lastname' => 'Otro'],
    ]);

    DB::table('system_tickets')->insert([
        'id' => 20,
        'title' => 'Acceso al sistema',
        'type' => 'INCIDENT',
        'description' => 'El acceso ya fue restablecido.',
        'status' => TicketStatus::RESOLVED->value,
        'impact' => 'MEDIUM',
        'urgency' => 'MEDIUM',
        'priority' => 'MEDIUM',
        'requester_id' => 1,
        'responsible_id' => 2,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    config(['services.access_api.key' => 'ticket-close-test-key']);

    $this->ticket = Ticket::query()->findOrFail(20);
    $this->service = new TicketService(Mockery::mock(BusinessHoursService::class));
});

it('permite cerrar el ticket a la persona responsable y registra su identidad', function () {
    $result = $this->service->closeTicket($this->ticket, 2);

    expect($result['ticket']->status)->toBe(TicketStatus::CLOSED->value)
        ->and($this->ticket->fresh()->status)->toBe(TicketStatus::CLOSED->value)
        ->and(DB::table('ticket_histories')->where([
            'ticket_id' => 20,
            'action' => TicketHistoryAction::STATUS_CHANGED->value,
            'performed_by' => 2,
            'description' => 'Cerrado el ticket',
        ])->exists())->toBeTrue();
});

it('rechaza el cierre cuando quien lo solicita no es el responsable asignado', function () {
    expect(fn() => $this->service->closeTicket($this->ticket, 3))
        ->toThrow(BadRequestException::class, 'Solo el responsable del ticket puede cerrarlo.');

    expect($this->ticket->fresh()->status)->toBe(TicketStatus::RESOLVED->value)
        ->and(DB::table('ticket_histories')->count())->toBe(0);
});

it('expone el cierre en la vista web solamente a la persona responsable', function () {
    $this->actingAs(User::query()->findOrFail(3))
        ->post('/tickets/20/close')
        ->assertRedirect();

    expect($this->ticket->fresh()->status)->toBe(TicketStatus::RESOLVED->value)
        ->and(DB::table('ticket_histories')->count())->toBe(0);

    $this->actingAs(User::query()->findOrFail(2))
        ->post('/tickets/20/close')
        ->assertRedirect();

    expect($this->ticket->fresh()->status)->toBe(TicketStatus::CLOSED->value)
        ->and(DB::table('ticket_histories')->where([
            'ticket_id' => 20,
            'performed_by' => 2,
        ])->count())->toBe(1);
});

it('exige el identificador del responsable en la API', function () {
    $this->withHeaders([
        'Accept' => 'application/json',
        'X-API-Key' => 'ticket-close-test-key',
    ])->postJson('/api/tickets/20/close')
        ->assertUnprocessable()
        ->assertJsonValidationErrors('responsible_id');
});

it('cierra por API solamente cuando el ID corresponde al responsable asignado', function () {
    $headers = [
        'Accept' => 'application/json',
        'X-API-Key' => 'ticket-close-test-key',
    ];

    $this->withHeaders($headers)
        ->postJson('/api/tickets/20/close', ['responsible_id' => 3])
        ->assertBadRequest()
        ->assertJsonPath('error', 'Solo el responsable del ticket puede cerrarlo.');

    $this->withHeaders($headers)
        ->postJson('/api/tickets/20/close', ['responsible_id' => 2])
        ->assertOk()
        ->assertJsonPath('message', 'Cerrado el ticket')
        ->assertJsonPath('data.status', TicketStatus::CLOSED->value)
        ->assertJsonPath('data.responsible_id', 2);

    expect(DB::table('ticket_histories')->where([
        'ticket_id' => 20,
        'performed_by' => 2,
    ])->count())->toBe(1);
});
