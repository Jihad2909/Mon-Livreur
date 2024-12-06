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
        Schema::create('prixnegocies', function (Blueprint $table) {
            $table->id();
            $table->integer('idcoli')->nullable();
            $table->integer('idclient')->nullable();
            $table->integer('idlivreur')->nullable();
            $table->integer('prixcoli')->nullable();
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
        Schema::dropIfExists('negocieprix');
    }
};
