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
        Schema::table('reservation', function (Blueprint $table) {
            $table->foreign(['fk_Classroomid_Classroom'], 'Reservation_BelongsTo_Classroom')->references(['id_Classroom'])->on('classroom');
            $table->foreign(['fk_Userid_User'], 'Reservation_CreatedBy_User')->references(['id_User'])->on('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservation', function (Blueprint $table) {
            $table->dropForeign('Reservation_BelongsTo_Classroom');
            $table->dropForeign('Reservation_CreatedBy_User');
        });
    }
};
