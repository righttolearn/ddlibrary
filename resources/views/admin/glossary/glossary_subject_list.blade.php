@extends('admin.layout')
@section('admin.content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <!-- Breadcrumbs-->
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ URL::to('admin') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">Glossary</li>
                <li class="breadcrumb-item active">Subjects</li>
            </ol>
            @if (session('status'))
                <br>
                <div id="add_success" class="alert alert-success">
                    {{ (session('status')) }}
                </div>
            @endif
            <!-- Example DataTables Card-->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-table"></i> Glossary subjects
                    <a href="{{ URL::to('admin/glossary_subjects/create') }}" class="btn btn-primary float-right">Create a new subject</a>
                </div>
                <div class="card-body">
                    <span>Total: <strong>{{ $glossary_subjects->total() }}</strong></span>
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>NO</th>
                                <th>ENGLISH</th>
                                <th>FARSI</th>
                                <th>PASHTO</th>
                                <th>MUNJI</th>
                                <th>NURISTANI</th>
                                <th>PASHAYI</th>
                                <th>SHUGHNI</th>
                                <th>SWAHILI</th>
                                <th>UZBEK</th>
                                <th>OPERATIONS</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>NO</th>
                                <th>ENGLISH</th>
                                <th>FARSI</th>
                                <th>PASHTO</th>
                                <th>MUNJI</th>
                                <th>NURISTANI</th>
                                <th>PASHAYI</th>
                                <th>SHUGHNI</th>
                                <th>SWAHILI</th>
                                <th>UZBEK</th>
                                <th>OPERATIONS</th>
                            </tr>
                            </tfoot>
                            <tbody>
                            @foreach ($glossary_subjects as $id => $subject)
                                <tr>
                                    <td>{{ (($glossary_subjects->currentPage() - 1) * 10) + $id + 1 }}</td>
                                    <td>{{ $subject->en }}</td>
                                    <td>{{ $subject->fa }}</td>
                                    <td>{{ $subject->ps }}</td>
                                    <td>{{ $subject->mj }}</td>
                                    <td>{{ $subject->no }}</td>
                                    <td>{{ $subject->pa }}</td>
                                    <td>{{ $subject->sh }}</td>
                                    <td>{{ $subject->sw }}</td>
                                    <td>{{ $subject->uz }}</td>
                                    <td>
                                        <a href="{{ URL::to('admin/glossary_subjects/edit/'.$subject->id) }}">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $glossary_subjects->links() }}
                </div>
            </div>
        </div>
        <!-- /.container-fluid-->
        <!-- /.content-wrapper-->
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $(function() {
                let div = $('#add_success');
                if (div) {
                    div.delay(5000).fadeOut('slow');
                }
            });
        });
    </script>
@endpush
