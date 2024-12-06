<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portfeuilles', function (Blueprint $table) {
            $table->id();
            $table->integer('idlivreur')->nullable();
            $table->double('solde')->default('0')->nullable();
            $table->double('soldebloque')->default('0')->nullable();;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('portfeuille');
    }
};
