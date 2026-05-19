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
        Schema::table('loan_details', function (Blueprint $table) {
            $table->string('condition')->nullable()->after('quantity')->comment('Kondisi barang saat dikembalikan: baik, rusak, hilang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_details', function (Blueprint $table) {
            $table->dropColumn('condition');
        });
    }
};
