<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Validator;
use JWTAuth;
use App\Models\Lesson;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use App\Http\Controllers\UserController;


class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function loggedIn()
    {
        if (auth()->user())
        return True;
        else return false;
    }

    public function authRole()
    {
        $log = AuthController::loggedIn();
        if ($log == True)
        {
            return auth()->user()->Role;
        }
        else return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized',
        ], 401); 
    }

    public function payloadEncoding($token)
    {
        //token encoding
        try {
            $jwtParts = explode('.', $token);
            if (empty($header = $jwtParts[0]) || empty($payload = $jwtParts[1]) || empty($jwtParts[2])) {
                throw new JwtServiceException('Missing JWT part(s).');
            }
        } catch (Throwable $e) {
            throw new JwtServiceException('Provided JWT is invalid.', $e);
        }
        
        if (
            !($header = base64_decode($header))
            || !($payload = base64_decode($payload))
        ) {
            throw new JwtServiceException('Provided JWT can not be decoded from base64.');
        }
        
        if (
            empty(($header = json_decode($header, true)))
            || empty(($payload = json_decode($payload, true)))
        ) {
            throw new JwtServiceException('Provided JWT can not be decoded from JSON.');
        }

        return $payload;

    }

    public function login(Request $request)
    {
        //validating credentials
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');

        
        //getting user token
        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        //getting user
        $user = \App\Models\User::where('email','=',$request->email)->get();
        $iat = \App\Models\User::where('email','=',$request->email)->get('iat');
        if(!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $payload = AuthController::payloadEncoding($token);

        //Token refresh
        if($payload == NULL || !$iat)
        {
            Auth::attempt();
            $token = Auth::attempt($credentials);
            $payload = AuthController::payloadEncoding($token);
            $user = \App\Models\User::where('email','=',$request->email)->update([
                'iat' => $payload['iat']
            ]);
        }

        $iat = \App\Models\User::where('email','=',$request->email)->find('iat');
        if($payload['iat']!= $iat)
        {
            Auth::attempt();
            $user = \App\Models\User::where('email','=',$request->email)->update([
                'iat' => $payload['iat']
            ]);
        }

        $user = Auth::user();
        return response()->json([
                'status' => 'success',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);

    }

    public function register(Request $request){
        $validator = Validator::make(request(['Name', 'email', 'password']), [
            'Name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:user',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $user = User::create([
            'Name' => $request->Name,
            'Surname' => $request->Surname,
            'Personal_code'=> $request->Personal_code,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function logout()
    {
        auth()->user()->update([
            'iat' => NULL
        ]);
        auth()->logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }
}