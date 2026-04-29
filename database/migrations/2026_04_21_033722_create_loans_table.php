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
<<<<<<< HEAD
            // Relasi user peminjam
=======
            // 🔗 Relasi user peminjam
>>>>>>> origin/feature/ubah-password
=======
>>>>>>> 5b9805554f3dcdefd7f38340bb9bd8bb4b4864b8
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

<<<<<<< HEAD
<<<<<<< HEAD
            // Data peminjaman
            $table->date('borrow_date');
            $table->date('return_date')->nullable();

            // Status
            $table->string('status', 20);

            // Audit trail
=======
=======
>>>>>>> 5b9805554f3dcdefd7f38340bb9bd8bb4b4864b8
            // 📅 Data peminjaman
            $table->date('borrow_date');
            $table->date('return_date')->nullable();

            // 📌 Status 
            $table->string('status', 20);

            // 👤 Audit trail 
<<<<<<< HEAD
>>>>>>> origin/feature/ubah-password
=======
>>>>>>> 5b9805554f3dcdefd7f38340bb9bd8bb4b4864b8
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
<<<<<<< HEAD
            // Timestamp
            $table->timestamps();

            // Soft delete
            $table->softDeletes();
=======
=======
>>>>>>> 5b9805554f3dcdefd7f38340bb9bd8bb4b4864b8
            // ⏱️ Timestamp
            $table->timestamps();

            // 🗑️ Soft delete 
            $table->softDeletes(); 
<<<<<<< HEAD
>>>>>>> origin/feature/ubah-password
=======
>>>>>>> 5b9805554f3dcdefd7f38340bb9bd8bb4b4864b8
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
<<<<<<< HEAD
};
=======
}; 
>>>>>>> origin/feature/ubah-password
=======
};
>>>>>>> 5b9805554f3dcdefd7f38340bb9bd8bb4b4864b8
