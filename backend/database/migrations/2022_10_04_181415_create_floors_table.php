<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFloorTable extends Migration
{
    public function up()
    {
        Schema::create('floor', function (Blueprint $table) {

		$table->integer('Class_number',5);
		$table->tinyInteger('Sport_equipment',1);
		$table->integer('id_Floor',20);
		$table->integer('fk_Schoolid_School',10);

        });
    }

    public function down()
    {
        Schema::dropIfExists('floor');
    }
};
