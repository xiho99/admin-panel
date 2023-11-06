<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
class AuthController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register','refresh','logout']]);
    }
    public function login(): Response
    {
        $credentials = request(['username', 'password']);
        if (!$token = auth()->attempt($credentials)) {
            return $this->error(null, null, 401);
        }
        return $this->responseToken($token);
    }
    public function register(Request $request): Response
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        $token = Auth::login($user);
        return $this->responseToken($token);
    }
    public function me(): Response
    {
        return $this->success(auth()->user());
    }
    public function logout(): Response
    {
        auth()->logout();
        return $this->success(null);
    }

    public function refresh(): Response
    {
        return $this->responseToken(auth()->refresh());
    }
    protected function responseToken($token): Response
    {
        $data = [
            'access_token' => $token->accessToken,
            'token_type' => 'Bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
//                'expires_at' => Carbon::parse($token->token->expires_at)->toDateString(),
        ];
        return $this->success($data, 0, 200);
    }
}
