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
        Schema::create('coliinfolivraisons', function (Blueprint $table) {
            $table->id();
            $table->integer('idcoli')->nullable();
            $table->integer('idinfoenvoyeur')->nullable();
            $table->integer('idinforecepture')->nullable();
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
        Schema::dropIfExists('coliinfolivraison');
    }
};
