<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Hymn;
use App\Models\HymnCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HymnController extends Controller
{
    /**
     * Display a listing of hymns for mobile users.
     */
    public function index(Request $request)
    {
        $cacheKey = 'hymns.index.'.http_build_query($request->all());
        $cacheDuration = 60 * 30; // 30 minutes

        $hymns = Cache::remember($cacheKey, $cacheDuration, function () use ($request) {
            $query = Hymn::query()
                ->when($request->search, function ($q, $search) {
                    // Search by hymn number if numeric, otherwise by title
                    if (is_numeric($search)) {
                        $q->where('no', $search);
                    } else {
                        $q->where('english_title', 'like', "%{$search}%")
                            ->orWhereHas('song', function ($songQuery) use ($search) {
                                $songQuery->where('title', 'like', "%{$search}%")
                                    ->orWhere('lyrics', 'like', "%{$search}%");
                            });
                    }
                })
                ->when($request->hymn_category_id, function ($q, $categoryId) {
                    $q->where('hymn_category_id', $categoryId);
                })
                ->with(['song:id,code,title,slug,youtube,lyrics', 'hymnCategory:id,name'])
                ->orderBy('no')
                ->orderByDesc('created_at')
                ->orderByDesc('id');

            if ($request->has('limit')) {
                return $query->paginate($request->limit);
            }

            return $query->paginate(20);
        });

        return response()->json($hymns);
    }

    /**
     * Display the specified hymn with full song details.
     */
    public function show($id)
    {
        $cacheKey = "hymn.show.{$id}";
        $cacheDuration = 60 * 15; // 15 minutes

        $hymn = Cache::remember($cacheKey, $cacheDuration, function () use ($id) {
            $hymn = Hymn::with(['song', 'hymnCategory'])->findOrFail($id);

            return [
                'id' => $hymn->id,
                'no' => $hymn->no,
                'english_title' => $hymn->english_title,
                'reference_id' => $hymn->reference_id,
                'hymn_category' => $hymn->hymnCategory,
                'song' => $hymn->song ? [
                    'id' => $hymn->song->id,
                    'code' => $hymn->song->code,
                    'title' => $hymn->song->title,
                    'slug' => $hymn->song->slug,
                    'youtube' => $hymn->song->youtube,
                    'lyrics' => $hymn->song->lyrics,
                    'description' => $hymn->song->description,
                    'song_writer' => $hymn->song->song_writer,
                ] : null,
                'created_at' => $hymn->created_at,
                'updated_at' => $hymn->updated_at,
            ];
        });

        return response()->json($hymn);
    }

    /**
     * Display a listing of all hymn categories.
     */
    public function hymnCategories()
    {
        $categories = HymnCategory::orderBy('name')->get(['id', 'name']);

        return response()->json($categories);
    }

    /**
     * Display available search filters for hymns.
     */
    public function searchFilters()
    {
        $cacheKey = 'hymn.searchFilters';
        $cacheDuration = 60 * 15; // 15 minutes

        $filters = Cache::remember($cacheKey, $cacheDuration, function () {
            $hymnCategories = HymnCategory::orderBy('name')->get(['id', 'name']);

            return [
                'hymn_categories' => $hymnCategories,
            ];
        });

        return response()->json($filters);
    }
}
