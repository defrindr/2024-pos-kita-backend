<?php

namespace App\Http\Controllers\LMS\Student;

use App\Http\Controllers\Controller;
use App\Models\LMS\Forum;
use App\Models\LMS\Lecturer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ForumController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getForum(Request $request)
    {
        $user = auth()->guard('api')->user();

        $classId = $request->id;

        if (!$classId) {
            return response([
                'message' => "Class id is required",
                'data' => null
            ], 400);
        }
        $forumData = [];

        $forumList = Forum::where("id_class", $classId)->orderBy("created_at", "asc")->get();
        if (!$forumList) {
            return response([
                'message' => "Reply not found",
                'data' => null
            ], 404);
        } else {
            foreach ($forumList as $forum) {
                $forum_user = $forum->id_user;
                $forum_lecturer = $forum->lecturer;

                $data = [
                    'id' => $forum->id,
                    'id_user' => $forum_user,
                    'id_lecturer' => $forum_lecturer,
                    'content' => $forum->content,
                    'created_at' => $forum->created_at
                ];

                if ($forum_user != null) {
                    $user = User::where('id', $forum_user)->first();

                    if (!$user) {
                        $data['user'] = null;
                    } else {
                        $data['user'] = [
                            'id' => $user->id,
                            'owner_name' => $user->owner_name,
                            'umkm_name' => $user->umkm_name,
                            'umkm_image' => $user->imkm_image
                        ];
                    }
                } else {
                    $data['user'] = null;
                }

                if ($forum_lecturer != null) {
                    $lecturer = Lecturer::where("id", $forum_lecturer)->first();
                    if (!$lecturer) {
                        $data['lecturer'] = null;
                    } else {
                        $data['lecturer'] = [
                            'id' => $lecturer->id,
                            'lecturer_code' => $lecturer->lecturer_code,
                            'lecturer_name' => $lecturer->name,
                            'lecturer_image' => $lecturer->url_image
                        ];
                    }
                } else {
                    $data['lecturer'] = null;
                }

                $forumData[] = $data;
            }

            return response([
                'message' => "Success",
                'data' => $forumData
            ], 200);
        }
    }

    public function replyForum(Request $request)
    {
        $user = auth()->guard('api')->user();

        $validator = Validator::make($request->all(), [
            'id_class' => 'required',
            'content' => 'required'
        ]);
        if ($validator->fails()) {
            return response([
                'message' => $validator->errors(),
                'data' => null
            ], 400);
        }

        $forum = new Forum();

        $forum->id_user = $user->id;
        $forum->content = $request->content;
        $forum->id_class = $request->id_class;

        if ($forum->save()) {
            return response([
                'message' => 'Success',
                'data' => $forum->id
            ], 201);
        } else {
            return response([
                'message' => 'Failed to reply forum',
                'data' => null
            ], 400);
        }
    }

    public function deleteForumReply(Request $request)
    {
        $user = auth()->guard('api')->user();

        $replyId = $request->id;
        if (!$replyId) {
            return response([
                'message' => 'Reply id is required',
                'data' => null
            ], 400);
        }

        $forum = Forum::find($replyId);
        $deleted = Forum::destroy($replyId);

        if ($deleted) {
            return response([
                'message' => "Reply deleted",
                'data' => $forum
            ], 200);
        } else {
            return response([
                'message' => "Failed to delete reply",
                'data' => null
            ], 400);
        }
    }
}
