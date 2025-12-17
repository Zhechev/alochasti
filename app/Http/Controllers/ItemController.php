<?php

namespace App\Http\Controllers;

use App\Enums\ItemStatus;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Category;
use App\Models\City;
use App\Models\Item;
use App\Models\ItemPhoto;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Item::class);

        $categories = Category::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->name,
            ])
            ->all();

        $cities = City::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (City $city) => [
                'id' => $city->id,
                'name' => $city->name,
            ])
            ->all();

        $tags = Tag::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Tag $tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
            ])
            ->all();

        return view('items.index', [
            'categories' => $categories,
            'cities' => $cities,
            'tags' => $tags,
        ]);
    }

    /**
     * Display the authenticated user's own listings.
     */
    public function my()
    {
        $this->authorize('viewAny', Item::class);

        $categories = Category::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->name,
            ])
            ->all();

        $cities = City::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (City $city) => [
                'id' => $city->id,
                'name' => $city->name,
            ])
            ->all();

        $tags = Tag::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Tag $tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
            ])
            ->all();

        return view('items.my', [
            'categories' => $categories,
            'cities' => $cities,
            'tags' => $tags,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Item::class);

        $categories = Category::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $cities = City::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $tags = Tag::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('items.create', [
            'categories' => $categories,
            'cities' => $cities,
            'tags' => $tags,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreItemRequest $request)
    {
        $validated = $request->validated();

        $item = DB::transaction(function () use ($request, $validated) {
            $item = Item::create([
                'user_id' => $request->user()->id,
                'category_id' => $validated['category_id'],
                'city_id' => $validated['city_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'weight' => $validated['weight'] ?? null,
                'dimensions' => $validated['dimensions'] ?? null,
                'status' => ItemStatus::Available,
                'gifted_at' => null,
            ]);

            $uploaded = $request->file('photos', []);
            foreach (array_values($uploaded) as $index => $file) {
                $path = $file->store("items/{$item->id}", 'public');
                $item->photos()->create([
                    'path' => $path,
                    'sort_order' => $index,
                ]);
            }

            $item->tags()->sync($validated['tag_ids'] ?? []);

            return $item;
        });

        return redirect()
            ->route('items.show', $item)
            ->with('status', __('Item created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        $this->authorize('view', $item);

        $item->load([
            'user:id,name',
            'category:id,name',
            'city:id,name',
            'photos',
            'tags:id,name',
        ]);

        return view('items.show', [
            'item' => $item,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        $this->authorize('update', $item);

        $categories = Category::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $item->load(['photos']);

        $cities = City::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $tags = Tag::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('items.edit', [
            'item' => $item,
            'categories' => $categories,
            'cities' => $cities,
            'tags' => $tags,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemRequest $request, Item $item)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($request, $item, $validated) {
            $status = ItemStatus::from($validated['status']);

            $item->fill([
                'category_id' => $validated['category_id'],
                'city_id' => $validated['city_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'weight' => $validated['weight'] ?? null,
                'dimensions' => $validated['dimensions'] ?? null,
                'status' => $status,
            ]);

            if ($status === ItemStatus::Gifted && $item->gifted_at === null) {
                $item->gifted_at = now();
            }

            if ($status === ItemStatus::Available) {
                $item->gifted_at = null;
            }

            $item->save();

            $item->tags()->sync($validated['tag_ids'] ?? []);

            $deletePhotoIds = $validated['delete_photo_ids'] ?? [];
            if (!empty($deletePhotoIds)) {
                $photosToDelete = ItemPhoto::query()
                    ->where('item_id', $item->id)
                    ->whereIn('id', $deletePhotoIds)
                    ->get(['id', 'path']);

                foreach ($photosToDelete as $photo) {
                    Storage::disk('public')->delete($photo->path);
                }

                ItemPhoto::query()
                    ->where('item_id', $item->id)
                    ->whereIn('id', $deletePhotoIds)
                    ->delete();
            }

            $existingCount = $item->photos()->count();
            $uploaded = $request->file('photos', []);
            foreach (array_values($uploaded) as $index => $file) {
                $path = $file->store("items/{$item->id}", 'public');
                $item->photos()->create([
                    'path' => $path,
                    'sort_order' => $existingCount + $index,
                ]);
            }
        });

        return redirect()
            ->route('items.edit', $item)
            ->with('status', __('Item updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $this->authorize('delete', $item);

        $item->load('photos');

        foreach ($item->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }

        $item->delete();

        return redirect()
            ->route('items.index')
            ->with('status', __('Item deleted successfully.'));
    }
}
