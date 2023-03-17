<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\SystemFile;
use App\Models\FileComment;
use App\Models\LibraryFile;
use App\Models\Notification;
use App\Models\PersonalFile;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Events\SendNotification;
use App\Events\PersonalFileComment;
use App\Repositories\Files\Library;
use App\Http\Controllers\Controller;
use App\Repositories\Files\Personal;
use App\Repositories\Files\FileUtils;
use Illuminate\Validation\ValidationException;

class FileCommentController extends Controller
{

    /**
     * Validate, Create, Update Comment and Reply
     */
    public function store(Request $request)
    {
        $commentable_type = $this->validateComment($request);
        try {
            if ($request->action == "store") {
                $comment = $this->createCommentOrReply($request->all(), $commentable_type);
                $this->dispatchCommentEvent($comment);
                $response = ['store' => true, 'comment' => $comment];
                return response()->json($response, Response::HTTP_CREATED);
            }
            $update_result =  $this->updateCommentOrReply($request->text, $request->comment_id);
            return response($update_result, $update_result["code"]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }


    /**
     * Validate Comment or Reply
     */
    private function validateComment(Request $request)
    {
        $request->validate([
            "type" => ["required", "string", "in:library,personal,system"],
            "comment_id" => ["nullable", "uuid", "exists:file_comments,id"],
            "action" => ["required", "string", "in:store,update"],
            "commentable_id" => ["nullable", "uuid"],
            "text" => ["required", "min:1", "max:300"],
            "action_type" => ["required", "string", "in:reply,comment"],
        ]);
        if ($request->type == "personal") {
            $isExists = PersonalFile::where("id", $request->commentable_id)->exists();
            if (!$isExists) {
                throw  ValidationException::withMessages(
                    ['commentable_id' => 'This commentable_id value is incorrect']
                );
            }
            return PersonalFile::class;
        } else if ($request->type == "library") {
            $isExists = LibraryFile::where("id", $request->commentable_id)->exists();
            if (!$isExists) {
                throw  ValidationException::withMessages(
                    ['commentable_id' => 'This commentable_id value is incorrect']
                );
            }
            return LibraryFile::class;
        } else if ($request->type == "system") {
            $isExists = SystemFile::where("id", $request->commentable_id)->exists();
            if (!$isExists) {
                throw  ValidationException::withMessages(
                    ['commentable_id' => 'This commentable_id value is incorrect']
                );
            }
            return SystemFile::class;
        }
    }


    /**
     * Create Comment and Reply
     */
    private function createCommentOrReply(array $attributes, $commentable_type)
    {

        $commentable_id = $attributes["commentable_id"];
        $text = $attributes["text"];
        $comment_id = null;
        if ($attributes["action_type"] == "reply") {
            $comment_id = $attributes["comment_id"];
        }

        $comment = new FileComment();
        $comment->commentable_type = $commentable_type;
        $comment->commentable_id = $commentable_id;
        $comment->comment = $text;
        $comment->user_id = auth()->id();
        $comment->comment_id = $comment_id;
        $comment->save();
        return $comment;
    }

    /**
     *  Dispatch Comment Events and Notifications
     */
    private function dispatchCommentEvent(FileComment $comment)
    {
        $comment->load(
            'replies',
            'replies.user:id,firstname,lastname,image',
            'parent',
            'commentable:id,name,user_id,sharedBy_id,parent_id'
        );
        $user = auth()->user();
        $event = $comment->comment_id ? "reply-new" : "comment-new";
        $action = $comment->comment_id ? "reply" : "comment";
        $data = $comment->toArray();

        PersonalFileComment::dispatch($comment->commentable_id, [$action =>  $data], $event);
        if ($event == "reply-new") {
            // reply
            $notification = Notification::where('description', 'user_reply_to_your_comment')->first();
            $notification2 = Notification::where('description', 'user_also_replied_on_user_comment_on_file')->first();
            $this->sendCmNotif($comment->parent->user_id, $notification, $comment, $user);
            $conditions = [
                ["comment_id", "=", $comment->comment_id],
                ["user_id", "!=",  $user->id]
            ];
            $otherComments = FileComment::where($conditions)->select('id', 'user_id')->groupBy('user_id')->get();
            foreach ($otherComments as $rp) {
                $this->sendCmNotif($rp->user_id, $notification2, $comment, $user);
            }
        } else {
            // comment
            $notification = Notification::where('description', 'user_commented_on_your_file')->first();
            $notification2 = Notification::where('description', 'user_also_commented_on_a_file_commented')->first();
            $this->sendCmNotif($comment->commentable->user_id, $notification, $comment, $user);
            $conditions = [
                ["commentable_id", "=", $comment->commentable_id],
                ["user_id", "!=",  $comment->commentable->user_id]
            ];
            $otherComments = FileComment::where($conditions)->select('id', 'user_id')->groupBy('user_id')->get();
            foreach ($otherComments as $cm) {
                $this->sendCmNotif($cm->user_id, $notification2, $comment, $user);
            }
        }
    }

    private function getCmNMeta($condition, $comment)
    {
        return [
            'system' => $condition ? 'personal-drive' : 'personal-shared',
            'item' => $comment->commentable->id,
            'item_parent' => $comment->commentable->parent_id
        ];
    }

    private function getCmContent($user, $comment)
    {
        return [
            $user->firstname . ' ' . $user->lastname,
            $comment->commentable->name,
        ];
    }

    private function sendCmNotif($user_id, $notification, $comment, $user)
    {
        SendNotification::dispatch(
            $user_id,
            $notification->id,
            [],
            $this->getCmContent($user, $comment),
            $this->getCmNMeta(
                $comment->commentable->sharedBy_id == $user_id,
                $comment
            )
        );
    }



    /**
     * Remove and Dispatch Comment Or Reply
     */
    public function destroy($id)
    {
        $comment = FileComment::with("commentable")->find($id);
        if ($comment) {
            if (!$comment->can_delete) {
                return response()->json(['unauthorized' => true], Response::HTTP_UNAUTHORIZED);
            }
            $event = $comment->comment_id ? "reply-delete" : "comment-delete";
            $comment->delete();
            PersonalFileComment::dispatch($comment->commentable_id, ['id' =>  $id, "comment_id" => $comment->comment_id], $event);
            return response()->json(['result' => true], Response::HTTP_OK);
        }
        return response()->json(['not_found' => true], Response::HTTP_NOT_FOUND);
    }


    /**
     * Update and Dispatch Comment or Reply
     */
    public function updateCommentOrReply(string $text, string $id)
    {
        $comment = FileComment::find($id);
        if ($comment->can_edit) {
            $comment->update(["comment" => $text]);
            $event = $comment->comment_id ? "reply-update" : "comment-update";
            $action = $comment->comment_id ? "reply" : "comment";
            $data = $comment->toArray();
            PersonalFileComment::dispatch($comment->commentable_id, [$action =>  $data], $event);
            $updated = ["updated_at" => $comment->updated_at, "id" => $comment->id];
            return ["update" => true, "comment" => $updated, "code" => 200];
        }
        return ["unauthorized" => true, "code" => 403];
    }

    /**
     * Update Description's of Personal Files, Libray Files and System Files
     */
    public function updateDescriptions(Request $request)
    {
        $request->validate([
            "type" => ["required", "string", "in:personal,library,system"],
            "id" => ["required", "uuid"],
            "description" => ["max:300"],
        ]);
        $type = $request->type;
        $item_id = $request->id;
        $item_description = $request->description;
        if ($type == "personal") {
            $personal = new Personal();
            return $personal->updateDescriptions($item_id, $item_description);
        } else if ($type == "system") {

        } else if ($type == "library") {
            $library  = new Library();
            return $library->updateDescriptions($item_id, $item_description);
        }
        return response()->json(["result" => false,], 404);
    }
}
