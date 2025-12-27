@component('admin.layouts.content')
    @section('title', 'لیست تخصیص‌ها')

@section('content')
    <div class="container-fluid">
        <div class=" justify-content-between align-items-center mb-3">
            <div class=" flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4"
                dir="rtl">
                <div class="mb-3 w-100" dir="rtl">
                    <h2 class="text-end">تخصیص‌ها</h2>
                </div>

                <div class="d-flex flex-column flex-md-row gap-2">
                    <a href="{{ route('allocations.create') }}" class="btn btn-success">ثبت جدید</a>

                    <a href="{{ route('allocations.export') }}" class="btn btn-outline-primary"> خروجی Excel </a>

                    <!-- دکمه باز کردن modal -->
                    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#importModal">
                        وارد کردن از Excel
                    </button>
                </div>
            </div>

        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif


        <form method="GET" action="{{ route('allocations.index') }}" class="card p-3 mb-3">
            <div class="row gy-2 align-items-end">

                {{-- فیلدهای دلخواه دیگر مثل شهرستان، مصرف ... --}}
                <div class="col-md-3">
                    <label class="form-label">شهرستان</label>
                    <input type="text" name="Shahrestan" value="{{ request('Shahrestan') }}" class="form-control"
                        placeholder="مثال: سمنان">
                </div>

                <div class="col-md-3">
                    <label class="form-label">تخصیص</label>
                    <input type="text" name="masraf" value="{{ request('masraf') }}" class="form-control"
                        placeholder="مثال: شرب، صنعت">
                </div>

                {{-- FROM datepicker --}}
                <div class="col-md-2">
                    <label class="form-label">از تاریخ (ارجاع)</label>

                    @php
                        use App\Helpers\DateHelper;

                        $fromValue = DateHelper::toJalaliDisplay(request('from'));
                        $toValue = DateHelper::toJalaliDisplay(request('to'));
                    @endphp

                    <input type="text" id="from_picker" class="form-control" value="{{ $fromValue }}"
                        placeholder="۱۴۰۳/۰۱/۰۱">
                    <input type="hidden" name="from" id="from" value="{{ request('from') }}">
                </div>

                {{-- TO datepicker --}}
                <div class="col-md-2">
                    <label class="form-label">تا تاریخ (ارجاع)</label>
                    <input type="text" id="to_picker" class="form-control" value="{{ $toValue }}"
                        placeholder="۱۴۰۳/۱۲/۲۹">
                    <input type="hidden" name="to" id="to" value="{{ request('to') }}">
                </div>

                {{-- جستجوی عمومی --}}
                <div class="col-md-2">
                    <label class="form-label">جستجو عمومی</label>
                    <div class="input-group">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                            placeholder="ردیف، کلاسه، متقاضی...">
                        <button class="btn btn-primary" type="submit">اعمال</button>
                    </div>
                </div>
                {{-- فیلتر نام سند --}}
                <div class="col-md-2">
                    <label class="me-2">انتخاب سند:</label>
                    <select name="file_name" class="form-select me-2" onchange="this.form.submit()">
                        <option value="">نمایش همه</option>
                        @foreach ($fileNames as $name)
                            <option value="{{ $name }}"
                                {{ isset($fileFilter) && $fileFilter == $name ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>

                    @if (isset($fileFilter) && $fileFilter)
                        <a href="{{ route('allocations.index') }}" class="btn btn-outline-secondary">حذف فیلتر</a>
                    @endif
                </div>
            </div>
        </form>

        <!-- جدول -->
        <div class="card">
            <div class="table-scroll-top" style="overflow-x:auto;">
                <div class="table-scroll-inner" style="height:1px;"></div>
            </div>

            <div class="table-responsive" id="table-container">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="table-light text-center align-middle">
                        <tr>
                            <th>#</th>
                            <th>ردیف</th>
                            <th> نام سند </th>
                            <th>شهرستان</th>
                            <th>سال</th>
                            <th>تاریخ ارجاع</th>
                            <th>كد محدوده مطالعاتي</th>
                            <th>نوع منطقه</th>
                            <th>نام آبادی</th>
                            <th>کلاسه</th>
                            <th>نام متقاضی</th>
                            <th>نوع درخواست</th>
                            <th>تخصیص</th>
                            <th>مصرف</th>
                            <th>تاريخ تشكيل كميته</th>
                            <th>شماره مصوبه</th>
                            <th>تاریخ مصوبه</th>
                            {{-- <th>واحد دبی</th> --}}
                            {{-- <th>مصوب دبی </th> --}}
                            <th>حجم مصوب (هزار متر مکعب در سال)</th>
                            <th>تخصیص پنجم</th>
                            <th>جمع</th>
                            <th>باقی‌مانده</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle text-center allocation-table">
                        @forelse($allocations as $allocation)
                            <tr data-id="{{ $allocation->id }}">
                                <td class="cell-index">
                                    {{ $loop->iteration + ($allocations->currentPage() - 1) * $allocations->perPage() }}
                                </td>
                                <td class="cell-row">{{ $allocation->row }}</td>
                                <td class="cell-file">{{ $allocation->file_name }}</td>
                                <td class="cell-Shahrestan">{{ $allocation->Shahrestan }}</td>
                                <td class="cell-sal">{{ $allocation->sal }}</td>
                                <td class="cell-erja">{{ $allocation->erja_jalali }}</td>
                                <td class="cell-code">{{ $allocation->code }}</td>
                                <td class="cell-mantaghe">{{ $allocation->mantaghe }}</td>
                                <td class="cell-Abadi">{{ $allocation->Abadi }}</td>
                                <td class="cell-kelace">{{ $allocation->kelace }}</td>
                                <td class="cell-motaghasi">{{ $allocation->motaghasi }}</td>
                                <td class="cell-darkhast">{{ $allocation->darkhast }}</td>
                                <td class="cell-Takhsis_group">{{ $allocation->Takhsis_group }}</td>
                                <td class="cell-masraf">{{ $allocation->masraf }}</td>
                                <td class="cell-comete">{{ $allocation->comete_jalali }}</td>
                                <td class="cell-shomare">{{ $allocation->shomare }}</td>
                                <td class="cell-date_shimare">{{ $allocation->date_shimare_jalali }}</td>
                                {{-- <td class="cell-vahed">{{ $allocation->vahed }}</td> --}}
                                {{-- <td class="cell-q_m">{{ $allocation->q_m }}</td> --}}
                                <td class="cell-V_m">{{ $allocation->V_m }}</td>
                                <td class="cell-t_mosavab">{{ $allocation->t_mosavvab }}</td>
                                <td class="cell-sum">{{ $allocation->sum }}</td>
                                {{-- <td class="cell-sum">{{ number_format($allocation->cumulative_vm ?? 0, 2) }}</td> --}}
                                <td class="cell-baghi">{{ $allocation->baghi }}</td>
                                <td style="min-width:160px;">
                                    <!-- دکمه ویرایش بازکننده modal -->
                                    {{-- <button type="button" class="btn btn-sm btn-primary btn-edit"
                                        data-id="{{ $allocation->id }}">
                                        ویرایش
                                    </button> --}}
                                    <a href="{{ route('allocations.edit', $allocation->id) }}"
                                        class="btn btn-success">ویرایش </a>


                                    <form action="{{ route('allocations.destroy', $allocation->id) }}" method="POST"
                                        class="delete-form d-inline" data-id="{{ $allocation->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm btn-delete"
                                            data-url="{{ route('allocations.destroy', $allocation->id) }}">
                                            حذف
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13">رکوردی یافت نشد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-light border-top py-3">
                <div class="row align-items-center flex-row-reverse">
                    <!-- متن توضیح در سمت راست -->
                    <div class="col-md-6 text-muted small mb-2 mb-md-0 text-md-end text-center">
                        نمایش
                        <strong>{{ $allocations->firstItem() ?? 0 }}</strong>
                        تا
                        <strong>{{ $allocations->lastItem() ?? 0 }}</strong>
                        از
                        <strong>{{ $allocations->total() }}</strong>
                        رکورد
                    </div>

                    <!-- صفحه‌بندی در سمت چپ -->
                    <div class="col-md-6 d-flex justify-content-md-start justify-content-center">
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                {{ $allocations->withQueryString()->onEachSide(1)->links() }}
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @include('admin.allocations.modals')


@endsection
@endcomponent

@include('admin.allocations.scripts')
