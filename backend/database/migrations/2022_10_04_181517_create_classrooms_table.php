<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('classroom', function (Blueprint $table) {

		$table->integer('Number');
		$table->integer('Pupil_capacity');
		$table->tinyInteger('Musical_equipment')->nullable();
		$table->tinyInteger('Chemistry_equipment')->nullable();
		$table->tinyInteger('Computers')->nullable();
		$table->integer('id_Classroom',true);
		$table->integer('fk_Floorid_Floor');

        });
    }

    public function down()
    {
        Schema::dropIfExists('classroom');
    }
};