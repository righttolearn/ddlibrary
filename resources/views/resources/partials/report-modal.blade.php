<div class="modal fade" id="reportModal" tabindex="-1"
     aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('flag') }}">
                @honeypot
                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel">@lang('Report this resource')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="type">
                            @lang('Type')
                        </label>
                        <select name="type" class="form-select{{ $errors->has('type') ? ' is-invalid' : '' }}" required>
                            <option value="">-</option>
                            <option value="1">@lang('Graphic Violence')</option>
                            <option value="2">@lang('Graphic Sexual Content')</option>
                            <option value="3">@lang('Spam, Scam or Fraud')</option>
                            <option value="4">@lang('Broken or Empty Data')</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="details">
                            @lang('Details')
                        </label>
                        <textarea name="details" class="form-control" rows="5" required></textarea>
                    </div>
                    <input type="hidden" value="{{ $resource->id }}" name="resource_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn-primary">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>
