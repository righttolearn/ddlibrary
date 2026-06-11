@extends('admin.layout')
@section('admin.content')
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="#">Menu</a>
        </li>
        <li class="breadcrumb-item active">Add Menu</li>
        </ol>
        <div class="row">
            <div class="col-lg-12">
                <!-- Example Bar Chart Card-->
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fa fa-list"></i>
                        <strong>
                            @if (! $new_menu )
                                Add {{ fixLanguage(Request::get('lang')) }} translation for ({{ $menu->title }}) menu
                            @else
                                Add a new menu
                            @endif
                        </strong>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            @include('layouts.messages')
                            <form method="POST" action="{{ route('store_menu') }}">
                            @csrf
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <tbody>
                                <tr>
                                    <td>
                                        <strong>Title</strong>
                                    </td>
                                    <td>
                                        <input type="text" name="title" class="form-control" value="" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Location</strong>
                                    </td>
                                    <td>
                                        <select name="location" id="loc" onchange="getParents()" required>
                                            <option value=""></option>
                                            @foreach($locations AS $lc)
                                            <option value="{{ $lc }}" {{ ($menu->location==$lc?"selected":"") }}>{{ $lc }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Path</strong>
                                    </td>
                                    <td>
                                        <input type="text" name="path" class="form-control" value="{{ $menu->path }}" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Language</strong>
                                    </td>
                                    <td>
                                        <select name="language" id="lang" onchange="getParents()" required>
                                            <option value="">- @lang('None') -</option>
                                            @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                            <option value="{{ $localeCode }}" {{ Request::get('lang') == $localeCode ? "selected" : "" }}>{{ $properties['native'] }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Parents</strong>
                                    </td>
                                    <td>
                                        <select name="parent" id="parent">
                                            <option value="">- No Parent -</option>
                                            @foreach($parents AS $key=>$value)
                                            <option value="{{ $key }}" {{ ($menu->parent==$key?"selected":"") }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Status</strong>
                                    </td>
                                    <td>
                                        <select name="status" id="status">
                                            <option value="0" {{ (isset($details) && $details?->status==0 ? "selected" : "") }}>Hidden</option>
                                            <option value="1" {{ (isset($details) && $details?->status==1 ? "selected" : "") }}>Visible</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Weight</strong>
                                    </td>
                                    <td>
                                        <input type="text" name="weight" class="form-control" value="" required>
                                        <input type="hidden" name="tnid" value="{{ $menu->tnid }}">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <input class="btn btn-outline-dark" type="submit" value="Save">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid-->
<!-- /.content-wrapper-->
@push('scripts')
    @vite('resources/assets/js/app.jsx')
    <script>
        $(document).ready(function(){
            $('#country').trigger('change');
            getParents();
        });

        function getParents()
        {
            $.get('{{ URL('admin/menu/ajax_get_parents') }}', {lang:$('#lang').val(), loc:$('#loc').val(), id:$('#parent').val()}, 
            function(data){
            $('#parent').html(data);
            //alert(data);
            });
        }
    </script> 
@endpush
@endsection
