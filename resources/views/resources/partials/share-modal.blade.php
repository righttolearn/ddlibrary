<div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareModalLabel">@lang('Share this resource')</h5>
            </div>
            <div class="modal-body row justify-content-center social-share">
                <a href="https://twitter.com/intent/tweet?url={{ Request::url() }}"
                   target="_blank" title="@lang("Share to Twitter")"
                   class="col-md-2 text-center text-decoration-none">
                    <i class="ph-fill ph-twitter-logo"></i>
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ Request::url() }}"
                   target="_blank" title="@lang("Share to Facebook")"
                   class="col-md-2 text-center text-decoration-none">
                    <i class="ph-fill ph-facebook-logo"></i>
                </a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ Request::url() }}"
                   target="_blank" title="@lang("Share to LinkedIn")"
                   class="col-md-2 text-center text-decoration-none">
                    <i class="ph-fill ph-linkedin-logo"></i>
                </a>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">@lang('Close')</button>
            </div>
        </div>
    </div>
</div>
