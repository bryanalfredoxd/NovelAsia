@extends('layouts.app')

@section('title', 'Inicio - NovelAsia')

@section('content')
<main class="app-container pt-20 pb-20 md:pb-12">


    @include('home.sections.hero')

    <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
        <div class="flex-1">
            @include('home.sections.updates')
        </div>

        <aside class="w-full lg:w-80 shrink-0">
            @include('home.sections.sidebar')
        </aside>
    </div>
</main>

@include('home.sections.mobile-nav')
@endsection
