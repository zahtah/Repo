@component('admin.layouts.content')
    @section('title', 'لیست تخصیص‌ها')

@section('content')
    <style>
        .modern-table tbody tr {
            transition: all 0.2s ease;
        }

        .modern-table tbody tr:hover {
            background: #f8f9fc;
            transform: scale(1.002);
        }

        .row-approved {
            border-right: 3px solid #198754;
        }

        .row-draft {
            border-right: 3px solid #ffc107;
        }

        .modern-table th {
            font-weight: 600;
            font-size: 13px;
            color: #6c757d;
        }

        .modern-table td {
            font-size: 13px;
        }
    </style>
    <div class="container-fluid mb-3">

        <div class="card shadow-sm border-0">

            <div class="card-body py-3">

                <div class="d-flex justify-content-between align-items-center flex-wrap">

                    <div>

                        <h4 class="mb-1 fw-bold text-primary">
                            داشبورد مدیریتی تخصیص منابع آب
                        </h4>

                        <small class="text-muted">
                            گزارش‌گیری، تحلیل و مدیریت تخصیص‌ها
                        </small>

                    </div>

                    <div class="text-end">

                        <div class="badge bg-light text-dark border">
                            آخرین بروزرسانی: {{ now()->format('Y-m-d H:i') }}
                        </div>

                        <div class="d-flex flex-column flex-md-row gap-2">
                            {{-- @can('create', App\Models\Allocation::class)
                        <a href="{{ route('allocations.create') }}" class="btn btn-success">ثبت جدید</a>
                    @endcan --}}

                            {{-- <a href="{{ route('allocations.export') }}" class="btn btn-outline-primary"> خروجی Excel </a> --}}
                            @role('admin')
                                <!-- دکمه باز کردن modal -->
                                <button class="btn btn-outline-secondary badge bg-primary mt-2" data-bs-toggle="modal"
                                    data-bs-target="#importModal">
                                    وارد کردن دیتا از Excel
                                </button>
                            @endrole
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
    <div class="container-fluid">

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif


        <form method="GET" action="{{ route('allocations.index') }}" class="card shadow-sm border-0 p-3 mb-3">

            <div class="row g-3">

                {{-- نام سند --}}
                <div class="col-md-3">
                    <label class="form-label fw-bold">نام سند</label>

                    <select name="file_name[]" class="form-select select2" multiple>
                        @foreach ($fileNames as $name)
                            <option value="{{ $name }}"
                                {{ in_array($name, (array) request('file_name')) ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- گروه تخصیص --}}
                <div class="col-md-3">
                    <label class="form-label fw-bold">گروه تخصیص</label>

                    <select name="takhsis_group[]" class="form-select select2" multiple>
                        @foreach ($takhsisGroups as $m)
                            <option value="{{ $m }}"
                                {{ in_array($m, (array) request('takhsis_group')) ? 'selected' : '' }}>
                                {{ $m }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- کد محدوده --}}
                <div class="col-md-2">
                    <label class="form-label fw-bold">کد محدوده</label>

                    <select name="code[]" class="form-select select2" multiple>
                        @foreach ($codes as $c)
                            <option value="{{ $c }}"
                                {{ in_array($c, (array) request('code')) ? 'selected' : '' }}>
                                {{ $c }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- کلاسه --}}
                <div class="col-md-2">
                    <label class="form-label fw-bold">کلاسه</label>
                    <input type="text" name="kelace" value="{{ request('kelace') }}" class="form-control">
                </div>

                {{-- دکمه‌ها --}}
                <div class="col-md-2 d-flex align-items-end gap-2">

                    <button class="btn btn-primary w-100">
                        اعمال فیلتر
                    </button>

                    <a href="{{ route('allocations.index') }}" class="btn btn-outline-secondary w-100">
                        ریست
                    </a>

                </div>

            </div>

        </form>


        <!-- جدول -->
        <div class="card shadow-sm border-0">

            {{-- Header --}}
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-secondary">
                    لیست تخصیص‌ها
                </h6>

                <span class="text-muted small">
                    {{ $allocations->total() }} رکورد
                </span>
            </div>

            {{-- Table --}}
            <div class="table-responsive" style="max-height:75vh; overflow:auto;">

                <table class="table table-hover align-middle text-center mb-0 modern-table">

                    <thead class="table-light position-sticky top-0" style="z-index:2;">
                        <tr>
                            <th>#</th>
                            <th>ردیف</th>
                            <th>سند</th>
                            <th>شهرستان</th>
                            <th>سال</th>
                            <th>تاریخ ارجاع</th>
                            <th>کد محدوده</th>
                            <th>نام آبادی</th>
                            <th>کلاسه</th>
                            <th>متقاضی</th>
                            <th>درخواست</th>
                            <th>گروه</th>
                            <th>مصرف</th>
                            <th>تاريخ تشكيل كميته</th>
                            <th>شماره مصوبه</th>
                            <th>تاریخ مصوبه</th>
                            <th>حجم</th>
                            <th>تخصیص ابلاغی</th>
                            <th>جمع</th>
                            <th>باقی‌مانده</th>
                            <th>جلسه</th>
                            <th>مصوبات </th>
                            <th>فایل</th>
                            <th>وضعیت</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($allocations as $allocation)
                            <tr class="table-row {{ $allocation->status === 'approved' ? 'row-approved' : 'row-draft' }}">

                                {{-- ردیف --}}
                                <td class="text-muted small">
                                    {{ $loop->iteration + ($allocations->currentPage() - 1) * $allocations->perPage() }}
                                </td>
                                <td class="cell-row">{{ $allocation->row }}</td>

                                {{-- سند --}}
                                <td class="fw-semibold">
                                    {{ $allocation->file_name }}
                                </td>

                                {{-- شهرستان --}}
                                <td>
                                    {{ $allocation->Shahrestan }}
                                </td>

                                <td class="cell-sal">{{ $allocation->sal }}</td>
                                <td class="cell-erja">{{ $allocation->erja_jalali }}</td>


                                {{-- کد --}}
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $allocation->code }}
                                    </span>
                                </td>

                                <td class="cell-Abadi">{{ $allocation->Abadi }}</td>

                                {{-- کلاسه --}}
                                <td>
                                    {{ $allocation->kelace }}
                                </td>

                                {{-- متقاضی --}}
                                <td class="fw-medium">
                                    {{ $allocation->motaghasi }}
                                </td>

                                {{-- درخواست --}}
                                <td>
                                    {{ $allocation->darkhast }}
                                </td>

                                {{-- گروه --}}
                                <td>
                                    {{ $allocation->Takhsis_group }}
                                </td>

                                <td class="cell-masraf">{{ $allocation->masraf }}</td>
                                <td class="cell-comete">{{ $allocation->comete_jalali }}</td>
                                <td class="cell-shomare">{{ $allocation->shomare }}</td>
                                <td class="cell-date_shimare">{{ $allocation->date_shimare_jalali }}</td>

                                {{-- حجم --}}
                                <td>
                                    {{ number_format($allocation->V_m, 2) }}
                                </td>

                                {{-- مصوب --}}
                                <td>
                                    <span class="badge bg-primary-subtle text-primary">
                                        {{ number_format($allocation->t_mosavvab, 2) }}
                                    </span>
                                </td>

                                <td class="cell-sum">{{ $allocation->sum }}</td>

                                {{-- باقی مانده --}}
                                <td>
                                    @php
                                        $baghi = (float) $allocation->baghi;
                                    @endphp

                                    <span class="badge {{ $baghi >= 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ number_format($baghi, 2) }}
                                    </span>
                                </td>

                                {{-- جلسه --}}
                                <td class="text-muted small">
                                    {{ $allocation->session }}
                                </td>

                                <td class="cell-mosavabat">{{ $allocation->mosavabat }}</td>

                                {{-- فایل --}}
                                <td>
                                    @if ($allocation->minutes)
                                        <a href="{{ route('allocations.download', $allocation->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            📎
                                        </a>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>

                                {{-- وضعیت --}}
                                <td>
                                    @if ($allocation->status === 'approved')
                                        <span class="badge rounded-pill bg-success">
                                            تایید شده
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-warning text-dark">
                                            در انتظار
                                        </span>
                                    @endif
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="14" class="py-5 text-muted">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-inbox fs-2 mb-2"></i>
                                        داده‌ای برای نمایش وجود ندارد
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Footer --}}
            <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center">

                <small class="text-muted">
                    نمایش {{ $allocations->firstItem() }} تا {{ $allocations->lastItem() }}
                    از {{ $allocations->total() }}
                </small>

                <div>
                    {{ $allocations->withQueryString()->links() }}
                </div>

            </div>

        </div>
    </div>

    @include('admin.allocations.modals')


@endsection
@endcomponent

@include('admin.allocations.scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "انتخاب کنید",
            allowClear: true,
            width: '100%'
        });
    });
</script>
