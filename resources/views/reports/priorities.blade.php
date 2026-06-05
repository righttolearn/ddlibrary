@extends('layouts.main')
@section('title')
    @lang('Resource priorities - Darakht-e Danesh Library')
@endsection
@section('description')
    @lang('Resource priorities')
@endsection
@section('page_image')
    {{ getFile('public/img/logo-ddl-new.png') }}
@endsection

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-5">
                <h2>@lang('Resource priorities based on subjects')</h2>
            </div>
            @if (isLibraryManager() || isAdmin())
                <div class="col-md-2 offset-md-5">
                    <a href="{{ URL::to('resources/priorities/exclusion') }}" class="btn btn-primary mt-2">@lang('Exclusion list') <i class="fas fa-forward"></i></a>
                </div>
            @endif
        </div>
    </div>

    <div id="success_msg" style="
    margin-top: 11px;
    text-align: center;
    color: #5cb85c;
    font-size: 22px;
    display: none;"></div>

    @if (session('status'))
        <br>
        <div id="add_success" class="alert alert-success">
            {{ (session('status')) }}
        </div>
    @endif

    <table class="table table-hover mt-2">
        <thead class="thead-light">
            <tr>
                <th scope="col">#</th>
                <th scope="col">@lang('Subject')</th>
                <th scope="col" class="text-center">@lang('No. of resources')</th>
                @if (isLibraryManager() || isAdmin())
                    <th scope="col" class="text-center">@lang('Exclude')</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($subjects_list as $subject)
                <tr>
                    <th scope="row" id="{{ $loop->iteration }}">{{ $loop->iteration }}</th>
                    <td>{{ $subject['name'] }}</td>
                    <td class="text-center">{{ $subject['count'] }}</td>
                    @if (isLibraryManager() || isAdmin())
                        <td class="text-center">
                            <button type="button" class="btn exclude_from_list" data-id="{{ $subject['id'] }}">
                                <i class="fas fa-ban"></i>
                            </button>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $(".exclude_from_list").click(function (event){
                if(confirm('{{ trans("Are you sure you would like to exclude the subject from the list?") }}')) {
                    let source = event.currentTarget;
                    let id = parseInt(source.getAttribute('data-id'));
                    $.ajax({
                        type: 'POST',
                        url: 'priorities/exclusion/add/' + id,
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function() {
                            let div = $('#success_msg');
                            div.html('{{ trans("Subject has been excluded. Page will reload now.") }}').fadeIn('slow');
                            div.delay(5000).fadeOut('slow');
                            location.reload();
                        },
                        error: function() {
                            console.log('{{ trans("Request to exclude a subject failed. File a bug request.") }}');
                        }
                    });
                }
            });
            $(function() {
                let div = $('#add_success');
                if (div) {
                    div.delay(5000).fadeOut('slow');
                }
            });
        });
    </script>
@endpush
