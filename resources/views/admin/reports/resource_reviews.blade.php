@extends('admin.layout')
@section('admin.content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ URL::to('admin') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Resource Review Report</li>
            </ol>

            @include('layouts.messages')

            <div class="card mb-3">
                <div class="card-header"><i class="fa fa-filter"></i> Filters</div>
                <div class="card-body">
                    <form method="GET" action="{{ route('resource-review-report') }}">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="title">Resource name</label>
                                <input id="title" type="text" name="title" value="{{ request('title') }}"
                                       class="form-control" placeholder="Search by name...">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="language">Language</label>
                                <select id="language" class="form-control" name="language">
                                    <option value="">All languages</option>
                                    @foreach ($languages as $key => $language)
                                        <option value="{{ $key }}" @selected(request('language') === $key)>
                                            {{ $language['native'] ?? $language['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="date_from">Reviewed from</label>
                                <input id="date_from" type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="date_to">Reviewed to</label>
                                <input id="date_to" type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="review_status">Review status</label>
                                <select id="review_status" class="form-control" name="review_status">
                                    <option value="">All resources</option>
                                    <option value="reviewed" @selected(request('review_status') === 'reviewed')>Reviewed</option>
                                    <option value="unreviewed" @selected(request('review_status') === 'unreviewed')>Not reviewed</option>
                                </select>
                            </div>
                            <div class="col-md-1 mb-3 d-flex align-items-end">
                                <button class="btn btn-primary btn-block" type="submit">Filter</button>
                            </div>
                        </div>
                        <a href="{{ route('resource-review-report') }}" class="btn btn-outline-secondary btn-sm">Clear filters</a>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fa fa-table"></i> Resource Reviews</span>
                    <strong>Total review hours: {{ number_format((float) $total_review_hours, 2) }}</strong>
                </div>
                <div class="card-body">
                    <p>Total resources: <strong>{{ $resources->total() }}</strong></p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Resource name</th>
                                    <th>Language</th>
                                    <th>Reviewed at</th>
                                    <th>Reviewed by</th>
                                    <th>Review hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($resources as $index => $resource)
                                    <tr>
                                        <td>{{ $resources->firstItem() + $index }}</td>
                                        <td>
                                            <a href="{{ URL::to($resource->language.'/resource/'.$resource->id) }}">{{ $resource->title }}</a>
                                        </td>
                                        <td>{{ fixLanguage($resource->language) }}</td>
                                        <td>{{ $resource->reviewed_at?->format('Y-m-d H:i') ?? 'Not reviewed' }}</td>
                                        <td>{{ $resource->reviewer?->username ?? '—' }}</td>
                                        <td style="min-width: 220px">
                                            <form method="POST" action="{{ route('resource-review-update', $resource->id) }}" class="form-inline flex-nowrap">
                                                @csrf
                                                <input type="number" name="review_hours" value="{{ $resource->review_hours }}"
                                                       min="0" max="100" step="0.01"
                                                       class="form-control form-control-sm mr-2" style="width: 110px" required>
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    {{ $resource->reviewed_at ? 'Update' : 'Save' }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center">No resources found.</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-right">Total review hours</th>
                                    <th>{{ number_format((float) $total_review_hours, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    {{ $resources->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
