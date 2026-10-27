<?php

// [AI] Vygenerovane s pomocou AI (Claude).

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Zjednotenie predvolenej krajiny na "Slovakia" (UI je v anglictine).
 * Predtym sa pouzivali hodnoty "Slovensko" (default v DB) a "United States" (default vo formulari).
 */
return new class extends Migration
{
    private const TABLES = ['user_addresses', 'order_addresses'];

    public function up(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->string('country')->default('Slovakia')->change();
            });

            DB::table($table)
                ->whereIn('country', ['Slovensko', 'United States'])
                ->update(['country' => 'Slovakia']);
        }
    }

    public function down(): void
    {
        // Povodne hodnoty zaznamov sa obnovit nedaju, vraciame len default stlpca.
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->string('country')->default('Slovensko')->change();
            });
        }
    }
};
