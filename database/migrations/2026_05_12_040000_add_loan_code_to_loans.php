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
        Schema::table('loans', function (Blueprint $table) {
            // Tambahkan kolom loan_code untuk kode unik peminjaman
            $table->string('loan_code')
                  ->after('asset_id')
                  ->nullable()
                  ->comment('Kode unik untuk setiap peminjaman');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn('loan_code');
        });
    }
};
