<?php

use App\DTOs\Ticket\TicketDashboardFiltersDto;
use App\Enums\Ticket\TicketCategory;
use App\Enums\Ticket\TicketImpact;
use App\Enums\Ticket\TicketPriority;
use App\Enums\Ticket\TicketStatus;
use App\Enums\Ticket\TicketType;
use App\Enums\Ticket\TicketUrgency;
use App\Services\TicketDashboardService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

uses(Tests\TestCase::class);

beforeEach(function () {
    Carbon::setTestNow('2026-07-10 18:00:00');

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

    DB::table('ost_staff')->insert([
        ['staff_id' => 1, 'firstname' => 'Luis', 'lastname' => 'Solicitante'],
        ['staff_id' => 2, 'firstname' => 'Ana', 'lastname' => 'Técnica'],
        ['staff_id' => 3, 'firstname' => 'Diego', 'lastname' => 'Técnico'],
    ]);

    $base = [
        'type' => TicketType::INCIDENT->value,
        'description' => 'Descripción suficiente para el ticket.',
        'category' => TicketCategory::SOFTWARE->value,
        'impact' => TicketImpact::MEDIUM->value,
        'urgency' => TicketUrgency::MEDIUM->value,
        'priority' => TicketPriority::MEDIUM->value,
        'requester_id' => 1,
        'images' => null,
        'sla_response_due_at' => '2026-07-01 10:00:00',
        'first_response_at' => null,
        'sla_paused_at' => null,
        'sla_paused_duration' => 0,
        'updated_at' => '2026-07-10 12:00:00',
    ];

    DB::table('system_tickets')->insert([
        [...$base,
            'id' => 1,
            'title' => 'Ticket abierto vencido',
            'status' => TicketStatus::OPEN->value,
            'responsible_id' => null,
            'sla_resolution_due_at' => '2026-07-05 18:00:00',
            'resolved_at' => null,
            'sla_breached' => false,
            'created_at' => '2026-07-01 09:00:00',
        ],
        [...$base,
            'id' => 2,
            'title' => 'Ticket resuelto en SLA',
            'status' => TicketStatus::RESOLVED->value,
            'responsible_id' => 2,
            'sla_resolution_due_at' => '2026-07-02 12:00:00',
            'resolved_at' => '2026-07-02 11:00:00',
            'sla_breached' => false,
            'created_at' => '2026-07-02 09:00:00',
        ],
        [...$base,
            'id' => 3,
            'title' => 'Ticket cerrado fuera de SLA',
            'status' => TicketStatus::CLOSED->value,
            'responsible_id' => 2,
            'sla_resolution_due_at' => '2026-07-03 11:00:00',
            'resolved_at' => '2026-07-03 13:00:00',
            'sla_breached' => true,
            'created_at' => '2026-07-03 10:00:00',
        ],
        [...$base,
            'id' => 4,
            'title' => 'Ticket fuera del rango',
            'status' => TicketStatus::IN_PROGRESS->value,
            'responsible_id' => 3,
            'sla_resolution_due_at' => '2026-06-21 18:00:00',
            'resolved_at' => null,
            'sla_breached' => false,
            'created_at' => '2026-06-20 09:00:00',
        ],
    ]);
});

afterEach(function () {
    Carbon::setTestNow();
});

it('calcula el resumen y las series del dashboard de tickets', function () {
    $filters = TicketDashboardFiltersDto::fromArray([
        'start_date' => '2026-07-01',
        'end_date' => '2026-07-10',
    ]);

    $dashboard = app(TicketDashboardService::class)->getDashboard($filters);

    expect($dashboard['summary'])
        ->toMatchArray([
            'total' => 3,
            'active' => 1,
            'resolved' => 2,
            'closed' => 1,
            'unassigned' => 1,
            'sla_breached' => 2,
            'sla_compliance_rate' => 50.0,
            'average_resolution_minutes' => 150,
        ])
        ->and(collect($dashboard['by_status'])->firstWhere('value', TicketStatus::OPEN->value)['count'])->toBe(1)
        ->and(collect($dashboard['by_priority'])->firstWhere('value', TicketPriority::LOW->value)['count'])->toBe(0)
        ->and($dashboard['daily_trend'])->toHaveCount(10)
        ->and(collect($dashboard['daily_trend'])->firstWhere('date', '2026-07-02'))
        ->toMatchArray(['created' => 1, 'resolved' => 1])
        ->and($dashboard['technicians'][0])
        ->toMatchArray([
            'staff_id' => 2,
            'name' => 'Ana Técnica',
            'total' => 2,
            'resolved' => 2,
            'resolution_rate' => 100.0,
        ])
        ->and($dashboard['tickets'])->toHaveCount(3)
        ->and($dashboard['recent_tickets'])->toHaveCount(3)
        ->and($dashboard['recent_tickets'][0]['category'])->toBe(TicketCategory::SOFTWARE->value);
});

it('aplica múltiples responsables a todos los indicadores', function () {
    $filters = TicketDashboardFiltersDto::fromArray([
        'responsible_ids' => [2, 3],
    ]);

    $dashboard = app(TicketDashboardService::class)->getDashboard($filters);

    expect($dashboard['summary'])
        ->toMatchArray([
            'total' => 3,
            'active' => 1,
            'resolved' => 2,
            'unassigned' => 0,
            'sla_breached' => 2,
        ])
        ->and($dashboard['filters']['responsible_ids'])->toBe([2, 3])
        ->and($dashboard['recent_tickets'])->toHaveCount(3);
});

it('aplica múltiples estados a todos los indicadores', function () {
    $filters = TicketDashboardFiltersDto::fromArray([
        'statuses' => [
            TicketStatus::CLOSED->value,
            TicketStatus::RESOLVED->value,
        ],
    ]);

    $dashboard = app(TicketDashboardService::class)->getDashboard($filters);

    expect($dashboard['filters']['statuses'])->toBe([
        TicketStatus::CLOSED->value,
        TicketStatus::RESOLVED->value,
    ])
        ->and($dashboard['summary'])
        ->toMatchArray([
            'total' => 2,
            'active' => 0,
            'resolved' => 2,
            'closed' => 1,
            'unassigned' => 0,
            'sla_breached' => 1,
        ])
        ->and(collect($dashboard['by_status'])->firstWhere('value', TicketStatus::CLOSED->value)['count'])->toBe(1)
        ->and(collect($dashboard['by_status'])->firstWhere('value', TicketStatus::RESOLVED->value)['count'])->toBe(1)
        ->and(collect($dashboard['by_status'])->firstWhere('value', TicketStatus::OPEN->value)['count'])->toBe(0)
        ->and($dashboard['recent_tickets'])->toHaveCount(2);
});

it('expone filtros multiselect mediante la API', function () {
    config(['services.access_api.key' => 'dashboard-test-key']);

    $response = $this
        ->withHeaders([
            'Accept' => 'application/json',
            'X-API-Key' => 'dashboard-test-key',
        ])
        ->get('/api/tickets/dashboard?statuses[]=CLOSED&statuses[]=RESOLVED&responsible_ids[]=2&requester_ids[]=1&types[]=INCIDENT&types[]=SERVICE_REQUEST&categories[]=SOFTWARE&categories[]=ACCESS');

    $response
        ->assertOk()
        ->assertJsonPath('data.filters.statuses.0', TicketStatus::CLOSED->value)
        ->assertJsonPath('data.filters.statuses.1', TicketStatus::RESOLVED->value)
        ->assertJsonPath('data.filters.responsible_ids.0', 2)
        ->assertJsonPath('data.filters.requester_ids.0', 1)
        ->assertJsonPath('data.filters.types.0', TicketType::INCIDENT->value)
        ->assertJsonPath('data.filters.types.1', TicketType::SERVICE_REQUEST->value)
        ->assertJsonPath('data.filters.categories.0', TicketCategory::SOFTWARE->value)
        ->assertJsonPath('data.filters.categories.1', TicketCategory::ACCESS->value)
        ->assertJsonPath('data.summary.total', 2)
        ->assertJsonPath('data.summary.closed', 1)
        ->assertJsonCount(2, 'data.recent_tickets');
});

it('expone el dashboard mediante la API protegida por key', function () {
    config(['services.access_api.key' => 'dashboard-test-key']);

    $response = $this
        ->withHeaders([
            'Accept' => 'application/json',
            'X-API-Key' => 'dashboard-test-key',
        ])
        ->get('/api/tickets/dashboard?start_date=2026-07-01&end_date=2026-07-10');

    $response
        ->assertOk()
        ->assertJsonPath('data.filters.start_date', '2026-07-01')
        ->assertJsonPath('data.filters.end_date', '2026-07-10')
        ->assertJsonPath('data.summary.total', 3)
        ->assertJsonPath('data.summary.sla_breached', 2)
        ->assertJsonCount(10, 'data.daily_trend');
});

it('no aplica filtro de fechas cuando la API no recibe parámetros', function () {
    config(['services.access_api.key' => 'dashboard-test-key']);

    $response = $this
        ->withHeaders([
            'Accept' => 'application/json',
            'X-API-Key' => 'dashboard-test-key',
        ])
        ->get('/api/tickets/dashboard');

    $response
        ->assertOk()
        ->assertJsonPath('data.filters.start_date', null)
        ->assertJsonPath('data.filters.end_date', null)
        ->assertJsonPath('data.filters.statuses', null)
        ->assertJsonPath('data.summary.total', 4)
        ->assertJsonCount(4, 'data.tickets')
        ->assertJsonCount(14, 'data.daily_trend');
});

it('rechaza un rango de fechas inválido en la API', function () {
    config(['services.access_api.key' => 'dashboard-test-key']);

    $response = $this
        ->withHeaders([
            'Accept' => 'application/json',
            'X-API-Key' => 'dashboard-test-key',
        ])
        ->get('/api/tickets/dashboard?start_date=2026-07-10&end_date=2026-07-01');

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors('end_date');
});
