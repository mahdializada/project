<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function __construct()
    {
        $this->middleware('scope:cnv')->only(['index']);
    }
    // Display a listing of the resource.

    public function index(): JsonResponse
    {
        $states = State::all();
        return response()->json($states);
    }


    // return country states
    public function countryState($countryId)
    {
        $states = State::where("country_id", $countryId)->get();
        return response()->json($states);

    }
}
