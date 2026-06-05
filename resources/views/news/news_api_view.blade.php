@extends('layouts.main')
@section('title')
{{ trim(strip_tags($news->title)) }}
@endsection
@section('description')
{{ trim(strip_tags(fixImage($news->summary, $news->id))) }}
@endsection
@section('page_image')
{{ getFile('public/img/logo-ddl-new.png') }}
@endsection
@section('content')
<section dir="@if ($news->language!='en'){{"rtl"}}@endif" class="general-content">
    <header>
        <h1>{{ $news->title }}</h1>
    </header>
    <article>
        {!! fixImage($news->body, $news->id) !!}
    </article>
    @if (isAdmin())
    <a href="{{ URL::to('news/edit/'.$news->id) }}">Edit</a>
    <a href="{{ URL::to('news/translate/'.$news->id.'/'.$news->tnid) }}">Translate</a>
    @endif
</section>
<style>
    .header{
        display:none;
    }
    img{
        max-width: 100%;
    }
</style>

<script>
    $('document').ready(function(){
        $('footer').remove();
        $('.hr-class').remove();
        $('#fb-root').hide(); // hide facebook chat box
    });
</script>
@endsection
