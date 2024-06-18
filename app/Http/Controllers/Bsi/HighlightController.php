<?php

namespace App\Http\Controllers\Bsi;

use App\Models\Highlight;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Bsi\HighlightStoreRequest;
use App\Http\Requests\Bsi\HighlightUpdateRequest;

class HighlightController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hightlights = Highlight::orderBy('position', 'asc')->get();

        return response([
            'message' => "success",
            'data' => $hightlights
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
    public function store(HighlightStoreRequest $request)
    {
        $validated = $request->validated();

        if (Highlight::count() >= 5) {
            return response()->json([
                'message' => 'Highlight telah mencapai maksimum kapasitas.',
                'status' => 403
            ], 403);
        }

        $nextPosition = Highlight::max('position') + 1;

        $highlight = new Highlight();
        $highlight->title = $validated['title'];
        $highlight->id_product = $validated['id_product'];
        $highlight->position = $nextPosition;

        if (!empty($validated['image'])) {
            $file = $validated['image'];
            $imageName = "public/images/highlight/" . str_replace(' ', '', $file->getClientOriginalName());
            $file->move(public_path('images/highlight'), $imageName);
            $highlight->image = $imageName;
        }

        if (!$highlight->save()) {
            return response([
                'message' => 'Failed to create highlight',
                'data' => null
            ], 400);
        }

        return response([
            'message' => 'Highlight created successfully',
            'data' => $highlight
        ], 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $highlight = Highlight::where('id', $id)->first();

        if (!$highlight) {
            return response([
                'status' => 404,
                'message' => "Highlight not found",
            ], 404);
        }

        return response([
            'message' => "success",
            'data' => $highlight,
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
    public function update(HighlightUpdateRequest $request, string $id)
    {
        $validated = $request->validated();

        $highlight = Highlight::where('id', $id)->first();

        if (!$highlight) {
            return response([
                'status' => 404,
                'message' => "Highlight not found",
            ], 404);
        }

        $highlight->title = $validated['title'];
        $highlight->id_product = $validated['id_product'];

        if (!empty($validated['image'])) {
            $file = $validated['image'];
            $imageName = "public/images/highlight/" . str_replace(' ', '', $file->getClientOriginalName());
            $file->move(public_path('images/highlight'), $imageName);
            $highlight->image = $imageName;
        }

        if (!$highlight->save()) {
            return response([
                'message' => "Failed to edit highlight",
                'data' => null
            ], 400);
        }

        return response([
            'message' => "Highlight edited successfully",
            'data' => $highlight
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $highlight = Highlight::where('id', $id)->first();

        if (!$highlight) {
            return response([
                'status' => 404,
                'message' => "Highlight not found",
            ], 404);
        }

        $result = Highlight::destroy($id);

        Highlight::where('position', '>', $highlight->position)->decrement('position');

        if (!$result) {
            return response([
                'message' => "Failed to delete highlight",
                'data' => null
            ], 400);
        }

        return response([
            'message' => "highlight deleted successfully",
            'data' => $highlight
        ], 200);
    }

    public function incrementPosition(string $id)
    {
        $highlight = Highlight::where('id', $id)->first();

        if (!$highlight) {
            return response([
                'status' => 404,
                'message' => "Highlight not found",
            ], 404);
        }

        $nextHighlight = Highlight::where('position', $highlight->position + 1)->first();

        if ($nextHighlight) {
            $highlight->increment('position');
            $nextHighlight->decrement('position');
        }

        if (!$highlight) {
            return response([
                'message' => "There is no highlight with that id",
                'data' => null
            ], 400);
        }

        return response([
            'status' => 'success',
            'message' => 'highlight position has been updated'
        ], 204);
    }

    public function decrementPosition(string $id)
    {
        $highlight = Highlight::find($id);

        if (!$highlight) {
            return response([
                'status' => 404,
                'message' => "Highlight not found",
            ], 404);
        }

        if ($highlight->position > 1) {
            $previousHighlight = Highlight::where('position', $highlight->position - 1)->first();

            if ($previousHighlight) {
                $highlight->decrement('position');
                $previousHighlight->increment('position');
            }
        }

        if (!$highlight) {
            return response([
                'message' => "There is no highlight with that id",
                'data' => null
            ], 400);
        }

        return response([
            'status' => 'success',
            'message' => 'highlight position has been updated'
        ], 204);
    }
}
