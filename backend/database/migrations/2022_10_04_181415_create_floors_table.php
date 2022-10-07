<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('floor', function (Blueprint $table) {

		$table->integer('Classroom_amount');
		$table->tinyInteger('Sport_equipment');
		$table->integer('id_Floor',true);
		$table->integer('fk_Schoolid_School');

        });
    }

    public function down()
    {
        Schema::dropIfExists('floor');
    }
};
