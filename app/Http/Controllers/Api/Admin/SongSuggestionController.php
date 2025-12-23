<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Models\SongSuggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class SongSuggestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cacheKey = 'song_suggestions.admin.index.' . http_build_query($request->all());

        $suggestions = cache()->remember($cacheKey, 300, function () use ($request) {
            return SongSuggestion::query()
                ->when($request->id, function ($query, $id) {
                    $query->where('id', $id);
                })
                ->when($request->search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                          ->orWhere('lyrics', 'like', "%{$search}%");
                    });
                })
                ->when($request->status, function ($query, $status) {
                    $query->where('status', $status);
                })
                ->when($request->style_id, function ($query, $styleId) {
                    $query->where('style_id', $styleId);
                })
                ->when($request->user_id, function ($query, $userId) {
                    $query->where('user_id', $userId);
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
                ->with(['style', 'categories', 'songLanguages', 'user'])
                ->when($request->has('sort_by') && $request->has('sort_order'), function ($query) use ($request) {
                    $sortBy = $request->sort_by;
                    $sortOrder = $request->sort_order === 'asc' ? 'asc' : 'desc';

                    if (in_array($sortBy, ['created_at', 'id', 'status'])) {
                        $query->orderBy($sortBy, $sortOrder);
                    }
                })
                ->when(!($request->has('sort_by') && $request->has('sort_order')), function ($query) {
                    $query->orderByDesc('created_at')->orderByDesc('id');
                })
                ->paginate(15);
        });

        return response()->json($suggestions);
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
            'user_id' => 'nullable|exists:users,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'song_language_ids' => 'nullable|array',
            'song_language_ids.*' => 'exists:song_languages,id',
        ]);

        $suggestion = SongSuggestion::create($validated);

        if ($request->has('category_ids')) {
            $suggestion->categories()->sync($request->category_ids);
        }

        if ($request->has('song_language_ids')) {
            $suggestion->songLanguages()->sync($request->song_language_ids);
        }

        $this->clearSuggestionCaches();

        return response()->json($suggestion->load(['style', 'categories', 'songLanguages', 'user']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(SongSuggestion $songSuggestion)
    {
        return response()->json($songSuggestion->load(['style', 'categories', 'songLanguages', 'user']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SongSuggestion $songSuggestion)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'song_writer' => 'nullable|string|max:255',
            'style_id' => 'nullable|exists:styles,id',
            'lyrics' => 'sometimes|required|string',
            'music_notes' => 'nullable|string',
            'status' => 'sometimes|in:pending,approved,cancelled',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'song_language_ids' => 'nullable|array',
            'song_language_ids.*' => 'exists:song_languages,id',
        ]);

        $songSuggestion->update($validated);

        if ($request->has('category_ids')) {
            $songSuggestion->categories()->sync($request->category_ids);
        }

        if ($request->has('song_language_ids')) {
            $songSuggestion->songLanguages()->sync($request->song_language_ids);
        }

        $this->clearSuggestionCaches();

        return response()->json($songSuggestion->load(['style', 'categories', 'songLanguages', 'user']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SongSuggestion $songSuggestion)
    {
        $songSuggestion->delete();

        $this->clearSuggestionCaches();

        return response()->json(null, 204);
    }

    /**
     * Approve a suggestion and create a song from it.
     */
    public function approve(SongSuggestion $songSuggestion)
    {
        if ($songSuggestion->status === 'approved') {
            return response()->json(['message' => 'Suggestion is already approved'], 400);
        }

        /** @var \App\Models\Admin $admin */
        $admin = auth('admin')->user();
        
        // Create a song from the suggestion
        $song = $admin->songs()->create([
            'code' => Song::max('code') + 1,
            'title' => $songSuggestion->title,
            'slug' => Str::slug($songSuggestion->title) . '-' . (Song::max('code') + 1),
            'youtube' => $songSuggestion->youtube,
            'description' => $songSuggestion->description,
            'song_writer' => $songSuggestion->song_writer,
            'style_id' => $songSuggestion->style_id,
            'lyrics' => $songSuggestion->lyrics,
            'music_notes' => $songSuggestion->music_notes,
        ]);

        // Sync categories and languages
        $song->categories()->sync($songSuggestion->categories->pluck('id'));
        $song->songLanguages()->sync($songSuggestion->songLanguages->pluck('id'));

        // Update suggestion status
        $songSuggestion->update(['status' => 'approved']);

        $this->clearSuggestionCaches();
        Cache::flush(); // Also clear song caches

        return response()->json([
            'message' => 'Suggestion approved and song created successfully',
            'suggestion' => $songSuggestion->load(['style', 'categories', 'songLanguages', 'user']),
            'song' => $song->load(['style', 'categories', 'songLanguages'])
        ]);
    }

    /**
     * Cancel/reject a suggestion.
     */
    public function cancel(SongSuggestion $songSuggestion)
    {
        if ($songSuggestion->status === 'cancelled') {
            return response()->json(['message' => 'Suggestion is already cancelled'], 400);
        }

        $songSuggestion->update(['status' => 'cancelled']);

        $this->clearSuggestionCaches();

        return response()->json([
            'message' => 'Suggestion cancelled successfully',
            'suggestion' => $songSuggestion->load(['style', 'categories', 'songLanguages', 'user'])
        ]);
    }

    /**
     * Clear caches related to song suggestions.
     */
    private function clearSuggestionCaches(): void
    {
        Cache::flush();
    }
}
