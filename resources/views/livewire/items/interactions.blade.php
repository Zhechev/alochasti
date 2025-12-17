<div class="position-relative">
    <div wire:loading.flex class="position-absolute top-0 start-0 w-100 h-100 align-items-center justify-content-center bg-white bg-opacity-75" style="z-index: 10;">
        <div class="spinner-border text-primary" role="status" aria-label="{{ __('Loading') }}"></div>
    </div>

    <div class="d-flex align-items-center justify-content-between">
        <div class="btn-group" role="group" aria-label="{{ __('Voting controls') }}">
            <button type="button"
                    class="btn btn-sm {{ $myVote === 1 ? 'btn-success' : 'btn-outline-success' }}"
                    wire:click="vote(1)"
                    wire:loading.attr="disabled">
                {{ __('Upvote') }}
            </button>
            <button type="button"
                    class="btn btn-sm {{ $myVote === -1 ? 'btn-danger' : 'btn-outline-danger' }}"
                    wire:click="vote(-1)"
                    wire:loading.attr="disabled">
                {{ __('Downvote') }}
            </button>
        </div>

        <div class="small">
            <span class="fw-semibold">{{ $score }}</span>
            <span class="text-secondary">{{ __('score') }}</span>
        </div>
    </div>

    <hr class="my-3">

    <form wire:submit.prevent="submitComment">
        <label class="form-label">{{ __('Add a comment') }}</label>
        <textarea class="form-control" rows="3" wire:model.defer="commentBody" placeholder="{{ __('Write something helpful...') }}"></textarea>
        @error('commentBody') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

        <div class="mt-2 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary btn-sm" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('Post comment') }}</span>
                <span wire:loading class="spinner-border spinner-border-sm" aria-hidden="true"></span>
            </button>
        </div>
    </form>

    <hr class="my-3">

    <div class="d-flex align-items-center justify-content-between mb-2">
        <div class="fw-semibold">{{ __('Comments') }}</div>
        <div class="text-secondary small">{{ $comments->total() }}</div>
    </div>

    @if ($comments->count() === 0)
        <div class="text-secondary">{{ __('No comments yet.') }}</div>
    @else
        <div class="list-group list-group-flush">
            @foreach ($comments as $comment)
                <div class="list-group-item px-0">
                    <div class="d-flex justify-content-between">
                        <div class="fw-semibold">{{ $comment->user->name }}</div>
                        <div class="text-secondary small">{{ $comment->created_at->diffForHumans() }}</div>
                    </div>
                    <div style="white-space: pre-wrap;">{{ $comment->body }}</div>
                </div>
            @endforeach
        </div>

        <div class="mt-2">
            {{ $comments->links() }}
        </div>
    @endif
</div>
