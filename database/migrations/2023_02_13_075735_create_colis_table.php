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
        Schema::create('colis', function (Blueprint $table) {
            $table->id();
            $table->integer('iduser')->nullable();
            $table->string('description')->nullable();
            $table->string('refcoli')->unique();
            $table->integer('idtaillecoli')->nullable();
            $table->integer('idtype')->nullable();
            $table->integer('idprix')->nullable();
            $table->string('status')->nullable();
            $table->integer('iddatecoli')->nullable();
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
        Schema::dropIfExists('colis');
    }
};
