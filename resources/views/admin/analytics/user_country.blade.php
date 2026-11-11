@extends('admin.analytics.analytics_main')
@section('analytics.content')
<div class="row">
    <div class="col-lg-12">
        <!-- Example Bar Chart Card-->
        <div class="card mb-3">
            <div class="card-header">
                <i class="fa fa-list"></i> Total Users by Country
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                            <th>Country</th>
                            <th>TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($totalUsersByCountry as $indexkey => $resource)
                        <tr>
                            <td><strong>{{ getCountry($resource->id) }}</strong></td>
                            <td><a href="{{ URL::to('admin/users?country='.$resource->id) }}">{{ $resource->total }}</a></td>
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
