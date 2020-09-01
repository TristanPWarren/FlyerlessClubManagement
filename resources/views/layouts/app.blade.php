@extends('bristolsu::base')

@section('content')
    <div id="flyerless-club-management-root">
        @yield('module-content')
    </div>
@endsection

@push('styles')
    <link href="{{ asset('modules/flyerless-club-management/css/module.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('modules/flyerless-club-management/js/module.js') }}"></script>
@endpush
