<?php

namespace App\Http\Controllers\LMS\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LMS\Classes;
use App\Models\LMS\ClassMember;
use App\Models\LMS\Question;
use App\Models\LMS\TopicMaterials;
use App\Models\LMS\AssignmentReplies;
use App\Models\LMS\AssignmentImages;
use App\Models\LMS\Topics;
use Illuminate\Support\Facades\Validator;


class ClassController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function activeClass()
    {
        $user = auth()->guard('api')->user();

        $user_id = $user->id;

        $myClass = Classes::whereHas('member', function ($query) use ($user_id) {
            $query->where('id_user', $user_id)->where('is_completed', '!=', 7);
        })->get();

        if ($myClass->isEmpty()) {
            return response([
                'message' => "There are no active class",
                'data' => null
            ], 404);
        } else {
            $classList = [];

            foreach ($myClass as $class) {
                $classInfo = [
                    'id' => $class->id,
                    'topic_name' => $class->topic->topic_name,
                    'module_name' => $class->topic->module->name,
                    'date_start' => $class->date_start,
                    'date_end' => $class->date_end
                ];

                $classList[] = $classInfo;
            }

            return response([
                'message' => "Success",
                'data' => $classList
            ], 200);
        }
    }

    public function completeClass()
    {
        $user = auth()->guard('api')->user();

        $user_id = $user->id;

        $myClass = Classes::whereHas('member', function ($query) use ($user_id) {
            $query->where('id_user', $user_id)->where('is_completed', '=', 7);
        })->get();

        if ($myClass->isEmpty()) {
            return response([
                'message' => "There are no completed class",
                'data' => null
            ], 404);
        } else {
            $classList = [];

            foreach ($myClass as $class) {
                $classInfo = [
                    'id' => $class->id,
                    'topic_name' => $class->topic->topic_name,
                    'module_name' => $class->topic->module->name,
                    'date_start' => $class->date_start,
                    'date_end' => $class->date_end
                ];

                $classList[] = $classInfo;
            }

            return response([
                'message' => "Success",
                'data' => $classList
            ], 200);
        }
    }

    public function detailClass(Request $request)
    {
        $user = auth()->guard('api')->user();
        $user_id = $user->id;

        $validator = Validator::make($request->all(), ['id_class' => 'required']);
        if ($validator->fails()) {
            return response([
                'message' => $validator->errors(),
                'data' => null
            ], 400);
        }

        $classInfo = Classes::whereHas('member', function ($query) use ($user_id) {
            $query->where('id_user', $user_id);
        })->where('id', $request->id_class)->first();

        if (!$classInfo) {
            return response([
                'message' => "Class not found",
                'data' => null
            ], 404);
        } else {
            $data = [
                'id' => $classInfo->id,
                'id_topic' => $classInfo->topic->id,
                'topic_name' => $classInfo->topic->topic_name,
                'is_completed' => $classInfo->member->first() ?  $classInfo->member->first()->is_completed : null,
                'link_video_conference' => $classInfo->link_video_conference,
                'vc_description' => $classInfo->vc_description,
                'vc_datetime' => $classInfo->conference_time,
                'date_start' => $classInfo->date_start,
                'date_end' => $classInfo->date_end,
                'description' => $classInfo->topic->description,
                'link_video_1' => $classInfo->topic->link_video_1,
                'link_video_2' => $classInfo->topic->link_video_2,
                'link_material' => $classInfo->topic->link_material,
            ];

            return response([
                'message' => 'success',
                'data' => $data
            ], 200);
        }
    }

    public function preTest(Request $request)
    {
        $user = auth()->guard('api')->user();
        // $user_id = $user->id;
        $classId = $request->id;

        if (!$classId) {
            return response([
                'message' => 'id is empty',
                'data' => null
            ], 400);
        }


        $topicId = Classes::where('id', $classId)->first()->id_topic;

        $questionData = Question::where('id_topic',  $topicId)->get();

        if (!$questionData) {
            return response([
                'message' => "Class not found",
                'data' => null
            ], 404);
        } else {
            $questionList = [];

            foreach ($questionData as $question) {
                $questionInfo = [
                    'id' => $question->id,
                    'question' => $question->question,
                    'option_a' => $question->option_a,
                    'option_b' => $question->option_b,
                    'option_c' => $question->option_c,
                    'option_d' => $question->option_d,
                    'answer' => $question->answer
                ];

                $questionList[] = $questionInfo;
            }

            return response([
                'message' => 'success',
                'data' => $questionList,
            ], 200);
        }
    }

    public function postTest(Request $request)
    {
        $user = auth()->guard('api')->user();
        // $user_id = $user->id;
        $classId = $request->id;

        if (!$classId) {
            return response([
                'message' => 'id is empty',
                'data' => null
            ], 400);
        }


        $topicId = Classes::where('id', $classId)->first()->id_topic;

        $questionData = Question::where('id_topic',  $topicId)->get();

        if (!$questionData) {
            return response([
                'message' => "Class not found",
                'data' => null
            ], 404);
        } else {
            $questionList = [];

            foreach ($questionData as $question) {
                $questionInfo = [
                    'id' => $question->id,
                    'question' => $question->question,
                    'option_a' => $question->option_a,
                    'option_b' => $question->option_b,
                    'option_c' => $question->option_c,
                    'option_d' => $question->option_d,
                    'answer' => $question->answer
                ];

                $questionList[] = $questionInfo;
            }

            return response([
                'message' => 'success',
                'data' => $questionList,
            ], 200);
        }
    }

    public function submitPreTest(Request $request)
    {
        $user = auth()->guard('api')->user();
        $user_id = $user->id;

        $validator = Validator::make($request->all(), [
            'id_class' => 'required',
            'answer' => 'required',
            'score' => 'required'
        ]);
        if ($validator->fails()) {
            return response([
                'message' => $validator->errors(),
                'data' => null
            ], 400);
        }

        $member = ClassMember::where('class_id', $request->id_class)->where('id_user', $user_id)->first();
        if ($member == null) {
            return response([
                'message' => "No member found in the class",
                'data' => null
            ], 404);
        } else {
            $member->answer_pretest = $request->answer;
            $member->score_pretest = $request->score;
            $result = $member->save();

            if ($result) {
                return response([
                    'message' => "Answer submitted",
                    'data' => $member
                ], 200);
            } else {
                return response([
                    'message' => "Failed to submit answer",
                    'data' => null
                ], 400);
            }
        }
    }

    public function submitPostTest(Request $request)
    {
        $user = auth()->guard('api')->user();
        $user_id = $user->id;

        $validator = Validator::make($request->all(), [
            'id_class' => 'required',
            'answer' => 'required',
            'score' => 'required'
        ]);
        if ($validator->fails()) {
            return response([
                'message' => $validator->errors(),
                'data' => null
            ], 400);
        }

        $member = ClassMember::where('class_id', $request->id_class)->where('id_user', $user_id)->first();
        if ($member == null) {
            return response([
                'message' => "No member found in the class",
                'data' => null
            ], 404);
        } else {
            $member->answer_posttest = $request->answer;
            $member->score_posttest = $request->score;
            $result = $member->save();

            if ($result) {
                return response([
                    'message' => "Answer submitted",
                    'data' => $member
                ], 200);
            } else {
                return response([
                    'message' => "Failed to submit answer",
                    'data' => null
                ], 400);
            }
        }
    }

    public function updateComplete(Request $request)
    {
        $user = auth()->guard('api')->user();
        $user_id = $user->id;

        $validator = Validator::make($request->all(), [
            'id_class' => 'required',
            'new_status' => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'message' => $validator->errors(),
                'data' => null
            ], 400);
        }

        $member = ClassMember::where('class_id', $request->id_class)->where('id_user', $user_id)->first();
        if ($member == null) {
            return response([
                'message' => "No member found in the class",
                'data' => null
            ], 404);
        } else {
            $member->is_completed = $request->new_status;
            $result = $member->save();

            if ($result) {
                $res = [
                    'is_completed' => $request->new_status
                ];

                return response([
                    'message' => "Status updated",
                    'data' => $res
                ], 200);
            } else {
                return response([
                    'message' => "Failed to update status",
                    'data' => null
                ], 400);
            }
        }
    }

    public function getMaterial(Request $request)
    {
        $user = auth()->guard('api')->user();
        $user_id = $user->id;

        $validator = Validator::make($request->all(), ['id_class' => 'required']);
        if ($validator->fails()) {
            return response([
                'message' => $validator->errors(),
                'data' => null
            ], 400);
        }

        $topicId = Classes::where('id_topic', $request->id_class)->first()->id_topic;

        $materialData = TopicMaterials::where("id_topic", $topicId);
        if ($materialData) {
            $materialList = [];

            foreach ($materialData as $material) {
                $materialInfo = [
                    'id' => $material->id,
                    'url_image' => $material->url_image
                ];

                $materialList[] = $materialInfo;
            }

            return response([
                'message' => 'success',
                'data' => $materialList,
            ], 200);
        }
    }

    public function getAssignment(Request $request)
    {
        $user = auth()->guard('api')->user();
        $user_id = $user->id;

        $classId = $request->id;

        if (!$classId) {
            return response([
                'message' => 'Class id is required',
                'data' => null
            ], 400);
        }

        $classes = Classes::where('id', $classId)->first();

        if ($classes) {
            $id_topic = $classes->id_topic;

            $assignment = Topics::where('id', $id_topic)->first();

            if ($assignment) {
                $data = [
                    'id_class' => $classId,
                    'assignment' => $assignment->assignment
                ];

                $answer = AssignmentReplies::where('id_class', $classId)->where('id_user', $user_id)->first();

                if (!$answer) {
                    $data['answer'] = null;
                } else {
                    $id_answer = $answer->id;

                    $data['answer'] = [
                        'id' => $id_answer,
                        'content' => $answer->content
                    ];

                    $img = AssignmentImages::where('id_assignment_reply', $id_answer)->get();

                    if (!$img) {
                        $data['answer']['images'] = null;
                    } else {
                        $data['answer']['images'] = $img;
                    }
                }

                return response([
                    'message' => "Ok",
                    'data' => $data
                ], 200);
            } else {
                return response([
                    'message' => "Data not found",
                    'data' => null
                ], 404);
            }
        } else {
            return response([
                'message' => "Class not found",
                'data' => null
            ], 404);
        }
    }

    public function submitAssignment(Request $request)
    {
        $user = auth()->guard('api')->user();
        $user_id = $user->id;

        $validator = Validator::make($request->all(), [
            'id_class' => 'required',
            'content' => 'nullable',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240' //max 10mb
        ]);

        if ($validator->fails()) {
            return response([
                'message' => $validator->errors(),
                'data' => null
            ], 400);
        }

        $assignment = new AssignmentReplies();
        $assignment->id_class = $request->id_class;
        $assignment->id_user = $user_id;

        if (!empty($request->content)) {
            $assignment->content = $request->content;
        }

        if (!$assignment->save()) {
            return response([
                'message' => "Failed to submit assignment.",
                'data' => null
            ], 400);
        } else {
            $assignment_id = $assignment->id;
            $img_assignment = new AssignmentImages();


            $img_assignment->id_assignment_reply = $assignment_id;

            if (!empty($request->file)) {
                $file = $request->file('file');
                $extension = $request->file('file')->guessExtension();
                $current = $user_id . time() . $assignment_id . "." . $extension;
                $imageName = "public/images/assignment/" . str_replace(' ', '', $current);
                $file->move(public_path('images/assignment'), $imageName);
                $img_assignment->url_image = $imageName;

                if (!$img_assignment->save()) {
                    return response([
                        'message' => "Failed to add image",
                        'data' => null
                    ], 200);
                } else {
                    return response([
                        'message' => "Assignment created",
                        'data' => $assignment->id
                    ], 201);
                }
            } else {
                return response([
                    'message' => "Assignment created",
                    'data' => $assignment->id
                ], 201);
            }
        }
    }
}
