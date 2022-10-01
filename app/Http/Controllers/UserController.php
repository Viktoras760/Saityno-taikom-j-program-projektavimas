<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    //Register, Login and Authentication method controller

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

}
