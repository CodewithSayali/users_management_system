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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_xid');
            $table->unsignedBigInteger('addresstype_xid');
            $table->string('door_street')->nullable();
            $table->string('landmark')->nullable();
            $table->unsignedBigInteger('city_xid');
            $table->unsignedBigInteger('country_xid');
            $table->unsignedBigInteger('state_xid');
            $table->tinyInteger('is_primary')->default(0) ->comment('1: Primary, 0: Not Primary');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_xid')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('addresstype_xid')->references('id')->on('address_types')->cascadeOnDelete();
            $table->foreign('city_xid')->references('id')->on('cities')->cascadeOnDelete();
            $table->foreign('country_xid')->references('id')->on('countries')->cascadeOnDelete();
            $table->foreign('state_xid')->references('id')->on('states')->cascadeOnDelete();

            


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
};
