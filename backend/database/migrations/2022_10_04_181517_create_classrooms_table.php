<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassroomTable extends Migration
{
    public function up()
    {
        Schema::create('classroom', function (Blueprint $table) {

		$table->integer('Number',5);
		$table->integer('Pupil_capacity',3);
		$table->tinyInteger('Musical_equipment',1)->nullable()->default('NULL');
		$table->tinyInteger('Chemistry_equipment',1)->nullable()->default('NULL');
		$table->tinyInteger('Computers',1)->nullable()->default('NULL');
		$table->integer('id_Classroom',20);
		$table->integer('fk_Floorid_Floor',20);

        });
    }

    public function down()
    {
        Schema::dropIfExists('classroom');
    }
}