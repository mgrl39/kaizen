@extends('layouts.app')

@push('styles')
    <x-welcome-styles />
@endpush

@section('content')
    <x-hero-slider />
    <x-promo-section />
    <x-top-movies />
    <x-categories-section />
@endsection

@push('scripts')
    <x-welcome-scripts />
@endpush