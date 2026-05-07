@extends('themes.hotel.luxury.views.layouts.app')
@section('title','Luxury Home')

@section('content')
    @include('themes.hotel.luxury.views.components.hero')
    <section class="listings">
        @include('themes.hotel.luxury.views.components.hotel-card')
        @include('themes.hotel.luxury.views.components.hotel-card')
    </section>
@endsection
