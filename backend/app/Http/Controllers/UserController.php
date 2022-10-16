<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Lesson;

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
