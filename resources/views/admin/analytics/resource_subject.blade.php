@extends('admin.analytics.analytics_main')
@section('analytics.content')
<div class="row">
    <div class="col-lg-12">
        <!-- Example Bar Chart Card-->
        <div class="card mb-3">
            <div class="card-header">
                <i class="fa fa-list"></i> Total Resources by Subject Area
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                            <th>Subject Area</th>
                            <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($totalResourcesBySubject as $indexkey => $resource)
                        <tr>
                            <td>{{ $resource->name }}</td>
                            <td><a href="{{ URL::to('admin/resources?language='.$resource->language.'&subject_area='.$resource->id) }}">{{ $resource->total }}</a></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
