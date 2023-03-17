<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class UserNotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Note::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $attr = $request->validate([
            'note' => 'required|string',
            'users' => 'required'
        ]);
        $user = $request->user();
        try {
            $note = Note::create([
                'note' => $attr['note'],
                'creator_id' => $user->id,
                "updated_by" => $user->id,
            ]);
            foreach ($attr['users'] as $reciver_id) {
                $note->users()->attach($reciver_id);
                $note->save();
            }
            return response()->json(["result" => true, "data" => $note], Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return response()->json(["result" => false, "data" => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Note  $userNote
     * @return \Illuminate\Http\Response
     */
    public function show(Note $userNote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Note  $userNote
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Note $userNote)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Note  $userNote
     * @return \Illuminate\Http\Response
     */
    public function destroy(Note $userNote)
    {
        //
    }
}
