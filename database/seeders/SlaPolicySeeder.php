<?php

namespace Database\Seeders;

use App\Enums\Ticket\TicketPriority;
use App\Models\SlaPolicy;
use Illuminate\Database\Seeder;
use Schema;

class SlaPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        SlaPolicy::truncate();

        SlaPolicy::insert([
            [
                'priority' => TicketPriority::URGENT->value,
                'response_time_minutes' => 15,
                'resolution_time_minutes' => 60,
                // 'is_active' => true,
            ],
            [
                'priority' => TicketPriority::HIGH->value,
                'response_time_minutes' => 30,
                'resolution_time_minutes' => 120,
                // 'is_active' => true,
            ],
            [
                'priority' => TicketPriority::MEDIUM->value,
                'response_time_minutes' => 60,
                'resolution_time_minutes' => 240,
                // 'is_active' => true,
            ],
            [
                'priority' => TicketPriority::LOW->value,
                'response_time_minutes' => 120,
                'resolution_time_minutes' => 480,
                // 'is_active' => true,
            ],
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
