@component('admin.layouts.content')
    @section('title', 'جزئیات جلسه')

@section('content')
{{-- اضافه کردن dir="rtl" به کانتینر اصلی --}}
<div class="container" dir="rtl">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>جزئیات جلسه شماره {{ $session->session_number }}</h1>
        <div>
            @can('create', App\Models\Allocation::class)
            <a href="{{ route('sessions.edit', $session) }}" class="btn btn-warning">ویرایش</a>
            @endcan

            {{-- Button for Final Approval and Report Generation --}}
            {{-- Only show if there are allocations and at least one is still in 'draft' status --}}
            @if ($session->allocations->count() > 0 && $session->allocations->contains('status', 'draft'))
                @can('create', App\Models\Allocation::class) {{-- Assuming you have a 'finalApproveReport' ability --}}
                    {{-- Using a form for POST request --}}
                    <form action="{{ route('sessions.finalApproveReport', $session) }}"
                        method="POST"
                        target="_blank"
                        style="display:inline;">
                        @csrf

                        <button type="submit"
                                class="btn btn-success"
                                onclick="return confirm('آیا از تایید نهایی این جلسه و تولید گزارش اطمینان دارید؟');">
                            تایید نهایی جلسه و دریافت صورتجلسه
                        </button>
                    </form>
                @endcan
                @elseif ($session->allocations->count() > 0 && $session->allocations->contains('status', 'approved'))
                <form action="{{ route('sessions.finalApproveReport', $session) }}"
                        method="POST"
                        target="_blank"
                        style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-success" >
                            مشاهده صورتجلسه
                        </button>
                    </form>

            @endif

            <a href="{{ route('sessions.index') }}" class="btn btn-secondary">بازگشت به لیست</a>
        </div>
    </div>

    <div class="card mb-4 text-right">
        <div class="card-body">
            <p><strong>دستور کار جلسه:</strong> {{ $session->title }}</p>
            <p><strong>تاریخ:</strong> {{ $session->date }}</p>
            <p><strong>ساعت:</strong> {{ $session->time }}</p>
            <p><strong>توضیحات:</strong></p>
            <p>{{ $session->description }}</p>
        </div>
    </div>

    <div class="row text-right"> {{-- اضافه کردن text-right به سطر برای متون --}}
        <div class="col-md-2 mb-4">
            <h3>اعضای جلسه</h3>
            @if ($session->users->count())
                <ul class="list-group">
                    @foreach ($session->users as $user)
                        <li class="list-group-item">
                            {{ $user->name }}
                        </li>
                    @endforeach
                </ul>
            @else
                <p>هیچ عضوی برای این جلسه ثبت نشده است.</p>
            @endif
        </div>

        <div class="col-md-10 mb-4">
            <h3>تخصیص‌ها</h3>
            @if ($session->allocations->count())
                {{-- اضافه کردن کلاس table-responsive برای جلوگیری از بهم ریختگی در موبایل --}}
                <div class="table-responsive">
                    <table class="table table-sm table-bordered text-right">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>نام واحد / متقاضی</th>
                                <th>کلاسه پرونده</th>
                                <th>شهرستان</th>
                                <th>محدوده مطالعاتی</th> 
                                <th>نوع مصرف</th>
                                <th>نوع منبع تامین آب</th>
                                <th>محل تخصیص</th>
                                <th>شماره و تاریخ ابلاغیه تخصیص</th>
                                <th>حجم آب تخصیص</th>
                                <th>حجم تخصیص داده شده تا کنون</th>
                                <th>میزان آب مصوب</th>
                                <th>میزان آب باقی مانده در بانک تخصیص</th>
                                @foreach ($session->users as $user)
                                    <th>{{ $user->name }}</th> {{-- نام کاربر به عنوان هدر ستون رأی --}}
                                @endforeach
                                <th>وضعیت</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($session->allocations as $allocation)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    {{-- <td>{{ optional($allocation->creator)->name }}</td> --}}
                                    <td>{{ $allocation->motaghasi ?? '' }}</td>
                                    <td>{{ $allocation->kelace ?? '' }}</td>
                                    <td>{{ $allocation->Shahrestan }}</td>
                                    <td>{{ $allocation->code ?? '' }}</td>
                                    <td>{{ $allocation->Takhsis_group ?? '' }}</td>
                                    <td>
                                        @php
                                            // اگر رابطه تعریف شده باشد، می‌توانید:
                                            $parentCategory = optional($allocation->fileCategory->parent)->name ?? 'نام پیدا نشد';
                                        @endphp
                                        {{ $parentCategory }}
                                    </td>
                                    <td>{{ $allocation->file_name ?? '' }}</td>
                                    <td>{{ $allocation->shomare ?? '' }}{{ "  " }}{{ $allocation->date_shimare_jalali ?? '' }}</td>
                                    <td>{{ $allocation->V_m ?? '' }}</td>
                                    <td>{{ $allocation->sum ?? '' }}</td>
                                    <td>{{ $allocation->t_mosavvab ?? '' }}</td>
                                    <td>{{ $allocation->baghi ?? '' }}</td>
                                    {{-- نمایش رأی هر کاربر برای این تخصیص --}}
                                    @foreach ($session->users as $user)
                                        <td>
                                            @php
                                                // پیدا کردن رأی این کاربر برای این تخصیص
                                                $vote = $allocation->votes->firstWhere('user_id', $user->id);
                                            @endphp

                                            {{-- @if ($vote) --}}
                                                @if ($vote && $vote->vote == 1)
                                                    <span class="badge bg-success">موافق</span> {{-- یا آیکون موافق --}}
                                                @else
                                                    <span class="badge bg-danger">مخالف</span> {{-- یا آیکون مخالف --}}
                                                {{-- @else --}}
                                                    {{-- <span class="badge bg-secondary">بدون رأی</span> اگر فیلد vote بتواند مقادیر دیگری داشته باشد --}}
                                                @endif
                                            {{-- @else --}}
                                                {{-- <span class="badge bg-light text-dark">ثبت نشده</span> کاربر رأی نداده --}}
                                            {{-- @endIf --}}
                                        </td>
                                    @endforeach
                                    <td class="allocation-status">
                                        @if($allocation->status === 'approved')
                                            <span class="badge bg-success">تأیید شده</span>
                                        @else
                                            <span class="badge bg-warning text-dark">در انتظار تأیید</span>
                                        @endif
                                    </td>


                                    <td style="min-width:160px;">

                                        {{-- حالت 1: رکورد draft --}}
                                        @if($allocation->status === 'draft')

                                            @can('create', $allocation)
                                                <a href="{{ route('allocations.edit', ['allocation' => $allocation->id, 'session' => $session]) }}"
                                                class="btn btn-success btn-sm">
                                                    ویرایش
                                                </a>

                                                <form action="{{ route('allocations.destroy', $allocation->id) }}"
                                                    method="POST"
                                                    class="delete-form d-inline"
                                                    data-id="{{ $allocation->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                            class="btn btn-danger btn-sm btn-delete"
                                                            data-url="{{ route('allocations.destroy', $allocation->id) }}">
                                                        حذف
                                                    </button>
                                                </form>
                                            @endcan

                                        {{-- حالت 2: رکورد approved --}}
                                        @elseif($allocation->status === 'approved')

                                            @role('admin')
                                                <a href="{{ route('allocations.edit', ['allocation' => $allocation->id, 'session' => $session]) }}"
                                                class="btn btn-success btn-sm">
                                                    ویرایش
                                                </a>

                                                <form action="{{ route('allocations.destroy', $allocation->id) }}"
                                                    method="POST"
                                                    class="delete-form d-inline"
                                                    data-id="{{ $allocation->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                            class="btn btn-danger btn-sm btn-delete"
                                                            data-url="{{ route('allocations.destroy', $allocation->id) }}">
                                                        حذف
                                                    </button>
                                                </form>
                                            @endrole

                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>تخصیصی برای این جلسه ثبت نشده است.</p>
            @endif
        </div>
        @can('create', App\Models\Allocation::class)
        <div class="d-flex flex-column flex-md-row gap-2">
            @php
                $session1=$session;
            @endphp
        <a href="{{ route('allocations.create', $session1 ) }}" class="btn btn-primary">ثبت تخصیص جدید</a>
        @endcan
        </div>
    </div>
</div>
@include('admin.allocations.scripts')
    {{-- در انتهای فایل resources/views/sessions/show.blade.php --}}
    @push('scripts')
    <script>
    $(document).ready(function() {
        // Use jQuery for AJAX request
        $('#finalApproveForm').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            var form = $(this);
            var url = form.attr('action');
            var method = form.attr('method');
            var formData = form.serialize(); // Get form data

            // Disable the button to prevent multiple clicks
            form.find('button[type="submit"]').prop('disabled', true).text('در حال پردازش...');

            $.ajax({
                url: url,
                type: method,
                data: formData,
                dataType: 'json', // Expect JSON response
                success: function(response) {
                    if (response.generate_report) {
                        // Report generation is requested, now I (AI) will handle it.
                        // For now, just inform the user.
                        alert(response.message);
                        // You might want to redirect or update the page here
                        // For this example, we'll assume the AI will pick up the request.
                        // A real application might need a different flow.
                        window.location.reload(); // Reload page to show updated status, or handle further steps.
                    } else if (response.error) {
                        alert('خطا: ' + response.error);
                        form.find('button[type="submit"]').prop('disabled', false).text('تایید نهایی جلسه و دریافت صورتجلسه');
                    } else {
                         // Handle other cases, maybe just a success message without report generation
                        alert(response.message);
                        window.location.reload();
                    }
                },
                error: function(xhr) {
                    // Handle server errors
                    var errorMessage = 'خطا در ارسال درخواست.';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    } else if (xhr.responseText) {
                        try {
                            var errorJson = JSON.parse(xhr.responseText);
                            if (errorJson.error) errorMessage = errorJson.error;
                        } catch(e) {
                            errorMessage = xhr.responseText; // Fallback to raw text
                        }
                    }
                    alert('خطا: ' + errorMessage);
                    form.find('button[type="submit"]').prop('disabled', false).text('تایید نهایی جلسه و دریافت صورتجلسه');
                }
            });
        });
    });
    </script>
    @endpush

    {{-- Make sure to include jQuery in your layout if you haven't already --}}
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

    {{-- Modify your form tag in the blade file --}}
    {{-- <form id="finalApproveForm" action="{{ route('sessions.finalApproveReport', $session) }}" method="POST" style="display:inline;"> --}}
    {{--     @csrf --}}
    {{--     <button type="submit" class="btn btn-success"> --}}
    {{--         تایید نهایی جلسه و دریافت صورتجلسه --}}
    {{--     </button> --}}
    {{-- </form> --}}

    {{-- Add @stack('scripts') in your layout file where you want JS to be injected --}}

@endsection
@endcomponent
