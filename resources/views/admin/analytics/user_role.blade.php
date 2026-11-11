@extends('admin.analytics.analytics_main')
@section('analytics.content')
<div class="row">
    <div class="col-lg-12">
        <!-- Example Bar Chart Card-->
        <div class="card mb-3">
            <div class="card-header">
                <i class="fa fa-list"></i> Total Users by Roles
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                            <th>Role</th>
                            <th>TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($totalResourcesByRoles as $indexkey => $resource)
                        <tr>
                            <td><strong>{{ $resource->name }}</strong></td>
                            <td><a href="{{ URL::to('admin/users?role='.$resource->id) }}">{{ $resource->total }}</a></td>
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
