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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();

            // 🔗 Relasi user peminjam
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // 📅 Data peminjaman
            $table->date('borrow_date');
            $table->date('return_date')->nullable();

            // 📌 Status 
            $table->string('status', 20);

            // 👤 Audit trail
            $table->foreignId('created_by')
                  ->nullable()
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();

            $table->foreignId('updated_by')
                  ->nullable()
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();

            $table->foreignId('deleted_by')
                  ->nullable()
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();

            // ⏱️ Timestamp
            $table->timestamps();

            // 🗑️ Soft delete 
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};