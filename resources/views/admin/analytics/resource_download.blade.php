@extends('admin.analytics.analytics_main')
@section('analytics.content')
<div class="row">
    <div class="col-lg-12">
        <!-- Example Bar Chart Card-->
        <div class="card mb-3">
            <div class="card-header">
                <i class="fa fa-list"></i> Top Downloaded Resources in the last 30 days
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                            <th>Resource</th>
                            <th>TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($downloadCount as $dc)
                        <tr>
                            <td><a href="{{ URL::to($dc->language.'/'.'resource/'.$dc->resource_id)}}">{{ $dc->title }}</a></td>
                            <td>{{ $dc->total }}</a></td>
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
