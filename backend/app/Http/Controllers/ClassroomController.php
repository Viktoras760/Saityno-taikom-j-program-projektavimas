<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Floor;
use App\Models\School;
use App\Models\Classroom;
use App\Models\Lesson;
use Validator;
use App\Http\Controllers\AuthController;

class ClassroomController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    function addClassroom(Request $req, $idSchool, $idFloor)
    {

        $role = (new AuthController)->authRole();
        if($role != 'System Administrator' && $role != 'School Administrator')
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to do that',
            ], 401);
        }

        $floor = \App\Models\Floor::find($idFloor);
        $school = \App\Models\School::find($idSchool);

        if ($role == 'School Administrator' && $school->id_School != auth()->user()->fk_Schoolid_School)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to add classroom at this school',
            ], 401);
        }
        $schoolsFloor = \App\Models\Floor::where('fk_Schoolid_School', '=', $idSchool)->where('id_Floor', '=', $idFloor)->get();
        $classroomEx = \App\Models\Classroom::where('fk_Floorid_Floor', '=', $idFloor)->where('Number', '=', $req->Number)->get();

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

        if (count($classroomEx) > 0)
        {
            return response()->json(['error' => 'Classroom with such number already exists on this floor'], 404);
        }

        $validator = Validator::make($req->all(), [
            'Number' => 'required|integer|max:100000|min:1',
            'Pupil_capacity' => 'required|integer|max:500|min:1'

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
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

        $role = (new AuthController)->authRole();
        if($role != 'System Administrator' && $role != 'School Administrator')
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to do that',
            ], 401);
        }
        $floor = \App\Models\Floor::find($idFloor);
        $school = \App\Models\School::find($idSchool);
        if ($role == 'School Administrator' && $school->id_School != auth()->user()->fk_Schoolid_School)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to update classrooms in this school',
            ], 401);
        }

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
        $role = (new AuthController)->authRole();
        $floor = \App\Models\Floor::find($idFloor);
        $school = \App\Models\School::find($idSchool);
        if (($role == 'School Administrator' || $role == 'Teacher' || $role == 'Pupil') && $school->id_School != auth()->user()->fk_Schoolid_School)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to get classroom in this school',
            ], 401);
        }
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
            return response()->json(['error' => 'Floor is in another school'], 404);
        }
        if (count($FloorsClassroom) < 1)
        {
            return response()->json(['error' => 'Classroom is on another floor'], 404);
        }
        return $classroom;
    }

    function deleteClassroom($idSchool, $idFloor, $idClassroom)
    {
        $role = (new AuthController)->authRole();
        if($role != 'System Administrator' && $role != 'School Administrator')
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to do that',
            ], 401);
        }
        $floor = \App\Models\Floor::find($idFloor);
        $school = \App\Models\School::find($idSchool);
        if ($role == 'School Administrator' && $school->id_School != auth()->user()->fk_Schoolid_School)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to delete classrooms in this school',
            ], 401);
        }
        $lesson = \App\Models\Lesson::where('fk_Classroomid_Classroom', '=', $idClassroom)->get();
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
        if (count($lesson) > 1)
        {
            return response()->json(['error' => 'Classroom has lesson(s). Cannot delete', $lesson], 404);
        }

        $classroom->delete();
        return response()->json(['success' => 'Classroom deleted']);
    }

    function getClassroomByFloor($idSchool, $idFloor)
    {
        $role = (new AuthController)->authRole();
        if($role != 'System Administrator' && $role != 'School Administrator')
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to do that',
            ], 401);
        }
        $classrooms = \App\Models\Classroom::where('classroom.fk_Floorid_Floor','=',$idFloor)->get();

        $floor = \App\Models\Floor::find($idFloor);
        $school = \App\Models\School::find($idSchool);
        if (($role == 'School Administrator' || $role == 'Teacher' || $role == 'Pupil') && $school->id_School != auth()->user()->fk_Schoolid_School)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to get classrooms in this school',
            ], 401);
        }
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


