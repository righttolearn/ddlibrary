@extends('admin.layout')
@section('admin.content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <!-- Breadcrumbs-->
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ URL::to('admin') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Reports</li>
                <li class="breadcrumb-item active">Resource Reports</li>
            </ol>
            <div class="card mb-3 col-lg-8">
                <div class="card-header">
                    <i class="fa fa-bar-chart"></i> Total resources by subjects and grade level breakdown for each subject</div>
                <div class="card-body">
                    <form action="{{ URL::to('admin/reports/resources/subjects') }}">
                        <div class="form-group">
                            <label for="lang_select">Select language:</label>
                            <select class="form-control col-sm-3" id="lang_select" name="lang">
                                @foreach($supported_locales as $locale => $properties)
                                    <option value="{{ $locale }}">{{ $properties['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-outline-primary">Fetch CSV report</button>
                    </form>
                </div>
            </div>
            <div class="card mb-3 col-lg-8">
                <div class="card-header">
                    <i class="fa fa-bar-chart"></i> Total resources by languages and grade level breakdown for each language</div>
                <div class="card-body">
                    <a href="{{ URL::to('admin/reports/resources/languages') }}" class="btn btn-outline-primary">Fetch CSV report</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid-->
        <!-- /.content-wrapper-->
@endsection
