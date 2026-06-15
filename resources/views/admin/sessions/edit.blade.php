@component('admin.layouts.content')
    @section('title', 'ویرایش جلسه')

@section('content')
<div class="container">
    <h1 class="mb-4">ویرایش جلسه شماره {{ $session->session_number }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('sessions.update', $session) }}" method="POST">
        @method('PUT')
        @include('admin.sessions._form', ['submitLabel' => 'بروزرسانی جلسه'])
    </form>
</div>
@endsection
@endcomponent
