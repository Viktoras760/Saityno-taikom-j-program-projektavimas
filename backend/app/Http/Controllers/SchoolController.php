<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\User;
use App\Models\Floor;
use Validator;

class SchoolController extends Controller
{
    
    function updateSchool($id, Request $request)
    {
        $school = \App\Models\School::find($id);
        if(!$school) {
            return response()->json(['error' => 'School not found'], 404);
        }
        $school->update([
            'Name' => $request->Name,
            'Adress' => $request->Adress,
            'Pupil_amount' => $request->Pupil_amount,
            'Teacher_amount' => $request->Teacher_amount
        ]);
        return response()->json(['success' => 'School updated successfully']);
    }

    function getSchool($id)
    {
        $school = \App\Models\School::find($id);
        if(!$school) {
            return response()->json(['error' => 'School not found'], 404);
        }
        return $school;
    }

    function getAllSchools()
    {
        $schools = \App\Models\School::all();

        if (!$schools) {
            return response()->json(['message' => 'Schools not found'], 404);
        }
        return $schools;
    }

    function addSchool(Request $req)
    {
        $schools = \App\Models\School::where('Name', '=', $req->input('Name'))->get();
        $address = \App\Models\School::where('Adress', '=', $req->input('Adress'))->get();
        if(count($schools) > 0 || count($address) > 0)
        {
            return response()->json(['message' => 'School already exist'], 400);
        }

        $validator = Validator::make($req->all(), [
            'Name' => 'required|string|max:255',
            'Adress' => 'required|string|max:255',
            'Pupil_amount' => 'required|integer|max:5000|min:0',
            'Teacher_amount' => 'required|integer|max:1000|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $school = new School;
        $school->Name= $req->input('Name');
        $school->Adress= $req->input('Adress');
        $school->Pupil_amount= $req->input('Pupil_amount');
        $school->Teacher_amount= $req->input('Teacher_amount');
        $school->save();
        return $school;
    }

    function deleteSchool($id)
    {
        $school = \App\Models\School::find($id);
        $user = \App\Models\User::where('user.fk_Schoolid_School','=',$id)->get();
        $floor = \App\Models\Floor::where('floor.fk_Schoolid_School','=',$id)->get();

        if ($school == "") {
            return response()->json(['message' => 'School does not exist'], 404);
        }
        else if (count($user) > 0 || count($floor) > 0)
        {
            return response()->json(['message' => 'School has users or floor attached. Delete them first.'], 400);
        }
        $school->delete();
        return response()->json(['success' => 'School deleted']);
    }
}
