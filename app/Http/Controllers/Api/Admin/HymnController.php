<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\HymnRequest;
use App\Models\Hymn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HymnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cacheKey = 'hymns.admin.index.'.http_build_query($request->all());

        $hymns = Cache::remember($cacheKey, 300, function () use ($request) {
            return Hymn::query()
                ->when($request->id, function ($query, $id) {
                    $query->where('id', $id);
                })
                ->when($request->no, function ($query, $no) {
                    $query->where('no', $no);
                })
                ->when($request->search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('english_title', 'like', "%{$search}%");
                    });
                })
                ->when($request->hymn_category_id, function ($query, $hymnCategoryId) {
                    $query->where('hymn_category_id', $hymnCategoryId);
                })
                ->when($request->song_id, function ($query, $songId) {
                    $query->where('song_id', $songId);
                })
                ->with(['hymnCategory', 'song'])
                ->when($request->has('sort_by') && $request->has('sort_order'), function ($query) use ($request) {
                    $sortBy = $request->sort_by;
                    $sortOrder = $request->sort_order === 'asc' ? 'asc' : 'desc';

                    if (in_array($sortBy, ['no', 'created_at', 'id', 'reference_id'])) {
                        $query->orderBy($sortBy, $sortOrder);
                    }
                })
                ->when(! ($request->has('sort_by') && $request->has('sort_order')), function ($query) {
                    $query->orderBy('no')->orderByDesc('created_at')->orderByDesc('id');
                })
                ->paginate(15);
        });

        return response()->json($hymns);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HymnRequest $request)
    {
        $this->authorize('create', Hymn::class);

        $hymn = Hymn::create($request->validated());

        $this->clearHymnCaches();

        return response()->json($hymn->load(['hymnCategory', 'song']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Hymn $hymn)
    {
        return response()->json($hymn->load(['hymnCategory', 'song']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HymnRequest $request, Hymn $hymn)
    {
        $this->authorize('update', $hymn);

        $hymn->update($request->validated());

        $this->clearHymnCaches();

        return response()->json($hymn->load(['hymnCategory', 'song']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hymn $hymn)
    {
        $this->authorize('delete', $hymn);

        $hymn->delete();

        $this->clearHymnCaches();

        return response()->json(null, 204);
    }

    /**
     * Clear caches related to hymns listings.
     */
    private function clearHymnCaches(): void
    {
        Cache::flush();
    }
}
