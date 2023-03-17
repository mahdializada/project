<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomizeTheme;
use Illuminate\Support\Facades\Auth;

class CustomizeThemeController extends Controller
{
    public function store(Request $request)
    {
        // $result=CustomizeTheme::create(['user_id'=>$request->user()->id]);
            $user_theme= CustomizeTheme::where("user_id" , Auth::user()->id)->first();


            if($user_theme == null ){
                $data= $request->all();
                $data['user_id']= Auth::user()->id;

                $result=CustomizeTheme::create($data);
                return response()->json([
                'message' => $result]);
            }else{
                $data= $request->all();
                $user_theme->update($data);
                return response()->json([
                'message' => $user_theme]);
            };
    }

}
