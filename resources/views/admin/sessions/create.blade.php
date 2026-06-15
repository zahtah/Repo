@component('admin.layouts.content')
    @section('title', 'ایجاد جلسه جدید')

@section('content')
<div class="container" dir="rtl">
    <h1 class="mb-4">ایجاد جلسه جدید</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('sessions.store') }}" method="POST">
        @include('admin.sessions._form', ['submitLabel' => 'ایجاد جلسه'])
    </form>
</div>
@endsection
@endcomponent