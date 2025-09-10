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
        Schema::table('interest_levels', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->text('description')->nullable()->after('name');
            $table->integer('value')->unsigned()->after('description');
            $table->string('color')->after('value');
            $table->index('value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interest_levels', function (Blueprint $table) {
            $table->dropIndex(['value']);
            $table->dropColumn(['name', 'description', 'value', 'color']);
        });
    }
};
