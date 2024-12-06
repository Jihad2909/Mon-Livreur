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
        Schema::create('livreuravis', function (Blueprint $table) {
            $table->id();
            $table->integer('idlivreur')->nullable();
            $table->integer('idclient')->nullable();
            $table->integer('idcoli')->nullable();
            $table->double('avis')->nullable();
            $table->string('message')->nullable();
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
        Schema::dropIfExists('livreuravis');
    }
};
