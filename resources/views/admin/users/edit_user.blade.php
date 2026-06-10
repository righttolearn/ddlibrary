@extends('admin.layout')
@section('admin.content')
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="#">Users</a>
        </li>
        <li class="breadcrumb-item active">Users Details</li>
        </ol>
        <div class="row">
            <div class="col-lg-12">
                <!-- Example Bar Chart Card-->
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fa fa-list"></i> Update user details for <strong>{{ $user->username }}</strong>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            @include('layouts.messages')
                            <form method="POST" action="{{ route('update_user', $user->id) }}">
                            @csrf
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <tbody>
                                <tr>
                                    <td>
                                        <strong>Username</strong>
                                    </td>
                                    <td>
                                        <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Password</strong>
                                    </td>
                                    <td>
                                        <input type="text" name="password" class="form-control" placeholder="Only fill this if you want to change the user's password">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Email</strong>
                                    </td>
                                    <td>
                                        <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Status</strong>
                                    </td>
                                    <td>
                                        <select name="status" required>
                                            <option value=""></option>
                                            <option value="1" {{ ($user->status==1?"selected":"") }}>Active</option>
                                            <option value="0" {{ ($user->status==0?"selected":"") }}>Not Active</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>First Name</strong>
                                    </td>
                                    <td>
                                        <input type="text" name="first_name" class="form-control" value="{{ $user->profile?->first_name }}" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Last Name</strong>
                                    </td>
                                    <td>
                                        <input type="text" name="last_name" class="form-control" value="{{ $user->profile?->last_name }}" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Gender</strong></td>
                                    <td>
                                        <select name="gender" required>
                                            <option value=""></option>
                                            <option value="Male" {{ ($user->profile?->gender=="Male"?"selected":"") }}>Male</option>
                                            <option value="Female" {{ ($user->profile?->gender=="Female"?"selected":"") }}>Female</option>
                                            <option value="None" {{ ($user->profile?->gender=="None"?"selected":"") }}>Prefer not to say</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Role</strong></td>
                                    <td>
                                        <select name="role" required>
                                            <option value=""></option>
                                            @foreach($roles AS $role)
                                            <option value="{{ $role->id }}" {{ (@$user->role->role_id==$role->id?"selected":"") }}>{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Phone</strong></td>
                                    <td><input type="text" name="phone" class="form-control" value="{{ $user->profile?->phone }}"></td>
                                </tr>
                                <tr>
                                    <td><strong>Country</strong></td>
                                    <td>
                                        Country: {{ $user->profile->country }} <br/>
                                        City: {{ $user->profile->city }} <br/>
                                        <select name="country" id="country" onchange="javascript:populate(this,'city', {{ json_encode($provinces) }})" required>
                                            @foreach($countries AS $cn)
                                            <option value="{{ $cn->tnid }}" {{ ($user->profile?->country==$cn->tnid?"selected":"") }}>{{ $cn->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>City</strong></td>
                                    <td>
                                        <select name="city" id="city">
                                            <option value=""> - None -</option>
                                            @foreach($provinces AS $pn)
                                            <option value="{{ $pn->tnid }}" {{ ($user->profile?->city==$pn->tnid?"selected":"") }}>{{ $pn->name }}</option>
                                            @endforeach
                                        </select>
                                    <input type="text" class="form-control" name="city_other" id="js-text-city" size="40" value="{{ $user->profile?->city }}" style="display:none;">
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created At</strong></td>
                                    <td>{{ $user->created_at }}</a></td>
                                </tr>
                                <tr>
                                    <td><strong>Accessed At</strong></td>
                                    <td>{{ $user->accessed_at }}</a></td>
                                </tr>
                                </tbody>
                            </table>
                            <input class="btn btn-outline-dark" type="submit" value="Update">
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
        });
    </script> 
@endpush
@endsection
