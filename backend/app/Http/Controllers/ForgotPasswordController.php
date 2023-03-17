<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\ResetTime;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{

    // validate user data to email reset password link
    public function sendPasswordResetToken(Request $request)
    {

        $request->validate([
            "email" => ["required", "email", "exists:users,email"]
        ]);

        return $this->emailResetPasswordToken($request->input("email"));
    }



    // send reset link to user email address
    private function emailResetPasswordToken(string $email)
    {
        return DB::transaction(function () use ($email) {
            $resetToken = Str::random(60);
            $resetPassword = [
                "email" => $email,
                "token" => $resetToken,
                'created_at' => Carbon::now(),
            ];

            DB::table('password_resets')->insert($resetPassword);

            Mail::to($email)->send(new \App\Mail\ResetPasswordMail($resetPassword));
            return response()->json(['result' => true], Response::HTTP_OK);
        });
    }


    // check token is exists or not
    private function checkToken(Request $request)
    {
        $email = $request->query->get("email");
        $token = $request->query->get("token");
        if (!$email || !$token) {
            return response()->json([
                "result" => false,
                "message" => "email or token is required"
            ], Response::HTTP_BAD_REQUEST);
        }

        $isExists =  DB::table("password_resets")->where("email", $email)
            ->where("token", $token)->exists();

        return response()->json([
            "result" => $isExists,
        ], $isExists ? Response::HTTP_OK :  Response::HTTP_NOT_FOUND);
    }



    public function resetPassword(Request $request)
    {

        $hasToken = $request->query->has("has-reset-token");
        if ($hasToken) {
            return $this->checkToken($request);
        }

        $request->validate([
            "token" => ["required", "exists:password_resets,token"],
            'email' => ['required', "email", 'exists:password_resets,email', "exists:users,email"],
            'password' => ['required', 'min:6', "same:confirm_password"],
            'confirm_password' => ['required', 'min:6', "same:password"],
        ]);

        return DB::transaction(function () use ($request) {

            $password1 = $request->password;
            $password2 = $request->confirm_password;
            $token = $request->token;
            $tokenData = DB::table('password_resets')->where('token', $token)->first();
            if ($tokenData) {
                $createdDate = strtotime($tokenData->created_at);
                $now = strtotime(Carbon::now());
                $diference = abs($now - $createdDate);
                if ($diference < 300) {
                    $user = User::where('email', $tokenData->email)->first();
                    if ($user) {
                        if ($password1 == $password2) {
                            $user->password = Hash::make($password1);
                            $user->update();
                            DB::table('reset_times')->insert([
                                'user_id' => $user->id,
                                'created_at' => Carbon::now(),
                            ]);
                            $countResets = ResetTime::where('user_id', $user->id)->get();
                            $resetTimes = 0;
                            foreach ($countResets as $count) {
                                $created = strtotime($count->created_at);
                                $nowDate = strtotime(Carbon::now());
                                $dif = abs($nowDate - $created);
                                if ($dif < 3600) {
                                    $resetTimes += 1;
                                }
                            }
                            if ($resetTimes > 3) {
                                $user->status = "warning";
                                $user->update();
                                DB::table('password_resets')->where('email', $user->email)->delete();
                                return response()->json(['Success and Warning' => 'Password Has been reseted more than three times in a hour!', "data" => null], Response::HTTP_OK);
                            }
                            DB::table('password_resets')->where('email', $user->email)->delete();
                            return response()->json(['Success' => 'Password Has been Successfully reseted!', "data" => $user], Response::HTTP_ACCEPTED);
                        }
                    }
                } else {
                    $tokenData->delete();
                    return response()->json(['Failed' => 'Token data expired'], Response::HTTP_BAD_REQUEST);
                }
            } else {
                return response()->json(['Failed' => 'Token data not found'], Response::HTTP_NOT_FOUND);
            }
        });
    }
}
