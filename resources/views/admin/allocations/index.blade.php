@component('admin.layouts.content')
    @section('title', 'لیست تخصیص‌ها')

@section('content')
    <div class="container-fluid">
        <div class=" justify-content-between align-items-center mb-3">
            <div class=" flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4"
                dir="rtl">
                <div class="mb-3 w-100" dir="rtl">
                    <h2 class="text-end">لیست تخصیص‌ها</h2>
                </div>

                <div class="d-flex flex-column flex-md-row gap-2">
                    @can('create', App\Models\Allocation::class)
                        <a href="{{ route('allocations.create') }}" class="btn btn-success">ثبت جدید</a>
                    @endcan

                    {{-- <a href="{{ route('allocations.export') }}" class="btn btn-outline-primary"> خروجی Excel </a> --}}
                    @role('admin')
                        <!-- دکمه باز کردن modal -->
                        <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#importModal">
                            وارد کردن از Excel
                        </button>
                    @endrole
                </div>
            </div>

        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif


<form method="GET" action="{{ route('allocations.index') }}" class="card p-3 mb-3">
    <div class="row gy-2">
        {{-- نام سند --}}
        <div class="col-md-2">
            <label class="form-label fw-bold">نام سند</label>
            <div class="border rounded p-2 filter-box">
                <div class="form-check text-end">
                    <input class="form-check-input select-all" type="checkbox"
                        data-target="file_name">
                    <label class="form-check-label fw-bold">همه</label>
                </div>
                <hr class="my-1">

                @foreach ($fileNames as $name)
                    <div class="form-check text-end">
                        <input class="form-check-input file_name-item"
                            type="checkbox"
                            name="file_name[]"
                            value="{{ $name }}"
                            {{ in_array($name, (array)request('file_name')) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ $name }}</label>
                    </div>
                @endforeach
            </div>
        </div>


        {{-- کد محدوده مطالعاتی --}}
        <div class="col-md-2">
            <label class="form-label fw-bold">کد محدوده</label>
            <div class="border rounded p-2 filter-box">
                <div class="form-check text-end">
                    <input class="form-check-input select-all" type="checkbox"
                        data-target="code">
                    <label class="form-check-label fw-bold">همه</label>
                </div>
                <hr class="my-1">

                @foreach ($codes as $c)
                    <div class="form-check text-end">
                        <input class="form-check-input code-item"
                            type="checkbox"
                            name="code[]"
                            value="{{ $c }}"
                            {{ in_array($c, (array)request('code')) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ $c }}</label>
                    </div>
                @endforeach
            </div>
        </div>


        {{-- گروه تخصیص --}}
        <div class="col-md-2">
            <label class="form-label fw-bold">گروه تخصیص</label>
            <div class="border rounded p-2 filter-box">
                <div class="form-check text-end">
                    <input class="form-check-input select-all" type="checkbox"
                        data-target="takhsis_group">
                    <label class="form-check-label fw-bold">همه</label>
                </div>
                <hr class="my-1">

                @foreach ($takhsisGroups as $m)
                    <div class="form-check text-end">
                        <input class="form-check-input takhsis_group-item"
                            type="checkbox"
                            name="takhsis_group[]"
                            value="{{ $m }}"
                            {{ in_array($m, (array)request('takhsis_group')) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ $m }}</label>
                    </div>
                @endforeach
            </div>
        </div>


        <!-- <div class="col-md-2">
            <label class="form-label">گروه تخصیص</label>
            <select name="takhsis_group[]" class="form-select" multiple>
                @foreach ($takhsisGroups as $t)
                    <option value="{{ $t }}"
                        {{ collect(request('takhsis_group'))->contains($t) ? 'selected' : '' }}>
                        {{ $t }}
                    </option>
                @endforeach
            </select>
        </div> -->

        {{-- شهرستان --}}
        <div class="col-md-2">
            <label class="form-label fw-bold">شهرستان </label>
            <div class="border rounded p-2 filter-box">
                <div class="form-check text-end">
                    <input class="form-check-input select-all" type="checkbox"
                        data-target="Shahrestan">
                    <label class="form-check-label fw-bold">همه</label>
                </div>
                <hr class="my-1">

                @foreach ($shahrestans as $m)
                    <div class="form-check text-end">
                        <input class="form-check-input Shahrestan-item"
                            type="checkbox"
                            name="Shahrestan[]"
                            value="{{ $m }}"
                            {{ in_array($m, (array)request('Shahrestan')) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ $m }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- نوع مصرف --}}
        <div class="col-md-2">
            <label class="form-label fw-bold">نوع مصرف</label>
            <div class="border rounded p-2 filter-box">
                <div class="form-check text-end">
                    <input class="form-check-input select-all" type="checkbox"
                        data-target="masraf">
                    <label class="form-check-label fw-bold">همه</label>
                </div>
                <hr class="my-1">

                @foreach ($masrafs as $m)
                    <div class="form-check text-end">
                        <input class="form-check-input masraf-item"
                            type="checkbox"
                            name="masraf[]"
                            value="{{ $m }}"
                            {{ in_array($m, (array)request('masraf')) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ $m }}</label>
                    </div>
                @endforeach
            </div>
        </div>


        {{-- کلاسه (متنی) --}}
        <div class="col-md-1">
            <label class="form-label">کلاسه</label>
            <input type="text" name="kelace" value="{{ request('kelace') }}"
                   class="form-control">
        </div>
        

        {{-- دکمه‌ها --}}
        <div class="col-md-1 d-flex align-items-end gap-1">
            <button class="btn btn-primary w-100">اعمال</button>
            <a href="{{ route('allocations.index') }}" class="btn btn-outline-danger w-100">
                ریست
            </a>
        </div>
        <!-- <div class="col-md-1 d-flex align-items-end">
            
        </div> -->

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
                            <th>شماره جلسه</th>
                            <th>فایل صورت جلسه</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                            <th> تایید نهایی</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle text-center allocation-table">
                        @forelse($allocations as $allocation)
                            <tr data-id="{{ $allocation->id }}" style="background-color:{{ $allocation->status === 'approved' ? 'cornflowerblue' : '#6a008a' }}">
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
                                <td class="cell-session">{{ $allocation->session }}</td>
                                @php
                                    $path=$allocation->minutes;
                                @endphp
                                @if ($path && Storage::disk('public')->exists($path))
                                <td class="cell-minutes"><a href="{{route('allocations.download',$allocation->id)  }}" class="btn btn-sm btn-primary">دانلود</a></td>
                                @else
                                <td> فایل موجود نیست </td>
                                @endif
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
                                            <a href="{{ route('allocations.edit', $allocation->id) }}"
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
                                            <a href="{{ route('allocations.edit', $allocation->id) }}"
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

                                <td> 
                                    @can('approve', $allocation)
                                         <form action="{{ route('allocations.approve', $allocation->id) }}"
                                            method="POST"
                                            class="approve-form d-inline"
                                            data-id="{{ $allocation->id }}">

                                            @csrf
                                            @method('PUT')

                                            <button type="button"
                                                    class="btn btn-success btn-sm btn-approve"
                                                    data-url="{{ route('allocations.approve', $allocation->id) }}">
                                                تأیید
                                            </button>
                                        </form>
                                    @endcan

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
