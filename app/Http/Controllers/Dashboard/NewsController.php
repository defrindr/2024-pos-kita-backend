<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\News;
use App\Models\LabelNews;
use App\Models\NewsLabels;
use App\Models\CategoryNews;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->guard('api')->user();

        $news = News::where('id_user', $user->id)->orderBy('created_at', 'desc')->get();

        return response([
            'message' => "success",
            'data' => $news
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->guard('api')->user();

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'id_category_news' => 'required',
            'content' => 'required|min:100',
            'id_label_news' => 'nullable',
            'file' => 'required|image|mimes:jpeg,png,jpg|max:10240', //max 4mb
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $news = new News();
        $news->id_user = $user->id;
        $news->title = $request->input('title');

        if (!CategoryNews::where('id', $request->input('id_category_news'))->first()) {
            return response([
                'message' => 'category not found'
            ], 400);
        }


        $news->id_category_news = $request->input('id_category_news');
        $news->content = $request->input('content');
        $news->date = Carbon::now()->format('j F Y');

        if (!empty($request->file('file'))) {
            $file = $request->file('file');
            $imageName = "public/images/news/" . str_replace(' ', '', $file->getClientOriginalName());
            $file->move(public_path('images/news'), $imageName);
            $news->image = $imageName;
        }

        if (!$news->save()) {
            return response([
                'message' => "Failed to add news",
                'data' => null
            ], 400);
        }

        $news['category_name'] = CategoryNews::where('id', $news->id_category_news)->first()->name;

        if ($request->input('id_label_news')) {
            $labels = $request->input('id_label_news');
            $labels = array_unique($labels);
            foreach ($labels as $label) {
                if (!LabelNews::where('id', $label)->first()) {
                    continue;
                }
                $newNewsLabel = NewsLabels::create([
                    'id_news' => $news->id,
                    'id_label' => $label,
                ]);
                $labelList[] =  $newNewsLabel->label->name;
            }
            $news['labels'] = $labelList;
        }

        return response([
            'message' => "News added",
            'data' => $news
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = auth()->guard('api')->user();

        $news = News::where('id', $id)->first();

        if (!$news) {
            return response([
                'message' => "There is no news with that id",
                'data' => null
            ], 400);
        }

        if ($news->id_user !== $user->id) {
            return response([
                'message' => "This is not your news!",
                'data' => null
            ], 400);
        }

        $news->newsLabels->pluck('label');

        return response([
            'message' => "success",
            'data' => $news
        ], 200);
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->guard('api')->user();

        $validator = Validator::make($request->all(), [
            'title' => 'nullable',
            'id_category_news' => 'nullable',
            'content' => 'nullable|min:100',
            'id_label_news' => 'nullable',
            'file' => 'nullable|image|mimes:jpeg,png,jpg|max:10240', //max 4mb
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $news = News::where('id', $id)->first();

        if (!$news) {
            return response([
                'message' => "There is no news with that id",
                'data' => null
            ], 400);
        }

        if ($news->id_user !== $user->id) {
            return response([
                'message' => "This is not your news!",
                'data' => null
            ], 400);
        }

        if (!empty($request->input('title'))) {
            $news->title = $request->input('title');
        }

        if (!empty($request->input('id_category_news'))) {
            if (!CategoryNews::where('id', $request->input('id_category_news'))->first()) {
                return response([
                    'message' => 'category not found'
                ], 400);
            }
            $news->id_category_news = $request->input('id_category_news');
        }

        if (!empty($request->input('content'))) {
            $news->content = $request->input('content');
        }

        if (!empty($request->file('file'))) {
            $file = $request->file('file');
            $imageName = "public/images/news/" . str_replace(' ', '', $file->getClientOriginalName());
            $file->move(public_path('images/news'), $imageName);
            $news->image = $imageName;
        }

        if (!empty($request->input('id_label_news'))) {
            $labels = $request->input('id_label_news');
            $labels = array_unique($labels);

            Newslabels::where('id_news', $news->id)->delete();

            foreach ($labels as $label) {
                if (!LabelNews::where('id', $label)->first()) {
                    continue;
                }
                $newNewsLabel = NewsLabels::create([
                    'id_news' => $news->id,
                    'id_label' => $label,
                ]);
                $labelList[] =  $newNewsLabel->label->name;
            }
        }

        if (!$news->save()) {
            return response([
                'message' => "Failed to edit news",
                'data' => null
            ], 400);
        }

        $news->newsLabels->pluck('label');

        return response([

            'message' => "news edited",
            'data' => $news
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
