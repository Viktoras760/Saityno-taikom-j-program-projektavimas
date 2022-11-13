<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Lesson;
use Validator;
use App\Http\Controllers\AuthController;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    // New user registration (adding to database)
    function addUser(Request $req)
    {
        $code = \App\Models\User::where('Personal_code', '=', $req->input('Personal_code'))->get();
        if(count($code) > 0)
        {
            return response()->json(['message' => 'User with such personal code already exist'], 400);
        }
        if($req->input('Personal_code') < 30000000000)
        {
            return response()->json(['message' => 'Invalid personal code'], 400);
        }
        //|regex:/^[a-zA-ZÑñ\s]+$/ 
        //Lithuanian simbol problem with only letters regex
        $validator = Validator::make($req->all(), [
            'Name' => 'required|string|max:255',
            'Surname' => 'required|string|max:255',
            'email' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $user = new User;
        $user->Name= $req->input('Name');
        $user->Surname= $req->input('Surname');
        $user->Personal_code= $req->input('Personal_code');
        $user->email= $req->input('email');
        $user->password= Hash::make($req->input('password'));
        $user->save();
        return $user;
    }


    /*function getAllUnconfirmed()
    {
        $unconfirmed = \App\Models\User::where('user.Confirmation','=','Unconfirmed')->get();

        if (count($uncomfirmed) < 1) {
            return response()->json(['message' => 'Unconfirmed registration requests not found'], 404);
        }
        return $uncomfirmed;
    }*/

    /*function confirmRegistrationRequest($id, Request $request)
    {
        $user = \App\Models\User::find($id);

        $user->update([
            'fk_Schoolid_School' => $request->school,
            'Confirmation' => $request->Confirmation,
            'Grade' => $request->Grade
        ]);

        return response()->json(['success' => 'User updated successfully']);
    }*/

    public function declineRegistrationRequest($id)
    {
        $role = (new AuthController)->authRole();
        if($role != 'System Administrator')
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to do that',
            ], 401);
        }
        $user = \App\Models\User::find($id);
        if ($user->Confirmation != 'Unconfirmed')
        {
            return response()->json(['message' => 'User is already confirmed or declined'], 200);
        }
        User::where('id_User',$id)->update(['Confirmation'=>'Declined']);
        return response()->json(['message' => 'Registration declined'], 200);
    }

    function getAllUsers(Request $request)
    {
        $role = (new AuthController)->authRole();
        if($role != 'System Administrator')
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to do that',
            ], 401);
        }
        if ($request->Confirmation)
        {
            $users = \App\Models\User::where('user.Confirmation','=',$request->Confirmation)->get();
            return $users;
        }
        else if (\App\Models\User::where('user.Confirmation','=',$request->Confirmation)->get() == NULL)
        {
            return response()->json(['message' => 'Users with this filter are missing'], 404);
        }
        else if (!$request->Confirmation && count($request->all()) > 1)
        {
            return response()->json(['message' => 'This filter is not implemented yet'], 404);
        }
        $users = \App\Models\User::all();
        return $users;
    }

    function deleteUser($id)
    {
        $role = (new AuthController)->authRole();
        if($role != 'System Administrator')
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to do that',
            ], 401);
        }
        $user = \App\Models\User::find($id);

        if ($user == "") {
            return response()->json(['message' => 'User does not exist'], 404);
        }

        $user->delete();
        return response()->json(['success' => 'User deleted']);
    }

    function updateUser($id, Request $request)
    {
        $user = \App\Models\User::find($id);
        $role = (new AuthController)->authRole();
        if (($role == 'Teacher' || $role == 'Pupil') && $id != auth()->user()->id_User)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to update other users',
            ], 401);
        }
        else if ($role == 'School Administrator' && auth()->user()->fk_Schoolid_School != $user->fk_Schoolid_School)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'No rights to update users from this school',
            ], 401);
        }
        $user = \App\Models\User::find($id);
        if(!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $contains = Str::contains($request->Email, '@');
        if (!$contains)
        {
            return response()->json(['failure' => 'Invalid email entered']);
        }
        $user->update([
            'Name' => $request->Name,
            'Surname' => $request->Surname,
            'Personal_code' => $request->Personal_code,
            'Email' => $request->Email,
            'Grade' => $request->Grade,
            'Password' => Hash::make($request->Password),
            'Confirmation' => $request->Confirmation,
            'fk_Schoolid_School' => $request->fk_Schoolid_School,
            'Role' => $request->Role
        ]);
        return response()->json(['success' => 'User updated successfully']);
    }



}
