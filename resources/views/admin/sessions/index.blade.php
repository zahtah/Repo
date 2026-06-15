@component('admin.layouts.content')
    @section('title', 'لیست [جلسات]')

@section('content')
<div class="container" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>لیست جلسات</h1>
        @can('create', App\Models\Allocation::class)
        <a href="{{ route('sessions.create') }}" class="btn btn-primary">ایجاد جلسه جدید</a>
        @endcan
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($sessions->count())
        <table class="table table-bordered  text-right">
            <thead>
                <tr>
                    <th>#</th>
                    <th>شماره جلسه</th>
                    <th>عنوان</th>
                    <th>تاریخ</th>
                    <th>ساعت</th>
                    <th>تعداد اعضا</th>
                    <th>تعداد تخصیص‌ها</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sessions as $session)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $session->session_number }}</td>
                        <td>{{ $session->title }}</td>
                        <td>{{ $session->date }}</td>
                        <td>{{ $session->time }}</td>
                        <td>{{ $session->users_count }}</td>
                        <td>{{ $session->allocations_count }}</td>
                        <td>
                            <a href="{{ route('sessions.show', $session) }}" class="btn btn-sm btn-info">مشاهده</a>
                            @can('create', App\Models\Allocation::class)
                            <a href="{{ route('sessions.edit', $session) }}" class="btn btn-sm btn-warning">ویرایش</a>
                            <form action="{{ route('sessions.destroy', $session) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('از حذف این جلسه مطمئن هستید؟');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit">حذف</button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- {{ $sessions->links() }} --}}
    @else
        <p>هیچ جلسه‌ای ثبت نشده است.</p>
    @endif
</div>
@endsection
@endcomponent
