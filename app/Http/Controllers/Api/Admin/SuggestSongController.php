<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Models\SuggestSong;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SuggestSongController extends Controller
{
    /**
     * Display a listing of the resource with optional filters.
     */
    public function index(Request $request)
    {
        $suggestions = SuggestSong::query()
            ->with(['categories', 'style'])
            ->when($request->id, function ($query, $id) {
                $query->where('id', $id);
            })
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('lyrics', 'like', "%{$search}%");
                });
            })
            ->when($request->style_id, function ($query, $styleId) {
                $query->where('style_id', $styleId);
            })
            ->when($request->category_id, function ($query, $categoryId) {
                $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                });
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15);

        return response()->json($suggestions);
    }

    /**
     * Display the specified resource.
     */
    public function show(SuggestSong $suggestSong)
    {
        return response()->json($suggestSong->load(['categories', 'style']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SuggestSong $suggestSong)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'song_writer' => 'nullable|string|max:255',
            'style_id' => 'nullable|exists:styles,id',
            'key' => 'nullable|string|max:255',
            'lyrics' => 'sometimes|required|string',
            'music_notes' => 'nullable|string',
            'popular_rating' => 'nullable|integer|min:0|max:5',
            'email' => 'nullable|email|max:255',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id',
        ]);

        // Do not persist category_ids directly on model
        $categoryIds = $request->input('category_ids');
        unset($validated['category_ids']);

        $suggestSong->update($validated);

        // Allow single ID or array; normalize and sync if provided
        if (!is_null($categoryIds)) {
            if (is_numeric($categoryIds)) {
                $categoryIds = [(int) $categoryIds];
            }
            if (is_array($categoryIds) && !empty($categoryIds)) {
                $suggestSong->categories()->sync($categoryIds);
            }
        }

        return response()->json($suggestSong->load('categories'));
    }

    /**
     * Approve the suggestion and create a song entry.
     */
    public function approve(SuggestSong $suggestSong)
    {
        if ($suggestSong->status === SuggestSong::STATUS_APPROVED) {
            return response()->json(['message' => 'Suggestion already approved'], 422);
        }

        $admin = auth('admin')->user();
        $song = DB::transaction(function () use ($suggestSong, $admin) {

            $code = Song::nextCode(true);

            $songData = [
                'code' => $code,
                'title' => $suggestSong->title,
                'slug' => Song::generateSlug($suggestSong->title, $code),
                'youtube' => $suggestSong->youtube,
                'description' => $suggestSong->description,
                'song_writer' => $suggestSong->song_writer,
                'style_id' => $suggestSong->style_id,
                'lyrics' => $suggestSong->lyrics,
                'music_notes' => $suggestSong->music_notes,
                'popular_rating' => $suggestSong->popular_rating,
            ];

            $song = $admin
                ? $admin->songs()->create($songData)
                : Song::create($songData);

            // Copy categories from suggestion to created song
            $categoryIds = $suggestSong->categories()->pluck('categories.id')->all();
            if (!empty($categoryIds)) {
                $song->categories()->sync($categoryIds);
            }

            $suggestSong->update(['status' => SuggestSong::STATUS_APPROVED]);

            return $song;
        });

        Cache::flush();

        return response()->json([
            'message' => 'Suggestion approved and song created',
            'suggestion' => $suggestSong,
            'song' => $song->load(['style', 'categories', 'songLanguages']),
        ]);
    }

    /**
     * Cancel a suggestion.
     */
    public function cancel(SuggestSong $suggestSong)
    {
        if ($suggestSong->status !== SuggestSong::STATUS_PENDING) {
            return response()->json(['message' => 'Only pending suggestions can be cancelled'], 422);
        }

        $suggestSong->update(['status' => SuggestSong::STATUS_CANCELLED]);

        return response()->json([
            'message' => 'Suggestion cancelled',
            'suggestion' => $suggestSong,
        ]);
    }
}
