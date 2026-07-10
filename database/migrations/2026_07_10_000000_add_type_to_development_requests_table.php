<?php

use App\Enums\DevelopmentRequest\DevelopmentRequestType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('development_requests', function (Blueprint $table) {
            $table->enum('type', DevelopmentRequestType::values())
                ->default(DevelopmentRequestType::NEW_PROJECT->value)
                ->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('development_requests', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
