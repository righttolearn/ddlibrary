@if (count($files))
    <div class="row g-2">
        @foreach ($files as $file)
            @php
                $fileName = getResourceImage($file->name, true);
            @endphp
            <div class="col-md-12 col-lg-4" data-id="{{ $file->id }}"
                onclick="selectImage('{{ $file->id }}', '{{ $fileName }}')">
                <div
                    class="position-relative border border-secondary rounded overflow-hidden w-100  h-100 image-{{ $file->id }}">
                    <div class="ratio ratio-1x1">
                        <img src="{{ $fileName }}" alt="{{ $file->label }}" class="img-fluid">
                    </div>
                    <p class="text-center mb-0">{{ $file->label }}</p>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="border border-secondary rounded p-3 w-100">
        <h2 class="text-center text-danger">@lang('Resource not found')</h2>
    </div>
@endif

@if ($files->hasPages())
    <div class="pagination-container mt-3">
        {{ $files->links() }}
    </div>
@endif
