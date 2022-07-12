<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'username'    => 'required|string',
                'password' => 'required|string|',
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [$validator->errors()],
                Response::HTTP_BAD_REQUEST
            );
        }

        $user = User::create(
            array_merge(
                $validator->validated(),
                ['password' => bcrypt($request->password)]
            )
        );

        return response()->json(['message' => 'Success']);

    }


    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'username'    => 'required|string',
                'password' => 'required|string',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $token_validity = (24 * 60);

        $this->guard()->factory()->setTTL($token_validity);

        if (!$token = $this->guard()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->respondWithToken($token);

    }


    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Logged out!']);

    }


    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());

    }

    protected function respondWithToken($token)
    {
        return response()->json(
            [
                'token'          => $token,
                'token_type'     => 'bearer',
                'token_validity' => ($this->guard()->factory()->getTTL() * 60),
            ]
        );

    }

    protected function guard()
    {
        return Auth::guard();
    }
}
