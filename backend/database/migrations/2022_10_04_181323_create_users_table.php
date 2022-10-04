<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTable extends Migration
{
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {

		$table->string('Name');
		$table->string('Surname');
		$table->integer('Personal_code',15)->nullable()->default('NULL');
		$table->string('Email')->nullable()->default('NULL');
		$table->string('Password');
		$table->tinyInteger('Confirmed',1);
		$table->integer('id_User',50);
		$table->integer('fk_Schoolid_School',10)->nullable()->default('NULL');
		$table->enum('Role',['Pupil','Teacher','School Administrator','System Administrator']);

        });
    }

    public function down()
    {
        Schema::dropIfExists('user');
    }
};
