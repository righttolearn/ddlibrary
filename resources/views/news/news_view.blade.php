@extends('layouts.main')
@section('title')
{{ trim(strip_tags($news->title)) }}
@endsection
@section('description')
{{ trim(strip_tags(fixImage($news->summary, $news->id, true))) }}
@endsection
@section('page_image')
{{ getFile('public/img/logo-ddl-new.png') }}
@endsection
@section('content')
<section class="general-content">
    @include('layouts.messages')
    <header>
        <h1>{{ $news->title }}</h1>
    </header>
    <article>
        {!! fixImage($news->body, $news->id, true) !!}
    </article>
    @if (isAdmin())
    <a class="btn btn-primary mt-2" href="{{ URL::to('news/edit/'.$news->id) }}">Edit</a>
    <a class="btn btn-primary mt-2" href="{{ URL::to('news/translate/'.$news->id.'/'.$news->tnid) }}">Translate</a>
    @endif
</section>
@endsection 
