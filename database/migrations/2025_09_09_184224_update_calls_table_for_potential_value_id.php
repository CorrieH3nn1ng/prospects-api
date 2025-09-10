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
        Schema::table('calls', function (Blueprint $table) {
            $table->dropColumn('potential_value');
            $table->unsignedBigInteger('potential_value_id')->nullable()->after('client_satisfaction_level');
            $table->foreign('potential_value_id')->references('id')->on('potential_values');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calls', function (Blueprint $table) {
            $table->dropForeign(['potential_value_id']);
            $table->dropColumn('potential_value_id');
            $table->decimal('potential_value', 15, 2)->nullable()->after('client_satisfaction_level');
        });
    }
};
