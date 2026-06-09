@extends('admin.layout')

@section('admin.content')
  <div class="content-wrapper">
    <div class="container-fluid">

      <!-- Breadcrumbs-->
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{ URL::to('admin') }}">Dashboard</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ URL::to('admin/surveys') }}">Surveys</a>
          </li>
        <li class="breadcrumb-item active">{{ $survey?->name}}</li>
      </ol>

      <!-- Surveys Answers DataTables -->
      <div class="card mb-3">
        <div class="card-header">
          <i class="fa fa-list"></i> Questions
        </div>

        <div class="card-body">
            @if (session('status'))
              <div class="alert alert-success">
                  {{ session('status') }}
              </div>
            @endif
            @if ($survey->language == 'en')
              <a href="{{ URL::to('admin/survey/question/add/'.$survey?->id) }}" class="btn btn-success pull-right" style="margin-bottom: 10px">
                <span class="fa fa-plus"></span> Add New
              </a>
            @endif
            <span>Total: <strong>{{count($survey_questions)}}</strong></span>
            <table class="table table-bordered" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>Text</th>
                  <th>Language</th>
                  <th>Type</th>
                  <th>OPERATIONS</th>
                </tr>
              </thead>

              <tbody>
                @foreach ($survey_questions as $indexkey => $survey_question)
                  <tr>
                    <td>{{ $survey_question-> text }}</td>
                    <td>{{ fixLanguage($survey_question->language) }}</td>
                    <td>
                      @if ($survey_question->type == 'single_choice')
                        <span>Single Choice</span>
                      @elseif ($survey_question->type == 'multi_choice')
                        <span>Multiple Choice</span>
                      @else
                        <span>Descriptive</span>
                      @endif
                    </td>
                    <td style="display: flex;">
                      @if ($survey_question->type != 'descriptive')
                        <a href="{{ URL::to('admin/survey/'.$survey?->id.'/question/'.$survey_question->id.'/view_options') }}" class="badge badge-primary" style="margin-right:5px;">Options</a>
                      @endif
                      <a href="{{ URL::to('admin/survey/'.$survey?->id.'/question/view/'.$survey_question->id.'/'.$survey_question->tnid) }}" class="badge badge-primary" style="margin-right: 5px;">Translations</a>
                      <a href="javascript:void(0)" id="{{$survey_question->id}}" onclick="confirm(this.id);" class="badge badge-danger">Delete</a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
    <!-- /.container-fluid-->
    <!-- /.content-wrapper-->


    <!-- Modal for confirmation -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Are you sure want to delete this question?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success btn-sm" data-dismiss="modal">No</button>
            <a class="delete"><button type="button" class="btn btn-danger btn-sm confirm">Yes</button></a>
          </div>
        </div>
      </div>
    </div>

    <!--javascript function for deleting a survey/starts-->
    <script>
        function confirm(id){
            $("#confirmModal").modal('show');
            $('.confirm').click(function(){
                var url='/admin/survey/question/delete/'+id;
                $('a.delete').attr('href',url);
            });
        }
    </script>
    <!--end-->

  @endsection
