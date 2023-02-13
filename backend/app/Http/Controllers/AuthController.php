<?php

namespace App\Http\Controllers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function login(Request $request){

        $request->validate([
            'email' => ['string', 'email'],
            'password' => ['required',
            // 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/'
            ]
        ]);

        // $login = $request -> identity; 
        // $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        // $request->merge([$field => $login]);

        if(!auth()->attempt($request->only('email','password'))){
            throw new AuthenticationException();
        }
        $token = auth()->user()->createToken("user-token");
        return[
            'message'=>['success'],
            'token' => $token->plainTextToken,
        ];
    }

    public function logout(){
        auth()->user()->currentAccessToken()->delete();

        return["message"=>'success'];
    }

    public function user(){
        return new UserResource(auth()->user());
    }
}
