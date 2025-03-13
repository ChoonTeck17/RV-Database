<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSourceToBnbTable extends Migration
{
    public function up()
    {
        Schema::table('bnb', function (Blueprint $table) {
            $table->string('source')->nullable(); // e.g., 'raw', 'nps', 'rfm'
        });
    }

    public function down()
    {
        Schema::table('bnb', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
}