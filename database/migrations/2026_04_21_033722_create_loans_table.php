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

<<<<<<< HEAD
            // Relasi user peminjam
=======
            // 🔗 Relasi user peminjam
>>>>>>> origin/feature/ubah-password
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

<<<<<<< HEAD
            // Data peminjaman
            $table->date('borrow_date');
            $table->date('return_date')->nullable();

            // Status
            $table->string('status', 20);

            // Audit trail
=======
            // 📅 Data peminjaman
            $table->date('borrow_date');
            $table->date('return_date')->nullable();

            // 📌 Status 
            $table->string('status', 20);

            // 👤 Audit trail 
>>>>>>> origin/feature/ubah-password
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

<<<<<<< HEAD
            // Timestamp
            $table->timestamps();

            // Soft delete
            $table->softDeletes();
=======
            // ⏱️ Timestamp
            $table->timestamps();

            // 🗑️ Soft delete 
            $table->softDeletes(); 
>>>>>>> origin/feature/ubah-password
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
<<<<<<< HEAD
};
=======
}; 
>>>>>>> origin/feature/ubah-password
