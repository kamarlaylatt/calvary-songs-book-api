<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Models\Category;
use Illuminate\Http\Request;

class SongController extends Controller
{
    /**
     * Display a listing of songs for users.
     */
    public function index(Request $request)
    {
        $songs = Song::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                })->orWhere(function ($q) use ($search) {
                    $q->where('lyrics', 'like', "%{$search}%");
                });
            })
            ->when($request->style_id, function ($query, $styleId) {
                $query->where('style_id', $styleId);
            })
            ->with(['style', 'categories'])
            ->select(['id', 'code', 'title', 'slug', 'youtube', 'description', 'song_writer', 'style_id'])
            ->paginate(15);

        return response()->json($songs);
    }

    /**
     * Display the specified song details.
     */
    public function show($slug)
    {
        $song = Song::where('slug', $slug)->firstOrFail();
        $song->load('style');

        return response()->json([
            'id' => $song->id,
            'code' => $song->code,
            'title' => $song->title,
            'slug' => $song->slug,
            'youtube' => $song->youtube,
            'description' => $song->description,
            'song_writer' => $song->song_writer,
            'style' => $song->style,
            'categories' => $song->categories,
            'lyrics' => $song->lyrics,
            'music_notes' => $song->music_notes,
            'created_at' => $song->created_at,
            'updated_at' => $song->updated_at,
        ]);
    }

    /**
     * Display a listing of all categories.
     */
    public function categories()
    {
        $categories = Category::orderBy('name')->get(['id', 'name', 'slug']);
        return response()->json($categories);
    }
}
