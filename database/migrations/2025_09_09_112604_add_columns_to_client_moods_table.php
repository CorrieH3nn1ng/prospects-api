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
        Schema::table('client_moods', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->text('description')->nullable()->after('name');
            $table->string('emoji')->after('description');
            $table->integer('value')->unsigned()->after('emoji');
            $table->string('color')->after('value');
            $table->index('value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_moods', function (Blueprint $table) {
            $table->dropIndex(['value']);
            $table->dropColumn(['name', 'description', 'emoji', 'value', 'color']);
        });
    }
};
