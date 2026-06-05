@extends('layouts.main')
@section('title')
{{ trim(strip_tags($page->title)) }}
@endsection
@section('description')
{{ trim(strip_tags(fixImage($page->summary, $page->id))) }}kk
@endsection
@section('page_image')
{{ getFile('public/img/logo-ddl-new.png') }}
@endsection
@section('content')
<section dir="@if ($page->language!='en'){{'rtl'}}@else{{'ltr'}}@endif" class="general-content">
    <header>
        <h1>{{ $page->title }}</h1>
    </header>
    <article>
        {!! fixImage($page->body, $page->id) !!}
    </article>
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
