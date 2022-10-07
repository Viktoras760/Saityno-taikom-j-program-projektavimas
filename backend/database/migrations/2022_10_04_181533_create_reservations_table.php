<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reservation', function (Blueprint $table) {

		$table->string('Lessons_name');
		$table->string('Reservation_period');
		$table->integer('id_Reservation',true);
        $table->integer('Lower_grade_limit');
        $table->integer('Upper_grade_limit');
		$table->integer('fk_Classroomid_Classroom');
		$table->integer('fk_Userid_User');
    

        });
    }

    public function down()
    {
        Schema::dropIfExists('reservation');
    }
};