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
        // Create a cache key based on request parameters
        $cacheKey = 'songs_index_' . md5(serialize($request->only([
            'id', 'search', 'style_id', 'category_ids', 'song_language_ids', 'sort_by', 'sort_order'
        ])));

        $songs = cache()->remember($cacheKey, 300, function () use ($request) {
            return Song::query()
                ->when($request->id, function ($query, $id) {
                    $query->where('id', $id);
                })
                ->when($request->search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%");
                    })->orWhere(function ($q) use ($search) {
                        $q->where('lyrics', 'like', "%{$search}%");
                        // $q->whereFullText(['lyrics'], $search, ['mode' => 'boolean']);
                    });
                })
                ->when($request->style_id, function ($query, $styleId) {
                    $query->where('style_id', $styleId);
                })
                ->when($request->category_ids, function ($query, $categoryIds) {
                    $query->whereHas('categories', function ($q) use ($categoryIds) {
                        $q->whereIn('categories.id', is_array($categoryIds) ? $categoryIds : [$categoryIds]);
                    });
                })
                ->when($request->song_language_ids, function ($query, $songLanguageIds) {
                    $query->whereHas('songLanguages', function ($q) use ($songLanguageIds) {
                        $q->whereIn('song_languages.id', is_array($songLanguageIds) ? $songLanguageIds : [$songLanguageIds]);
                    });
                })
                ->with(['style', 'categories', 'songLanguages'])
                ->when($request->has('sort_by') && $request->has('sort_order'), function ($query) use ($request) {
                    $sortBy = $request->sort_by;
                    $sortOrder = $request->sort_order === 'asc' ? 'asc' : 'desc';

                    if (in_array($sortBy, ['created_at', 'id'])) {
                        $query->orderBy($sortBy, $sortOrder);
                    }
                })
                ->when(!($request->has('sort_by') && $request->has('sort_order')), function ($query) {
                    $query->orderByDesc('created_at')->orderByDesc('id');
                })
                ->paginate(15);
        });

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
            'style_id' => 'nullable|exists:styles,id',
            'lyrics' => 'required|string',
            'music_notes' => 'nullable|string',
            'popular_rating' => 'nullable|integer|min:0|max:5',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'song_language_ids' => 'nullable|array',
            'song_language_ids.*' => 'exists:song_languages,id',
        ]);

        /** @var \App\Models\Admin $admin */
        $admin = auth('admin')->user();
        $song = $admin->songs()->create($validated + [
            'code' => Song::max('code') + 1,
            'slug' => Str::slug($request->title) . '-' . (Song::max('code') + 1),
        ]);

        if ($request->has('category_ids')) {
            $song->categories()->sync($request->category_ids);
        }

        if ($request->has('song_language_ids')) {
            $song->songLanguages()->sync($request->song_language_ids);
        }

        return response()->json($song->load(['style', 'categories', 'songLanguages']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Song $song)
    {
        return response()->json($song->load(['style', 'categories', 'songLanguages']));
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
            'style_id' => 'nullable|exists:styles,id',
            'lyrics' => 'sometimes|required|string',
            'music_notes' => 'nullable|string',
            'popular_rating' => 'nullable|integer|min:0|max:5',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'song_language_ids' => 'nullable|array',
            'song_language_ids.*' => 'exists:song_languages,id',
        ]);

        $song->update($validated + ['slug' => Str::slug($request->title) . '-' . $song->code]);

        if ($request->has('category_ids')) {
            $song->categories()->sync($request->category_ids);
        }

        if ($request->has('song_language_ids')) {
            $song->songLanguages()->sync($request->song_language_ids);
        }

        return response()->json($song->load(['style', 'categories', 'songLanguages']));
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
