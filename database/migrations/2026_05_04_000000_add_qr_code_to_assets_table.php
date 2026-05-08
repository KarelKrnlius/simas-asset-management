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
        Schema::table('assets', function (Blueprint $table) {
            $table->string('qr_code', 100)->nullable()->after('code');
            $table->string('brand')->nullable()->after('description');
            $table->string('model')->nullable()->after('brand');
            $table->integer('year')->nullable()->after('model');
            $table->string('serial_number')->nullable()->after('year');
            $table->string('location')->nullable()->after('serial_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['qr_code', 'brand', 'model', 'year', 'serial_number', 'location']);
        });
    }
};
