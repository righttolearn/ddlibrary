<div class="col-md-8 mt-4">
    <h5 class="mb-3">{{ $comments->count() }} @lang('Comment(s)')</h5>

    <form method="POST" action="{{ route('comment') }}" class="mb-4">
        @csrf
        @honeypot
        <input type="hidden" value="{{ $resource->id }}" name="resource_id">
        <div class="mb-3">
            <label for="commentTextArea" class="form-label">
                @if (Auth::check())
                    @lang('Enter your comment below')
                @else
                    @lang('Please login to add a comment')
                @endif
            </label>
            <textarea class="form-control" name="comment" id="commentTextArea" rows="3"
                      @if (!Auth::check()) disabled @endif required></textarea>
        </div>
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary" @if (!Auth::check()) disabled @endif>
                @lang('Submit')
            </button>
        </div>
    </form>

    @foreach ($comments as $cm)
        <div class="d-flex gap-3 mb-3">
            <div class="flex-shrink-0">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold"
                     style="width: 40px; height: 40px;">
                    {{ strtoupper(substr($cm->user->username, 0, 1)) }}
                </div>
            </div>
            <div class="flex-grow-1">
                <div class="card border-0 bg-light p-3 rounded-3">
                    <p class="mb-1">{{ $cm->comment }}</p>
                    <div class="d-flex gap-2 align-items-center">
                        <small class="fw-semibold">{{ $cm->user->username }}</small>
                        <small class="text-muted">{{ $cm->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
