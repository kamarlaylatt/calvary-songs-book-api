<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Models\Category;
use App\Models\SongLanguage;
use App\Models\Style;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SongController extends Controller
{
    /**
     * Display a listing of songs for users.
     */
    public function index(Request $request)
    {
        $cacheKey = 'songs.index.' . http_build_query($request->all());
        $cacheDuration = 60 * 30; // 30 minutes (half hour)

        $songs = Cache::remember($cacheKey, $cacheDuration, function () use ($request) {
            $songsQuery = Song::query()
                ->when($request->search, function ($query, $search) {
                    if (is_numeric($search)) {
                        $query->where('id', $search);
                    } else {
                        $query->where(function ($q) use ($search) {
                            $q->where('title', 'like', "%{$search}%");
                        })->orWhere(function ($q) use ($search) {
                            $q->where('lyrics', 'like', "%{$search}%");
                        });
                    }
                })
                ->when($request->style_id, function ($query, $styleId) {
                    $query->where('style_id', $styleId);
                })
                ->when($request->category_id, function ($query, $categoryId) {
                    $query->whereHas('categories', function ($q) use ($categoryId) {
                        $q->where('categories.id', $categoryId);
                    });
                })
                ->when($request->song_language_id, function ($query, $songLanguageId) {
                    $query->whereHas('songLanguages', function ($q) use ($songLanguageId) {
                        $q->where('song_languages.id', $songLanguageId);
                    });
                })
                ->with(['style', 'categories', 'songLanguages'])
                ->orderByDesc('popular_rating')
                ->orderByDesc('created_at')
                ->orderByDesc('id');

            if ($request->has('limit')) {
                return $songsQuery->paginate($request->limit);
            }

            return [
                'data' => $songsQuery->get()
            ];
        });

        return response()->json($songs);
    }

    /**
     * Display the specified song details.
     */
    public function show($slug)
    {
        $cacheKey = "song.show.{$slug}";
        $cacheDuration = 60 * 15; // 15 minutes

        $song = Cache::remember($cacheKey, $cacheDuration, function () use ($slug) {
            $song = Song::where('slug', $slug)->firstOrFail();
            $song->load('style', 'categories', 'songLanguages');

            return [
                'id' => $song->id,
                'code' => $song->code,
                'title' => $song->title,
                'slug' => $song->slug,
                'youtube' => $song->youtube,
                'description' => $song->description,
                'song_writer' => $song->song_writer,
                'style' => $song->style,
                'categories' => $song->categories,
                'song_languages' => $song->songLanguages,
                'lyrics' => $song->lyrics,
                'music_notes' => $song->music_notes,
                'created_at' => $song->created_at,
                'updated_at' => $song->updated_at,
            ];
        });

        return response()->json($song);
    }

    /**
     * Display a listing of all categories.
     */
    public function categories()
    {
        $categories = Category::orderBy('sort_no', 'asc')->get(['id', 'name', 'slug']);
        return response()->json($categories);
    }

    /**
     * Display a listing of all categories and styles for search filters.
     */
    public function searchFilters()
    {
        $cacheKey = 'song.searchFilters';
        $cacheDuration = 60 * 15; // 15 minutes

        $filters = Cache::remember($cacheKey, $cacheDuration, function () {
            $categories = Category::orderBy('name')->get(['id', 'name', 'slug']);
            $styles = Style::orderBy('name')->get(['id', 'name']);
            $songLanguages = SongLanguage::orderBy('name')->get(['id', 'name']);

            return [
                'categories' => $categories,
                'styles' => $styles,
                'song_languages' => $songLanguages
            ];
        });

        return response()->json($filters);
    }
}
