<?php

namespace App\Livewire\Items;

use App\Models\Comment;
use App\Models\Item;
use App\Models\Vote;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Interactions extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public int $itemId;

    public string $commentBody = '';

    public function mount(int $itemId): void
    {
        $this->itemId = $itemId;

        // Ensure the item exists; visibility is handled by route auth + controller authorization.
        Item::query()->select('id')->findOrFail($this->itemId);
    }

    public function vote(int $value): void
    {
        abort_unless(Auth::check(), 403);

        if (!in_array($value, [-1, 1], true)) {
            return;
        }

        /** @var Vote|null $existing */
        $existing = Vote::query()
            ->where('item_id', $this->itemId)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing && $existing->value === $value) {
            $existing->delete();
            return;
        }

        Vote::query()->updateOrCreate(
            ['item_id' => $this->itemId, 'user_id' => Auth::id()],
            ['value' => $value]
        );
    }

    public function submitComment(): void
    {
        abort_unless(Auth::check(), 403);

        $validated = $this->validate([
            'commentBody' => ['required', 'string', 'min:1', 'max:1000'],
        ]);

        Comment::create([
            'item_id' => $this->itemId,
            'user_id' => Auth::id(),
            'body' => $validated['commentBody'],
        ]);

        $this->commentBody = '';
        $this->resetPage();
    }

    public function render()
    {
        $score = (int) Vote::query()
            ->where('item_id', $this->itemId)
            ->sum('value');

        $myVote = Vote::query()
            ->where('item_id', $this->itemId)
            ->where('user_id', Auth::id())
            ->value('value');

        $comments = Comment::query()
            ->where('item_id', $this->itemId)
            ->with('user:id,name')
            ->latest()
            ->paginate(10);

        return view('livewire.items.interactions', [
            'score' => $score,
            'myVote' => $myVote !== null ? (int) $myVote : null,
            'comments' => $comments,
        ]);
    }
}
