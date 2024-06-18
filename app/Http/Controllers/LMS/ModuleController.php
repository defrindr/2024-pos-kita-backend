<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\LMS\Module;
use App\Models\LMS\TopicMaterials;
use App\Models\LMS\Topics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ModuleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function showAll()
    {
        $module = Module::all();

        $result = $module;

        if ($result) {
            return response([
                'message' => "success",
                'data' => $module
            ], 200);
        } else {
            return response([
                'message' => "There is no module data",
                'data' => null
            ], 404);
        }
    }

    public function showAllTopic(Request $request)
    {
        $validator = Validator::make($request->all(), ['id_module' => 'required']);
        if ($validator->fails()) {
            return response([
                'message' => $validator->errors(),
                'data' => null
            ], 400);
        }

        $topics = Topics::where('id_module', $request->input('id_module'))->get();
        if ($topics) {
            return response([
                'message' => "Success",
                'data' => $topics
            ], 200);
        } else {
            return response([
                'message' => "There are no topics",
                'data' => null
            ], 404);
        }
    }

    public function topicMaterial(Request $request)
    {
        $validator = Validator::make($request->all(), ['id_topic' => 'required']);
        if ($validator->fails()) {
            return response([
                'message' => $validator->errors(),
                'data' => null
            ], 400);
        }

        $materials = TopicMaterials::where('id_topic', $request->id_topic)->orderBy("id", "ASC")->get();
        if ($materials) {
            return response([
                'message' => "Success",
                'data' => $materials
            ], 200);
        } else {
            return response([
                'message' => "There are no topics",
                'data' => null
            ], 404);
        }
    }
}
