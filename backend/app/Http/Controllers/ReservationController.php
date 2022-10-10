<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;

class ReservationController extends Controller
{
    function getReservation($id)
    {
        $reservation = \App\Models\Reservation::find($id);
        if(!$reservation) {
            return response()->json(['error' => 'Reservation not found'], 404);
        }
        return $reservation;
    }

    function addReservation(Request $req, $idSchool, $idFloor, $idClassroom)
    {
        $reservation = new Reservation;
        $reservation->Lessons_name= $req->input('Lessons_name');
        $reservation->Reservation_period= $req->input('Reservation_period');
        $reservation->Lower_grade_limit= $req->input('Lower_grade_limit');
        $reservation->Upper_grade_limit= $req->input('Upper_grade_limit');
        $reservation->fk_Classroomid_Classroom= $idClassroom;
        $reservation->fk_Userid_User= $req->input('fk_Userid_User');
        $reservation->save();
        return $reservation;
    }


}
