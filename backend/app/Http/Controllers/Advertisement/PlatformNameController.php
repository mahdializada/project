<?php


namespace App\Http\Controllers\Advertisement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Advertisement\PlatformName;

class PlatformNameController extends Controller
{

    public function index()
    {
        $platform_names = PlatformName::all();
        return response()->json($platform_names);
    }

    public function store(Request $request)
    {
        $platformName = new PlatformName;
        $platformName->name = $request->name;
        $platformName->save();
        return $platformName;
    }

    public function update(Request $request, PlatformName $platformName)
    {
        $platformName->name = $request->name;
        $platformName->save();
        return $platformName;
    }

    public function destroy(PlatformName $platformName)
    {
        $platformName->delete();
    }
}
