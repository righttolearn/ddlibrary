@extends('admin.layout')
@section('admin.content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ URL::to('admin') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Resource Literacy Level</li>
            </ol>
            @include('layouts.messages')
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fa fa-table"></i> All Resource Literacy Level
                    </div>
                    <a href="{{ route('literacy_levels.edit_or_create') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> Create New
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center" style="width: 50px;">#</th>
                                    <th>English</th>
                                    <th>Persian</th>
                                    <th>Pashto</th>
                                    <th>Uzbeki</th>
                                    <th>Munji</th>
                                    <th>Nooristani</th>
                                    <th>Savji</th>
                                    <th>Sheghnani</th>
                                    <th>Pashai</th>
                                    <th class="text-center" style="width: 100px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($literacyLevels as $tnid => $translations)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        @foreach ($languages as $localeCode => $language)
                                            <td>{{ $translations[$localeCode] ?? '' }}</td>
                                        @endforeach
                                        <td class="text-center">
                                            <a href='{{ route("literacy_levels.edit_or_create",$tnid) }}'
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                        </td>
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
