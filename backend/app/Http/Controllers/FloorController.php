<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Floor;
use App\Models\School;
use App\Models\Classroom;
use Illuminate\Support\Collection;
use Validator;

class FloorController extends Controller
{
    function addFloor(Request $req, $idSchool)
    {
        $school = \App\Models\School::find($idSchool);
        if(!$school) {
            return response()->json(['error' => 'School not found'], 404);
        }
        $schoolsFloor = \App\Models\Floor::where('fk_Schoolid_School', '=', $idSchool)->where('Floor_number', '=', $req->Floor_number)->get();
        if(count($schoolsFloor) > 0)
        {
            return response()->json(['error' => 'School already has a floor with such number'], 404);
        }

        $validator = Validator::make($req->all(), [
            'Floor_number' => 'required|integer|max:50',
            'Classroom_amount' => 'required|integer|max:100',
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
        $school = \App\Models\School::find($idSchool);
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
        $school = \App\Models\School::find($idSchool);
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
        $floors = \App\Models\Floor::where('floor.fk_Schoolid_School','=',$idSchool)->get();
        $school = \App\Models\School::find($idSchool);
        if(!$school) {
            return response()->json(['error' => 'School not found'], 404);
        }

        if (count($floors) < 1) {
            return response()->json(['message' => 'Floors not found'], 404);
        }
        return $floors;
    }



}
