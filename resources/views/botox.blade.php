@extends('layouts.home')

@section('content')
@php
    $page_link =request()->segment(2);
    $page = config('mubashir.pages')[$page_link]??null;
    if($page == null){
        header("Location: " . URL::to('/'));
        exit();
    }
    $allPages = $page;
@endphp
<!-- main-content start -->
    <section>
        <div class="container-fluid py-4 px-3">
            <div class="text-center">
                <span class="h1 fw-bold text-theme">Botox</span>
            </div>
            @foreach ($allPages as $key => $allPage)
            
                @php
                    $data['page_link'] = $key;
                @endphp

                @include("includes/".$allPage,$data)
                
            @endforeach

        </div>
    </section>
<!-- main-content end -->

@endsection
