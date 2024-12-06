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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->integer('idphone');
            $table->integer('idadresse');
            $table->integer('idusertype')->nullable();
            $table->integer('idphotoprofile')->nullable();
            $table->integer('idpermis')->nullable();
            $table->integer('idpieceidentite')->nullable();
            $table->integer('idjustification')->nullable();
            $table->integer('status')->default('0')->nullable();
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
        Schema::dropIfExists('user');
    }
};
