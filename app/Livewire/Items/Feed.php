<?php

namespace App\Livewire\Items;

use App\Enums\ItemStatus;
use App\Models\Item;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Feed extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    /**
     * @var array<int, array{id:int, name:string}>
     */
    public array $categories = [];

    /**
     * @var array<int, array{id:int, name:string}>
     */
    public array $cities = [];

    /**
     * @var array<int, array{id:int, name:string}>
     */
    public array $tags = [];

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(as: 'category', history: true)]
    public ?int $categoryId = null;

    #[Url(as: 'city', history: true)]
    public ?int $cityId = null;

    #[Url(as: 'status', history: true)]
    public string $status = '';

    /**
     * Filter items by one or more tag IDs.
     *
     * @var array<int, int>
     */
    #[Url(as: 'tags', history: true)]
    public array $tagIds = [];

    #[Url(as: 'sort', history: true)]
    public string $sort = 'newest';

    public bool $onlyMine = false;

    /**
     * @param Collection<int, \App\Models\Category>|array $categories
     * @param Collection<int, \App\Models\City>|array $cities
     * @param Collection<int, \App\Models\Tag>|array $tags
     */
    public function mount($categories = [], $cities = [], $tags = [], bool $onlyMine = false): void
    {
        $this->onlyMine = $onlyMine;

        $this->categories = collect($categories)
            ->map(fn ($c) => [
                'id' => (int) (is_array($c) ? $c['id'] : $c->id),
                'name' => (string) (is_array($c) ? $c['name'] : $c->name),
            ])
            ->values()
            ->all();

        $this->cities = collect($cities)
            ->map(fn ($c) => [
                'id' => (int) (is_array($c) ? $c['id'] : $c->id),
                'name' => (string) (is_array($c) ? $c['name'] : $c->name),
            ])
            ->values()
            ->all();

        $this->tags = collect($tags)
            ->map(fn ($t) => [
                'id' => (int) (is_array($t) ? $t['id'] : $t->id),
                'name' => (string) (is_array($t) ? $t['name'] : $t->name),
            ])
            ->values()
            ->all();

        // Normalize tagIds coming from URL to ints.
        $this->tagIds = collect($this->tagIds)->map(fn ($v) => (int) $v)->filter()->values()->all();
    }

    public function updated($property): void
    {
        if (in_array($property, ['search', 'categoryId', 'cityId', 'status', 'sort', 'tagIds'], true)) {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->categoryId = null;
        $this->cityId = null;
        $this->status = '';
        $this->tagIds = [];
        $this->sort = 'newest';

        $this->resetPage();
    }

    public function applyTag(int $tagId): void
    {
        $this->tagIds = [$tagId];
        $this->resetPage();
    }

    public function vote(int $itemId, int $value): void
    {
        abort_unless(Auth::check(), 403);

        if (!in_array($value, [-1, 1], true)) {
            return;
        }

        /** @var Vote|null $existing */
        $existing = Vote::query()
            ->where('item_id', $itemId)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing && $existing->value === $value) {
            $existing->delete();
            return;
        }

        Vote::query()->updateOrCreate(
            ['item_id' => $itemId, 'user_id' => Auth::id()],
            ['value' => $value]
        );
    }

    public function markGifted(int $itemId): void
    {
        abort_unless(Auth::check(), 403);

        /** @var Item $item */
        $item = Item::query()
            ->whereKey($itemId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($item->status === ItemStatus::Gifted) {
            return;
        }

        $item->status = ItemStatus::Gifted;
        $item->gifted_at = $item->gifted_at ?? now();
        $item->save();
    }

    protected function itemsQuery(): Builder
    {
        $query = Item::query()
            ->with([
                'user:id,name',
                'category:id,name',
                'city:id,name',
                'tags:id,name',
                'primaryPhoto:id,item_id,path,sort_order',
                'votes' => fn ($q) => $q->where('user_id', Auth::id())->select(['id', 'item_id', 'user_id', 'value']),
            ])
            ->withCount('comments')
            ->withSum('votes as score', 'value');

        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }

        if ($this->cityId) {
            $query->where('city_id', $this->cityId);
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        if (!empty($this->tagIds)) {
            $query->whereHas('tags', fn (Builder $t) => $t->whereIn('tags.id', $this->tagIds));
        }

        if ($this->onlyMine) {
            $query->where('user_id', Auth::id());
        }

        $query->search($this->search);

        if ($this->sort === 'top') {
            $query
                ->orderByDesc(DB::raw('COALESCE(score, 0)'))
                ->orderByDesc('created_at');
        } else {
            $query->orderByDesc('created_at');
        }

        return $query;
    }

    public function render()
    {
        $items = $this->itemsQuery()->paginate(12);

        return view('livewire.items.feed', [
            'items' => $items,
        ]);
    }
}
