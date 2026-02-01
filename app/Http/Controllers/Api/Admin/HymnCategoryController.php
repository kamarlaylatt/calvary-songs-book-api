<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\HymnCategoryRequest;
use App\Models\HymnCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HymnCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cacheKey = 'hymn-categories.admin.index.'.http_build_query($request->all());

        $hymnCategories = Cache::remember($cacheKey, 300, function () use ($request) {
            return HymnCategory::query()
                ->when($request->search, function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->when($request->has('sort_by') && $request->has('sort_order'), function ($query) use ($request) {
                    $sortBy = $request->sort_by;
                    $sortOrder = $request->sort_order === 'asc' ? 'asc' : 'desc';

                    if (in_array($sortBy, ['name', 'created_at', 'id'])) {
                        $query->orderBy($sortBy, $sortOrder);
                    }
                })
                ->when(! ($request->has('sort_by') && $request->has('sort_order')), function ($query) {
                    $query->orderByDesc('created_at')->orderByDesc('id');
                })
                ->paginate(15);
        });

        return response()->json($hymnCategories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HymnCategoryRequest $request)
    {
        $this->authorize('create', HymnCategory::class);

        $hymnCategory = HymnCategory::create($request->validated());

        $this->clearHymnCategoryCaches();

        return response()->json($hymnCategory, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(HymnCategory $hymnCategory)
    {
        return response()->json($hymnCategory->load('hymns'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HymnCategoryRequest $request, HymnCategory $hymnCategory)
    {
        $this->authorize('update', $hymnCategory);

        $hymnCategory->update($request->validated());

        $this->clearHymnCategoryCaches();

        return response()->json($hymnCategory);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HymnCategory $hymnCategory)
    {
        $this->authorize('delete', $hymnCategory);

        $hymnCategory->delete();

        $this->clearHymnCategoryCaches();

        return response()->json(null, 204);
    }

    /**
     * Clear caches related to hymn categories.
     */
    private function clearHymnCategoryCaches(): void
    {
        Cache::flush();
    }
}
