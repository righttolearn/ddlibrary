@extends('admin.layout')
@section('admin.content')
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="{{ URL::to('admin') }}">Dashboard</a>
      </li>
      <li class="breadcrumb-item active">Flags</li>
    </ol>
    <!-- Example DataTables Card-->
    <div class="card mb-3">
      <div class="card-header">
        <i class="fa fa-table"></i> All Flags</div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>NO</th>
                <th>Title</th>
                <th>User</th>
                <th>Type</th>
                <th>Details</th>
                <th>Created</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th>NO</th>
                <th>Title</th>
                <th>User</th>
                <th>Type</th>
                <th>Details</th>
                <th>Created</th>
              </tr>
            </tfoot>
            <tbody>
            <?php 
            $flagTypes = [1 => "Graphic Violence",
                            2=>"Graphic Sexual Content",
                            3=>"Spam, Scam or Fraud",
                            4=>"Broken or Empty Data"];
            ?>
            @foreach ($flags as $indexkey => $flag)
              <tr>
                <td>{{ (($flags->currentPage() - 1) * 20)+$indexkey + 1 }}</td>
                <td><a href="{{ URL::to('resources/view/'.$flag->resource_id) }}">{{ $flag->resource->title }}</a></td>
                <td><a href="{{ URL::to('users/view/'.$flag->user_id) }}">{{ $flag->user->username }}</a></td>
                <td>{{ $flagTypes[$flag->type] ?? 'Unknown' }}</td>
                <td>{{ $flag->details }}</td>
                <td>{{ $flag->created_at->diffForHumans() }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        {{ $flags->links() }}
      </div>
    </div>
  </div>
  <!-- /.container-fluid-->
  <!-- /.content-wrapper-->
  @endsection
