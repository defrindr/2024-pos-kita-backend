<?php

namespace App\Http\Controllers\WebCommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\News;
use App\Models\User;
use App\Models\LabelNews;
use App\Models\NewsLabels;
use App\Models\CategoryNews;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class NewsController extends Controller
{
    public function add(Request $request){
        $user = auth()->guard('api')->user();

        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'id_category_news' => 'required',
            'content' => 'required',
            'id_label_news' => 'required',
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096', //max 4mb
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        // Image
        $file = $request->file('file');
        $imageName = "public/images/news/".str_replace(' ', '', $file->getClientOriginalName());
        $file->move(public_path('images/news'), $imageName);

        $news = new News();
        $news->id_user = $user->id;
        $news->title = $request->input('title');
        $news->id_category_news = $request->input('id_category_news');
        $news->content = $request->input('content');
        $news->id_label_news = $request->input('id_label_news');
        $news->date = Carbon::now()->format('j F Y');
        $news->image = $imageName; // new column for image

        $result = $news->save();
        if($result){
            return response([
                'message' => "News added",
                'data' => $news
            ], 201);
        }
        else{
            return response([
                'message' => "Failed to add news",
                'data' => null
            ], 400);
        }
    }

    public function showImage($id){
        $news = News::findOrFail($id);
        $path = storage_path('app/' . $news->image);

        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    public function show(Request $request){
        $user = auth()->guard('api')->user();

        $news = News::all()->find($request->id);

        if($news==null){
            return response([
                'message' => "There is no news with that id",
                'data' => null
            ], 400);
        }
        $findLabel = $news->id_label_news;
        $findCategory = $news->id_category_news;
        $label = LabelNews::where('id', $findLabel)->first();
        $category = CategoryNews::where('id', $findCategory)->first();

        $author = User::where('id', $news->id_user)->first();

        return response([
            'message' => "success",
            'data' => $news,
            'news_label' => $label->name,
            'news_category' => $category->name,
            'author' => $author->umkm_name,
            'slug' => Str::slug($news->title)
        ], 200);
    }

    public function showByName(Request $request){
        $user = auth()->guard('api')->user();

        $validator = Validator::make($request->all(),[
            'query' => 'required'
        ]);

        $news = News::where('title', $request->input('query'))->first();

        if($news==null){
            return response([
                'message' => "There is no news with that title",
                'data' => null
            ], 400);
        }

        $author = User::where('id', $news->id_user)->first();
        $label = NewsLabels::where('id_news', $news->id)->get();

        $allLabel = $label->map(function ($map) {
            $labelName = LabelNews::where('id',$map->id_label)->first();
            return [
                'name' => $labelName ? $labelName->name : null
            ];
        })->toArray();

        return response([
            'message' => "success",
            'data' => [
                'id' => $news->id,
                'title' => $news->title,
                'content' => $news->content,
                'image' => $news->image,
                'date' => $news->date,
                'id_news_label' => $news->id_label_news,
                'id_news_category' => $news->id_category_news,
                'author' => $author->umkm_name,
                'slug-title' => Str::slug($news->title),
                'labels' => $allLabel
            ]
        ], 200);
    }

    public function showAll(Request $request){
        $user = auth()->guard('api')->user();

        $validator = Validator::make($request->all(),[
            'id_user' => 'required'
        ]);

        $idUser = $request->input('id_user');

        $news = News::where('id_user', $idUser)->get();


        $newsDetails = $news->map(function ($map) {
            $users = User::where('id',$map->id_user)->first();
            // dd($users);
            return [
                'id' => $map->id,
                'title' => $map->title,
                'id_category_news' => $map->id_category_news,
                'content' => $map->content,
                'image' => $map->image,
                'date' => $map->date,
                'id_label_news' => $map->id_label_news,
                'slug-umkm' => Str::slug($users ? $users->umkm_name : null),
                'slug-title' => Str::slug($map->title)
            ];
        });

        if($news->isEmpty()){
            return response([
                'message' => "There are no news",
                'data' => null
            ], 400);
        }
        else{
            return response([
                'message' => "success",
                'data' => $newsDetails
            ], 200);
        }
    }

    public function showLatestNews(Request $request){
        $user = auth()->guard('api')->user();

        $news = News::orderBy('created_at', 'DESC')->paginate(20);

        $newsDetails = $news->map(function ($map) {
            $umkm = User::where('id', $map->id_user)->first();
            return [
                'id' => $map->id,
                'title' => $map->title,
                'id_category_news' => $map->id_category_news,
                'content' => $map->content,
                'image' => $map->image,
                'date' => $map->date,
                'id_label_news' => $map->id_label_news,
                'slug-title' => Str::slug($map->title),
                'slug-umkm' => Str::slug($umkm ? $umkm->umkm_name : null),
                'author' => $umkm ? $umkm->umkm_name : null
            ];
        });

        if($news->isEmpty()){
            return response([
                'message' => "There are no news",
                'data' => null
            ], 400);
        }
        else{
            return response([
                'message' => "success",
                'data' => $newsDetails,
                'pagination' => [
                    'total' => $news->total(),
                    'per_page' => $news->perPage(),
                    'current_page' => $news->currentPage(),
                    'last_page' => $news->lastPage(),
                    'from' => $news->firstItem(),
                    'to' => $news->lastItem(),
                ],
                'full-data' => $news
            ], 200);
        }
    }

    public function showOldestNews(Request $request){
        $user = auth()->guard('api')->user();

        $news = News::orderBy('created_at', 'ASC')->get();

        $newsDetails = $news->map(function ($map) {
            $umkm = User::where('id', $map->id_user)->first();
            return [
                'id' => $map->id,
                'title' => $map->title,
                'id_category_news' => $map->id_category_news,
                'content' => $map->content,
                'image' => $map->image,
                'date' => $map->date,
                'id_label_news' => $map->id_label_news,
                'slug-title' => Str::slug($map->title),
                'slug-umkm' => Str::slug($umkm ? $umkm->umkm_name : null),
                'author' => $umkm ? $umkm->umkm_name : null
            ];
        });

        if($news->isEmpty()){
            return response([
                'message' => "There are no news",
                'data' => null
            ], 400);
        }
        else{
            return response([
                'message' => "success",
                'data' => $newsDetails
            ], 200);
        }
    }

    public function edit(Request $request){
        $user = auth()->guard('api')->user();
        $validator = Validator::make($request->all(),[
            'title' => 'nullable',
            'id_category_news' => 'nullable',
            'content' => 'nullable',
            'id_label_news' => 'nullable',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096', //max 4mb
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $news = News::all()->find($request->id);

        if(!empty($request->file('file'))){
            $file = $request->file('file');
            $imageName = "public/images/news/".str_replace(' ', '', $file->getClientOriginalName());
            $file->move(public_path('images/news'), $imageName);
            $news->image = $imageName;
        }

        if(!empty($request->input('id_category_news'))){
            $searchCategory = CategoryNews::where('name', $request->input('id_category_news'))->first();
            $news->id_category_news = $searchCategory->id;
        }

        if(!empty($request->input('id_label_news'))){
            $searchLabel = LabelNews::where('name', $request->input('id_label_news'))->first();
            $news->id_label_news = $searchLabel->id;
        }

        if(!empty($request->input('title'))){
            $news->title = $request->input('title');
        }

        if(!empty($request->input('content'))){
            $news->content = $request->input('content');
        }

        $result = $news->save();

        if($result){
            return response([
                'message' => "News edited",
                'data' => $news
            ], 201);
        }
        else{
            return response([
                'message' => "Failed to edit news",
                'data' => null
            ], 400);
        }
    }

    public function delete(Request $request){
        $user = auth()->guard('api')->user();
        $news = News::all()->find($request->id);
        $result = $news->delete();
        if($result){
            return response([
                'message' => "news deleted",
                'data' => $news
            ], 201);
        }
        else{
            return response([
                'message' => "Failed to delete news",
                'data' => null
            ], 400);
        }
    }
}
