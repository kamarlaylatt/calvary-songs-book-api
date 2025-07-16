<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SongController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $songs = Song::query()
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('song_writer', 'like', "%{$search}%")
                    ->orWhere('lyrics', 'like', "%{$search}%");
            })
            ->with('style')
            ->paginate(15);

        return response()->json($songs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'song_writer' => 'nullable|string|max:255',
            'style_id' => 'required|exists:styles,id',
            'lyrics' => 'required|string',
            'music_notes' => 'nullable|string',
        ]);

        $song = auth()->user()->songs()->create($validated + [
            'code' => Song::max('code') + 1,
            'slug' => Str::slug($request->title) . '-' . (Song::max('code') + 1),
        ]);

        return response()->json($song, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Song $song)
    {
        return response()->json($song->load('style'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Song $song)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'song_writer' => 'nullable|string|max:255',
            'style_id' => 'sometimes|required|exists:styles,id',
            'lyrics' => 'sometimes|required|string',
            'music_notes' => 'nullable|string',
        ]);

        $song->update($validated + ['slug' => Str::slug($request->title)]);

        return response()->json($song);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Song $song)
    {
        $song->delete();

        return response()->json(null, 204);
    }
}
