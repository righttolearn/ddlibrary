@extends('admin.layout')
@section('admin.content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ URL::to('admin') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">
                    <a href="{{ route('resource_types.index') }}">Resource Type</a>
                </li>
            </ol>
            @include('layouts.messages')
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fa fa-table"></i> {{ $tnid ? 'Update Resource Type' : 'Create Resource Type' }}
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <form method="POST" action="{{ route('resource_types.store_or_update') }}">
                            @csrf
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <tbody>
                                    @foreach ($languages as $localeCode => $language)
                                        @php
                                            $data = $terms[$localeCode];
                                        @endphp
                                        <tr>
                                            {{-- Language --}}
                                            <td><strong>{{ $language['name'] }}</strong></td>

                                            {{-- Name --}}
                                            <td>
                                                <input type="text" name="name[{{ $localeCode }}]" class="form-control"
                                                    value="{{ $data['term']['name'] }}"
                                                    placeholder="{{ $language['name'] }}">
                                                <input type="hidden" name="id[{{ $localeCode }}]" value="{{ $data['term']['id'] }}">
                                            </td>

                                            {{-- Weight --}}
                                            <td>
                                                <input type="number" name="weight[{{ $localeCode }}]"
                                                    value="{{ $data['term']['weight'] }}" class="form-control"
                                                    placeholder="Enter {{ $language['weight'] }} weight">
                                            </td>

                                            {{-- Parent --}}
                                            <td>
                                                <select name="parent[{{ $localeCode }}]" class="form-control">
                                                    <option value="0">...</option>
                                                    @foreach ($parents->where('language', $localeCode) as $parent)
                                                        <option value="{{ $parent->id }}" @selected($data['term']['taxonomyHierarchy']['parent'] == $parent->id)>
                                                            {{ $parent->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <input type="hidden" name="tnid" value="{{ $tnid }}">
                            <input class="btn btn-outline-dark" type="submit"
                                value="@if ($tnid) Update @else Create @endif">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
