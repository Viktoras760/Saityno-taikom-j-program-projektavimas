<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    // New user registration (adding to database)
    function register(Request $req)
    {
        $user = new User;
        $user->Name= $req->input('Name');
        $user->Surname= $req->input('Surname');
        $user->Personal_code= $req->input('Personal_code');
        $user->Email= $req->input('Email');
        $user->Password= Hash::make($req->input('Password'));
        $user->save();
        return $user;
    }


    function getAllUncomfirmed()
    {
        $uncomfirmed = \App\Models\User::where('user.Confirmation','=','Uncomfirmed')->get();

        if (count($uncomfirmed) < 1) {
            return response()->json(['message' => 'Uncomfirmed registration requests not found'], 404);
        }
        return $uncomfirmed;
    }

    function confirmRegistrationRequest($id, Request $request)
    {
        $user = \App\Models\User::find($id);

        $user->update([
            'fk_Schoolid_School' => $request->school,
            'Confirmation' => $request->Confirmation,
            'Grade' => $request->Grade
        ]);

        return response()->json(['success' => 'User updated successfully']);
    }

    public function declineRegistrationRequest($id)
    {
        User::where('id_User',$id)->update(['Confirmation'=>'Declined']);
        return response()->json(['message' => 'Registration declined'], 200);
    }

    function getAllUsers()
    {
        $users = \App\Models\User::all();

        if ($users == "") {
            return response()->json(['message' => 'Users not found'], 404);
        }
        return $users;
    }

    function deleteUser($id)
    {
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
        if(!$user) {
            return response()->json(['error' => 'User not found'], 404);
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
