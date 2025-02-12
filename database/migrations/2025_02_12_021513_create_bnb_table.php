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
        Schema::create('bnb', function (Blueprint $table) {
            $table->id(); // Primary Key (PK)
            $table->string('card_no')->unique(); // Unique constraint (U)
            $table->string('email')->unique(); // Unique constraint (U)
            $table->string('last_name');
            $table->string('phone_no');
            $table->string('brand');
            $table->string('mfm_segment')->nullable();
            $table->string('tr_segment')->nullable();
            $table->string('nyss_segment')->nullable();
            $table->date('last_transaction_date')->nullable();
            $table->string('last_visited_store')->nullable();
            $table->integer('remaining_points')->default(0);
            $table->timestamp('points_last_updated')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bnb');
    }
};
