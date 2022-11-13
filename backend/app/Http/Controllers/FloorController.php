<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Floor;
use App\Models\School;
use App\Models\Classroom;
use Illuminate\Support\Collection;
use Validator;
use App\Http\Controllers\AuthController;

class FloorController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    function addFloor(Request $req, $idSchool)
    {

        $role = (new AuthController)->authRole();
        if($role != 'System Administrator' && $role != 'School Administrator')
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to do that',
            ], 401);
        }


        $school = \App\Models\School::find($idSchool);
        if(!$school) {
            return response()->json(['error' => 'School not found'], 404);
        }

        if ($role == 'School Administrator' && $school->id_School != auth()->user()->fk_Schoolid_School)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to add floors at this school',
            ], 401);
        }

        $schoolsFloor = \App\Models\Floor::where('fk_Schoolid_School', '=', $idSchool)->where('Floor_number', '=', $req->Floor_number)->get();
        if(count($schoolsFloor) > 0)
        {
            return response()->json(['error' => 'School already has a floor with such number'], 404);
        }

        $validator = Validator::make($req->all(), [
            'Floor_number' => 'required|integer|max:50|min:1',
            'Classroom_amount' => 'required|integer|max:100|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $floor = new Floor;
        $floor->Floor_number= $req->input('Floor_number');
        $floor->Classroom_amount= $req->input('Classroom_amount');
        $floor->Sport_equipment= $req->input('Sport_equipment');
        $floor->fk_Schoolid_School= $idSchool;
        $floor->save();
        return $floor;
    }

    function updateFloor($idSchool, $idFloor, Request $request)
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
                'message' => 'No rights to update floors at this school',
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
            return response()->json(['error' => 'Floor is in another school. Cannot update it'], 404);
        }
        $floor->update([
            'Classroom_amount' => $request->Classroom_amount,
            'Sport_equipment' => $request->Sport_equipment
        ]);
        return response()->json(['success' => 'Floor updated successfully']);
    }

    function getFloor($idSchool, $idFloor)
    {
        $role = (new AuthController)->authRole();
        $school = \App\Models\School::find($idSchool);
        if (($role == 'School Administrator' || $role == 'Teacher' || $role == 'Pupil') && $school->id_School != auth()->user()->fk_Schoolid_School)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to get floors at this school',
            ], 401);
        }
        $floor = \App\Models\Floor::find($idFloor);
        $schoolsFloor = \App\Models\Floor::where('fk_Schoolid_School', '=', $idSchool)->where('id_Floor', '=', $idFloor)->get();
        if(!$school) {
            return response()->json(['error' => 'School not found'], 404);
        }
        if(!$floor) {
            return response()->json(['error' => 'Floor not found'], 404);
        }
        if (count($schoolsFloor) < 1)
        {
            return response()->json(['error' => 'Floor is in another school'], 404);
        }
        return $floor;
    }

    function deleteFloor($idSchool, $idFloor)
    {
        $role = (new AuthController)->authRole();
        if($role != 'System Administrator' && $role != 'School Administrator')
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to do that',
            ], 401);
        }
        $school = \App\Models\School::find($idSchool);
        if ($role == 'School Administrator' && $school->id_School != auth()->user()->fk_Schoolid_School)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to delete floors at this school',
            ], 401);
        }
        $floor = \App\Models\Floor::find($idFloor);
        $classroom = \App\Models\Classroom::where('fk_Floorid_Floor', '=', $idFloor)->get();
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
        if (count($classroom) > 0)
        {
            return response()->json(['message' => 'Floor has classroom(s) attached. Delete them first.'], 401);
        }

        $floor->delete();
        return response()->json(['success' => 'Floor deleted']);
    }

    function getFloorBySchool($idSchool)
    {
        $role = (new AuthController)->authRole();
        $floors = \App\Models\Floor::where('floor.fk_Schoolid_School','=',$idSchool)->get();
        $school = \App\Models\School::find($idSchool);
        if (($role == 'School Administrator' || $role == 'Teacher' || $role == 'Pupil') && $school->id_School != auth()->user()->fk_Schoolid_School)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to get floors at this school',
            ], 401);
        }
        if(!$school) {
            return response()->json(['error' => 'School not found'], 404);
        }

        if (count($floors) < 1) {
            return response()->json(['message' => 'Floors not found'], 404);
        }
        return $floors;
    }



}
