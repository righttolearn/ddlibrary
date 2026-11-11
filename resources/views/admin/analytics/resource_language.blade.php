@extends('admin.analytics.analytics_main')
@section('analytics.content')
<div class="row">
    <div class="col-lg-12">
        <!-- Example Bar Chart Card-->
        <div class="card mb-3">
            <div class="card-header">
                <i class="fa fa-list"></i> Total Resources by Language
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                            <th>LANGUAGE</th>
                            <th>TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($totalResources as $indexkey => $resource)
                        <tr>
                            <td><strong>{{ fixLanguage($resource->language) }}</strong></td>
                            <td><a href="{{ URL::to('admin/resources?language='.$resource->language) }}">{{ $resource->total }}</a></td>
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
