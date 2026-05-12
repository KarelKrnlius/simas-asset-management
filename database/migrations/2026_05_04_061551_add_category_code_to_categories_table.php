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
        Schema::table('categories', function (Blueprint $table) {
            $table->string('category_code', 2)->nullable()->unique()->after('name');
        });

        // Populate existing categories with codes based on ID order
        $categories = DB::table('categories')->orderBy('id')->get();
        foreach ($categories as $index => $category) {
            $code = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            DB::table('categories')->where('id', $category->id)->update(['category_code' => $code]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('category_code');
        });
    }
};
