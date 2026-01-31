@component('admin.layouts.content')

    @section('content')
        <div class="container" dir="rtl">
            <h1 class="mb-4 text-end">ایجاد رکورد جدید تخصیص</h1>

            {{-- نمایش پیام موفقیت --}}
            @if (session('success'))
                <div class="alert alert-success text-end">{{ session('success') }}</div>
            @endif

            {{-- نمایش ارورهای اعتبارسنجی --}}
            @if ($errors->any())
                <div class="alert alert-danger text-end">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li class="text-end">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- فرم ایجاد رکورد --}}
            <form action="{{ route('allocations.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3 d-flex">

                    {{-- ردیف
                    <div class="col-md-2">
                        <label class="form-label">ردیف</label>
                        <input type="text" name="row" class="form-control text-end" value="{{ old('row', $nextRow) }}"
                            required>
                    </div> --}}
                    {{-- نوع سند --}}
                    <div class="col-md-2">
                        <label for="file_name" class="form-label">نوع سند</label>
                        <select name="file_category_id" id="file_category_id" class="form-select">
                            <option value="">— انتخاب کنید —</option>
                            @foreach ($fileOptions as $name)
                                <option value="{{ $name->id }}">{{ $name->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- کد محدوده --}}
                    <div class="col-md-2">
                        <label class="form-label">کد محدوده</label>
                        <select name="code" id="field_code" class="form-control text-end">
                            <option value="">انتخاب کنید</option>
                            @foreach ($codeOptions as $option)
                                <option value="{{ $option }}" {{ old('code') == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- شهرستان --}}
                    <div class="col-md-2">
                        <label class="form-label">شهرستان</label>
                        <select name="Shahrestan" class="form-control text-end">
                            <option value="">انتخاب کنید</option>
                            @foreach ($shahrOptions as $option)
                                <option value="{{ $option }}" {{ old('Shahrestan') == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- سال --}}
                    <div class="col-md-2">
                        <label class="form-label">سال</label>
                        <input type="number" name="sal" class="form-control text-end" value="{{ old('sal') }}">
                    </div>

                    {{-- تاریخ ارجاع --}}
                    <div class="col-md-2">
                        <label class="form-label">تاریخ ارجاع</label>
                        <input type="text" id="erja_picker" class="form-control text-end" value="{{ old('erja') }}"
                            placeholder="۱۴۰۳/۰۱/۰۱">
                        <input type="hidden" name="erja" id="erja" value="{{ old('erja') }}">
                    </div>




                    {{-- نوع منطقه --}}
                    <div class="col-md-2">
                        <label class="form-label">نوع منطقه</label>
                        <select name="mantaghe" class="form-control text-end">
                            <option value="">انتخاب کنید</option>
                            @foreach ($mantagheOptions as $option)
                                <option value="{{ $option }}" {{ old('mantaghe') == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- نام آبادی --}}
                    <div class="col-md-2">
                        <label class="form-label">نام آبادی</label>
                        <input type="text" name="Abadi" class="form-control text-end" value="{{ old('Abadi') }}">
                    </div>

                    {{-- کلاسه پرونده --}}
                    <div class="col-md-2">
                        <label class="form-label">کلاسه پرونده</label>
                        <input type="text" name="kelace" class="form-control text-end" value="{{ old('kelace') }}"
                            required>
                    </div>

                    {{-- نام متقاضی --}}
                    <div class="col-md-2">
                        <label class="form-label">نام متقاضی</label>
                        <input type="text" name="motaghasi" class="form-control text-end" value="{{ old('motaghasi') }}">
                    </div>

                    {{-- نوع درخواست --}}
                    <div class="col-md-2">
                        <label class="form-label">نوع درخواست</label>
                        <select name="darkhast" class="form-control text-end">
                            <option value="">انتخاب کنید</option>
                            @foreach ($darkhastOptions as $option)
                                <option value="{{ $option }}" {{ old('darkhast') == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- تخصیص -->
                    <div class="col-md-2">
                        <label class="form-label">تخصیص</label>
                        <select name="Takhsis_group" class="form-control text-end" id="field_Takhsis_group">
                            <option value="">انتخاب کنید</option>
                            @foreach ($takhsisOptions as $option)
                                <option value="{{ $option }}" {{ old('Takhsis_group') == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- مصرف --}}
                    <div class="col-md-2">
                        <label class="form-label">مصرف</label>
                        <input type="text" name="masraf" class="form-control text-end" value="{{ old('masraf') }}">
                    </div>

                    {{-- تاریخ تشکیل کمیته --}}
                    <div class="col-md-2">
                        <label class="form-label">تاریخ تشکیل کمیته</label>
                        <input type="text" id="comete_picker" class="form-control text-end" value="{{ old('comete') }}"
                            placeholder="۱۴۰۳/۰۱/۰۱">
                        <input type="hidden" name="comete" id="comete" value="{{ old('comete') }}">
                    </div>

                    {{-- شماره مصوبه --}}
                    <div class="col-md-2">
                        <label class="form-label">شماره مصوبه</label>
                        <input type="text" name="shomare" class="form-control text-end" value="{{ old('shomare') }}">
                    </div>

                    {{-- تاریخ مصوبه --}}
                    <div class="col-md-2">
                        <label class="form-label">تاریخ مصوبه</label>
                        <input type="text" id="date_shimare_picker" class="form-control text-end"
                            value="{{ old('date_shimare') }}" placeholder="۱۴۰۳/۰۱/۰۱">
                        <input type="hidden" name="date_shimare" id="date_shimare" value="{{ old('date_shimare') }}">
                    </div>
                    {{-- شماره جلسه  --}}
                    <div class="col-md-2">
                        <label class="form-label">شماره جلسه</label>
                        <input type="text" name="session" class="form-control text-end" value="{{ old('session') }}">
                    </div>

                    {{-- واحد دبی
                    <div class="col-md-2">
                        <label class="form-label">واحد دبی</label>
                        <select name="vahed" class="form-control text-end">
                            <option value="">انتخاب کنید</option>
                            @foreach ($vahedOptions as $option)
                                <option value="{{ $option }}" {{ old('vahed') == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                     مصوب دبی 
                    <div class="col-md-2">
                        <label class="form-label">مصوب دبی</label>
                        <input type="number" name="q_m" class="form-control text-end" value="{{ old('q_m') }}">
                    </div> --}}

                    {{-- حجم دبی --}}
                    <div class="d-flex justify-content-around">
                        <div class="col-md-2">
                            <label class="form-label">حجم مصوب</label>
                            <input type="number" step="0.001" name="V_m" class="form-control text-end"
                                id="field_V_m" value="{{ old('V_m') }}">
                        </div>

                        {{-- تخصیص پنجم --}}
                        <div class="col-md-2">
                            <label class="form-label">تخصیص سند</label>
                            <input type="number" step="0.001" name="t_mosavvab" id="field_t_mosavvab"
                                class="form-control text-end" value="{{ old('t_mosavvab') }}">
                        </div>

                        {{-- sum (readonly) --}}
                        <div class="col-md-2">
                            <label class="form-label">هزینه(جمع حجم مصوب)</label>
                            <input type="number" step="0.001" name="sum" id="field_sum"
                                class="form-control text-end" readonly value="{{ old('sum') }}">
                        </div>

                        {{-- باقی مانده --}}
                        <div class="col-md-2">
                            <label class="form-label">حجم باقی مانده</label>
                            <input type="number" step="0.001" name="baghi" id="field_baghi"
                                class="form-control text-end" value="{{ old('baghi') }}">
                        </div>

                        {{-- مصوبات --}}
                        <div class="col-md-2">
                            <label class="form-label">مصوبات</label>
                            <input type="text" name="mosavabat" class="form-control text-end"
                                value="{{ old('mosavabat') }}">
                        </div>

                    </div>
                    {{-- فایل صورت جلسه --}}
                    <div class="col-md-2 float-left">
                        <label class="form-label">فایل صورت‌جلسه</label>
                        <input type="file" name="minutes" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    </div>

                </div>

                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-success">ذخیره رکورد</button>
                    <a href="{{ route('allocations.index') }}" class="btn btn-secondary">بازگشت</a>
                </div>
            </form>
        </div>

        {{-- === CDN های مورد نیاز برای datepicker شمسی === --}}
        <link rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>

        <script>
            $(function() {
                function convertPersianNumbersToEnglish(str) {
                    if (!str) return str;
                    const persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
                    let result = str;
                    persianNumbers.forEach((num, i) => result = result.replace(new RegExp(num, 'g'), i));
                    return result;
                }

                function initDatepicker(pickerId, hiddenId) {
                    $("#" + pickerId).persianDatepicker({
                        format: 'YYYY/MM/DD',
                        observer: true,
                        autoClose: true,
                        initialValue: false,
                        toolbox: {
                            calendarSwitch: {
                                enabled: false
                            }
                        },
                        onSelect: function() {
                            $('#' + hiddenId).val(convertPersianNumbersToEnglish($('#' + pickerId).val()));
                        }
                    });
                    // مقدار اولیه hidden
                    let val = convertPersianNumbersToEnglish($('#' + pickerId).val());
                    if (val) $('#' + hiddenId).val(val);
                }

                initDatepicker('erja_picker', 'erja');
                initDatepicker('comete_picker', 'comete');
                initDatepicker('date_shimare_picker', 'date_shimare');

                $('form').on('submit', function() {
                    $('#erja').val(convertPersianNumbersToEnglish($('#erja_picker').val()));
                    $('#comete').val(convertPersianNumbersToEnglish($('#comete_picker').val()));
                    $('#date_shimare').val(convertPersianNumbersToEnglish($('#date_shimare_picker').val()));
                });
            });
        </script>


        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                        '{{ csrf_token() }}';

                    const elFile = document.getElementById('file_category_id');
                    const elCode = document.getElementById('field_code');
                    const elTakhsis = document.getElementById('field_Takhsis_group');
                    const elTMosavvab = document.getElementById('field_t_mosavvab');
                    const elSum = document.getElementById('field_sum');
                    const elBaghi = document.getElementById('field_baghi');
                    const elVm = document.getElementById('field_V_m');

                    // تبدیل ارقام فارسی به انگلیسی
                    function persianToEnglish(str) {
                        if (!str) return '';
                        let s = String(str).trim();
                        const pers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
                        pers.forEach((p, i) => s = s.replace(new RegExp(p, 'g'), i));
                        s = s.replace(/٬/g, '').replace(/,/g, '').replace(/٫/g, '.');
                        return s;
                    }

                    // خواندن عدد float از input
                    function getFloatValue(el) {
                        if (!el) return 0;
                        const val = el.value;
                        if (!val) return 0;
                        const n = parseFloat(persianToEnglish(val));
                        return Number.isFinite(n) ? n : 0;
                    }

                    // محاسبه baghi = t_mosavvab - sum
                    function recalcBaghi() {
                        const t = getFloatValue(elTMosavvab);
                        const s = getFloatValue(elSum);
                        const diff = t - s;
                        elBaghi.value = (Math.round(diff * 1000) / 1000).toFixed(3);
                        // هایلایت کوتاه
                        elBaghi.style.transition = 'background-color 0.25s ease';
                        elBaghi.style.backgroundColor = '#fff3cd';
                        setTimeout(() => elBaghi.style.backgroundColor = '', 400);
                    }

                    // مقدار اولیه sum و t_mosavvab = 0
                    elSum.value = '0';
                    elTMosavvab.value = '0';
                    elBaghi.value = '0';

                    // محاسبه t_mosavvab از سرور
                    async function fetchTMosavvab() {
                        const file = elFile.value;
                        const code = elCode.value.trim();
                        const takhsis = elTakhsis.value;

                        if (!file || !code || !takhsis) {
                            elTMosavvab.value = '0';
                            recalcBaghi();
                            return;
                        }

                        try {
                            const res = await fetch("{{ route('allocations.computeTMosavvab') }}", {
                                method: 'POST',
                                credentials: 'same-origin',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrf
                                },
                                body: JSON.stringify({
                                    file_category_id: file,
                                    code: code,
                                    Takhsis_group: takhsis
                                })

                            });

                            if (res.ok) {
                                const data = await res.json();
                                elTMosavvab.value = data.t_mosavvab ?? 0;
                            } else {
                                elTMosavvab.value = '0';
                            }
                        } catch (e) {
                            console.error(e);
                            elTMosavvab.value = '0';
                        }

                        recalcBaghi();
                    }

                    // بروزرسانی sum از سرور
                    async function computeAndSetSum() {
                        const code = elCode.value.trim();
                        const takhsis = elTakhsis.value;
                        const vm = getFloatValue(elVm);
                        const fileName = elFile.value;

                        try {
                            const res = await fetch("{{ route('allocations.computeSum') }}", {
                                method: 'POST',
                                credentials: 'same-origin',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrf,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    code: code || null,
                                    Takhsis_group: takhsis || null,
                                    V_m: vm,
                                    file_category_id: elFile.value || null
                                })

                            });

                            if (res.ok) {
                                const data = await res.json();
                                elSum.value = Number(data.sum ?? 0).toFixed(3);
                            } else {
                                elSum.value = vm.toFixed(3);
                            }
                        } catch (err) {
                            console.error(err);
                            elSum.value = vm.toFixed(3);
                        }

                        recalcBaghi();
                    }

                    // بروزرسانی nextRow با تغییر file_name
                    async function fetchNextRow() {
                        const file = elFile.value;
                        if (!file) return;
                        try {
                            const res = await fetch(
                                "{{ route('allocations.nextRow') }}?file_category_id=" + encodeURIComponent(elFile
                                    .value), {
                                    credentials: 'same-origin'
                                }
                            );

                            if (res.ok) {
                                const data = await res.json();
                                document.querySelector('input[name="row"]').value = data.nextRow;
                            }
                        } catch (e) {
                            console.error(e);
                        }
                    }

                    // اضافه کردن event listener به هر سه فیلد
                    [elFile, elCode, elTakhsis].forEach(el => {
                        if (!el) return;
                        el.addEventListener('change', async () => {
                            await fetchTMosavvab();
                            await computeAndSetSum();
                            await fetchNextRow();
                        });
                    });


                    // بروزرسانی sum و baghi وقتی vm تغییر کند
                    if (elVm) {
                        elVm.addEventListener('input', computeAndSetSum);
                    }

                    // محاسبه اولیه
                    fetchTMosavvab();
                    computeAndSetSum();
                    fetchNextRow();
                });
            </script>
        @endpush
    @endsection

@endcomponent
