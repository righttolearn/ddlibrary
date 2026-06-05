@extends('layouts.main')

@section('title')
    @lang('Opt out of Analytics')
@endsection

@section('description')
    @lang('You can opt out of tracking here')
@endsection

@section('page_image')
    {{ getFile('public/img/logo-ddl-new.png') }}
@endsection

@push('styles')
    <style>
        .policies-content {
            line-height: 1.8;
        }
    </style>
@endpush

@section('content')
    <div class="container my-4">
        <div class="policies-content rounded shadow-sm">
            <!-- Analytics Opt-Out Section -->
            <div class="card border mt-4">
                <div class="card-body p-4">
                    <h2>@lang('Opt out of Analytics')</h2>

                    <div class="mb-">
                        <div class="d-flex gap-4">
                            <div class="d-flex flex-column">
                                <span class="small">Opt out of Google Analytics</span>
                                <button type="button" class="btn btn-primary" onclick="gaOptout()">
                                    @lang('Opt-out of Google Analytics')
                                </button>
                            </div>
                        </div>
                        <p class="mt-3 small mb-0" id="ga-optout-status"></p>
                    </div>

                    <div class="border-top pt-4">
                        <span class="small">Opt out of Matomo Analytics</span> <br>
                        <div id="matomo-opt-out"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-QZK5S8JQJN"></script>
    <script>
        // Source - https://stackoverflow.com/a
        // Posted by XTOTHEL, modified by community. See post 'Timeline' for change history
        // Retrieved 2025-11-12, License - CC BY-SA 4.0

        // Disable tracking if the opt-out cookie exists.
        var disableStr = 'ga-disable-G-QZK5S8JQJN';
        if (document.cookie.indexOf(disableStr + '=true') > -1) {
            window['ga-disable-G-QZK5S8JQJN'] = true; //this is what disables the tracking, note the measurement ID
        }
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-QZK5S8JQJN');

        // Opt-out function (this function is unchanged)
        function gaOptout() {
            document.cookie = disableStr + '=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/';
            window[disableStr] = true;

            // Update status message
            var statusEl = document.getElementById('ga-optout-status');
            if (statusEl) {
                statusEl.textContent = '{{ __('You have successfully opted out of Google Analytics tracking.') }}';
                statusEl.className = 'mt-3 small mb-0 text-success';
            }

            alert('{{ __('You have successfully opted out of Google Analytics tracking.') }}');
        }

        // Check and display current opt-out status on page load
        document.addEventListener('DOMContentLoaded', function() {
            var statusEl = document.getElementById('ga-optout-status');
            if (document.cookie.indexOf(disableStr + '=true') > -1) {
                if (statusEl) {
                    statusEl.textContent = '{{ __('You are currently opted out of Google Analytics tracking.') }}';
                    statusEl.className = 'mt-3 small mb-0 text-success';
                }
            } else {
                if (statusEl) {
                    statusEl.textContent = '{{ __('You are currently opted in to Google Analytics tracking.') }}';
                    statusEl.className = 'mt-3 small mb-0 text-muted';
                }
            }
        });
    </script>

    <!-- Matomo Opt-Out -->
    <script
        src="https://analytics.darakhtdanesh.org/index.php?module=CoreAdminHome&action=optOutJS&divId=matomo-opt-out&language=auto&showIntro=1">
    </script>
@endpush
