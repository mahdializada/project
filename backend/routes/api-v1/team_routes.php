<?php


use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('export-team-template', [TeamController::class, "exportTeamTemplate"]);
Route::apiResource("teams", TeamController::class);
Route::post("teams/update/{id}", [TeamController::class, "update"]);
Route::post("teams/check-uniqueness", [TeamController::class, "checkUniqueness"]);
Route::post("teams/change-status", [TeamController::class, "changeTeamStatus"]);
Route::post('teams/searchTeam', [TeamController::class, "searchTeam"]);
