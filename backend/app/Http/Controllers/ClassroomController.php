<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Floor;
use App\Models\School;
use App\Models\Classroom;
use App\Models\Reservation;

class ClassroomController extends Controller
{
    function addClassroom(Request $req, $idSchool, $idFloor)
    {
        $floor = \App\Models\Floor::find($idFloor);
        $school = \App\Models\School::find($idSchool);

        $schoolsFloor = \App\Models\Floor::where('fk_Schoolid_School', '=', $idSchool)->where('id_Floor', '=', $idFloor)->get();

        if(!$school) {
            return response()->json(['error' => 'School not found'], 404);
        }
        if(!$floor) {
            return response()->json(['error' => 'Floor not found'], 404);
        }

        if (count($schoolsFloor) < 1)
        {
            return response()->json(['error' => 'Floor is in another school. Cannot add classroom'], 404);
        }


        $classroom = new Classroom;
        $classroom->Number= $req->input('Number');
        $classroom->Pupil_capacity= $req->input('Pupil_capacity');
        $classroom->Musical_equipment= $req->input('Musical_equipment');
        $classroom->Chemistry_equipment= $req->input('Chemistry_equipment');
        $classroom->Computers= $req->input('Computers');
        $classroom->fk_Floorid_Floor= $idFloor;
        $classroom->save();
        return $classroom;
    }

    function updateClassroom($idSchool, $idFloor, $idClassroom, Request $request)
    {
        $floor = \App\Models\Floor::find($idFloor);
        $school = \App\Models\School::find($idSchool);
        $classroom = \App\Models\Classroom::find($idClassroom);
        $schoolsFloor = \App\Models\Floor::where('fk_Schoolid_School', '=', $idSchool)->where('id_Floor', '=', $idFloor)->get();
        $FloorsClassroom = \App\Models\Classroom::where('fk_Floorid_Floor', '=', $idFloor)->where('id_Classroom', '=', $idClassroom)->get();
        if(!$school) {
            return response()->json(['error' => 'School not found'], 404);
        }
        if(!$floor) {
            return response()->json(['error' => 'Floor not found'], 404);
        }
        if(!$classroom) {
            return response()->json(['error' => 'Classroom not found'], 404);
        }
        if (count($schoolsFloor) < 1)
        {
            return response()->json(['error' => 'Floor is in another school. Cannot update'], 404);
        }
        if (count($FloorsClassroom) < 1)
        {
            return response()->json(['error' => 'Classroom is on another floor. Cannot update'], 404);
        }
        $classroom->update([
            'Number' => $request->Number,
            'Pupil_capacity' => $request->Pupil_capacity,
            'Musical_equipment' => $request->Musical_equipment,
            'Chemistry_equipment' => $request->Chemistry_equipment,
            'Computers' => $request->Computers
        ]);
        return response()->json(['success' => 'Classroom updated successfully']);
    }

    function getClassroom($idSchool, $idFloor, $idClassroom)
    {
        $floor = \App\Models\Floor::find($idFloor);
        $school = \App\Models\School::find($idSchool);
        $classroom = \App\Models\Classroom::find($idClassroom);
        $schoolsFloor = \App\Models\Floor::where('fk_Schoolid_School', '=', $idSchool)->where('id_Floor', '=', $idFloor)->get();
        $FloorsClassroom = \App\Models\Classroom::where('fk_Floorid_Floor', '=', $idFloor)->where('id_Classroom', '=', $idClassroom)->get();
        if(!$school) {
            return response()->json(['error' => 'School not found'], 404);
        }
        if(!$floor) {
            return response()->json(['error' => 'Floor not found'], 404);
        }
        if(!$classroom) {
            return response()->json(['error' => 'Classroom not found'], 404);
        }
        if (count($schoolsFloor) < 1)
        {
            return response()->json(['error' => 'Floor is in another school. Cannot get it'], 404);
        }
        if (count($FloorsClassroom) < 1)
        {
            return response()->json(['error' => 'Classroom is on another floor. Cannot get it'], 404);
        }
        return $classroom;
    }

    function deleteClassroom($idSchool, $idFloor, $idClassroom)
    {
        $floor = \App\Models\Floor::find($idFloor);
        $school = \App\Models\School::find($idSchool);
        $reservation = \App\Models\Reservation::where('fk_Classroomid_Classroom', '=', $idClassroom)->get();
        $classroom = \App\Models\Classroom::find($idClassroom);
        $schoolsFloor = \App\Models\Floor::where('fk_Schoolid_School', '=', $idSchool)->where('id_Floor', '=', $idFloor)->get();
        $FloorsClassroom = \App\Models\Classroom::where('fk_Floorid_Floor', '=', $idFloor)->where('id_Classroom', '=', $idClassroom)->get();
        if(!$school) {
            return response()->json(['error' => 'School not found'], 404);
        }
        if(!$floor) {
            return response()->json(['error' => 'Floor not found'], 404);
        }
        if(!$classroom) {
            return response()->json(['error' => 'Classroom not found'], 404);
        }
        if (count($schoolsFloor) < 1)
        {
            return response()->json(['error' => 'Floor is in another school. Cannot delete'], 404);
        }
        if (count($FloorsClassroom) < 1)
        {
            return response()->json(['error' => 'Classroom is on another floor. Cannot delete'], 404);
        }
        if (count($reservation) > 1)
        {
            return response()->json(['error' => 'Classroom has reservation(s). Cannot delete', $reservation], 404);
        }

        $classroom->delete();
        return response()->json(['success' => 'Classroom deleted']);
    }

    function getClassroomByFloor($idSchool, $idFloor)
    {
        $classrooms = \App\Models\Classroom::where('classroom.fk_Floorid_Floor','=',$idFloor)->get();

        $floor = \App\Models\Floor::find($idFloor);
        $school = \App\Models\School::find($idSchool);
        $schoolsFloor = \App\Models\Floor::where('fk_Schoolid_School', '=', $idSchool)->where('id_Floor', '=', $idFloor)->get();


        if(!$school) {
            return response()->json(['error' => 'School not found'], 404);
        }
        if(!$floor) {
            return response()->json(['error' => 'Floor not found'], 404);
        }
        if (count($schoolsFloor) < 1)
        {
            return response()->json(['error' => 'Floor is in another school. Cannot delete'], 404);
        }
        if (count($classrooms) < 1) {
            return response()->json(['message' => 'Classrooms not found'], 404);
        }
        return $classrooms;
    }

}


