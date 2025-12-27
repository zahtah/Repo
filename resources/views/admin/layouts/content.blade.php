@extends('admin.master')
@section('content')
    <div class="content-wrapper">
        {{ $slot }}
    </div>
@endsection
@yield('scripts')
{{-- یا اگر از push استفاده می‌کنید: --}}
@stack('scripts')
