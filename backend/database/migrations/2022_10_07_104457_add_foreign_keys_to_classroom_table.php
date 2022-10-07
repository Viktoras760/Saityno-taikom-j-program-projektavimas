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
        Schema::table('classroom', function (Blueprint $table) {
            $table->foreign(['fk_Floorid_Floor'], 'Floor_Has_Classroom')->references(['id_Floor'])->on('floor');
            //$table->foreign(['fk_Reservationid_Reservation'], 'Reservation_BelongsTo_Classroom')->references(['id_Reservation'])->on('reservation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('classroom', function (Blueprint $table) {
            $table->dropForeign('Floor_Has_Classroom');
            //$table->dropForeign('Reservation_BelongsTo_Classroom');
        });
    }
};
