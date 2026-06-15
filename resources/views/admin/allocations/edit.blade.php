@component('admin.layouts.content')

@section('content')
<div class="container" dir="rtl">

    <h1 class="mb-4 text-end">ویرایش تخصیص</h1>

    {{-- پیام‌ها --}}
    @if (session('success'))
        <div class="alert alert-success text-end">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger text-end">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li class="text-end">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('allocations.update', ['id' => $allocation->id, 'session' => $session]) }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="row">
    <h6 class="mb-2">موافقان تخصیص</h6>

    @foreach($users as $user)
        @php
            $isChecked = in_array($user->id, $selectedUsers ?? []);
        @endphp

        <div class="col-md-3 col-6 mb-1">
            <div class="form-check d-flex align-items-center gap-1 p-0 m-0">

                <input class="form-check-input m-0"
                       style="margin-left:6px;"
                       type="checkbox"
                       name="votes[{{ $user->id }}]"
                       value="1"
                       id="u_{{ $user->id }}"
                       {{ $isChecked ? 'checked' : '' }}>

                <label class="form-check-label small m-0" for="u_{{ $user->id }}">
                    {{ $user->name }}
                </label>

            </div>
        </div>
    @endforeach
</div>

            {{-- نوع سند --}}
            <div class="col-md-2">
                <label class="form-label">نوع سند</label>
                <select name="file_category_id" id="file_category_id" class="form-select">
                    @foreach($fileOptions as $file)
                        <option value="{{ $file->id }}"
                            {{ old('file_category_id',$allocation->file_category_id)==$file->id?'selected':'' }}>
                            {{ $file->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- کد --}}
            <div class="col-md-2">
                <label class="form-label">کد محدوده</label>
                <select name="code" id="field_code" class="form-control">
                    <option value="">انتخاب کنید</option>
                    @foreach($codeOptions as $option)
                        <option value="{{ $option }}"
                            {{ old('code',$allocation->code)==$option?'selected':'' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- شهرستان --}}
            <div class="col-md-2">
                <label class="form-label">شهرستان</label>
                <select name="Shahrestan" class="form-control">
                    <option value="">انتخاب کنید</option>
                    @foreach($shahrOptions as $option)
                        <option value="{{ $option }}"
                            {{ old('Shahrestan',$allocation->Shahrestan)==$option?'selected':'' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- سال --}}
            <div class="col-md-2">
                <label class="form-label">سال</label>
                <input type="number"
                       name="sal"
                       class="form-control"
                       value="{{ old('sal',$allocation->sal) }}">
            </div>

            {{-- تاریخ ارجاع --}}
            <div class="col-md-2">
                <label class="form-label">تاریخ ارجاع</label>
                <input type="text"
                       id="erja_picker"
                       class="form-control"
                       value="{{ old('erja',$allocation->erja) }}">
                <input type="hidden"
                       name="erja"
                       id="erja"
                       value="{{ old('erja',$allocation->erja) }}">
            </div>

            {{-- منطقه --}}
            <div class="col-md-2">
                <label class="form-label">نوع منطقه</label>
                <select name="mantaghe" class="form-control">
                    <option value="">انتخاب کنید</option>
                    @foreach($mantagheOptions as $option)
                        <option value="{{ $option }}"
                            {{ old('mantaghe',$allocation->mantaghe)==$option?'selected':'' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- آبادی --}}
            <div class="col-md-2">
                <label class="form-label">نام آبادی</label>
                <input type="text"
                       name="Abadi"
                       class="form-control"
                       value="{{ old('Abadi',$allocation->Abadi) }}">
            </div>

            {{-- کلاسه --}}
            <div class="col-md-2">
                <label class="form-label">کلاسه پرونده</label>
                <input type="text"
                       name="kelace"
                       class="form-control"
                       value="{{ old('kelace',$allocation->kelace) }}">
            </div>

            {{-- متقاضی --}}
            <div class="col-md-2">
                <label class="form-label">نام متقاضی</label>
                <input type="text"
                       name="motaghasi"
                       class="form-control"
                       value="{{ old('motaghasi',$allocation->motaghasi) }}">
            </div>

            {{-- درخواست --}}
            <div class="col-md-2">
                <label class="form-label">نوع درخواست</label>
                <select name="darkhast" class="form-control">
                    <option value="">انتخاب کنید</option>
                    @foreach($darkhastOptions as $option)
                        <option value="{{ $option }}"
                            {{ old('darkhast',$allocation->darkhast)==$option?'selected':'' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- تخصیص --}}
            <div class="col-md-2">
                <label class="form-label">تخصیص</label>
                <select name="Takhsis_group"
                        id="field_Takhsis_group"
                        class="form-control">
                    <option value="">انتخاب کنید</option>
                    @foreach($takhsisOptions as $option)
                        <option value="{{ $option }}"
                            {{ old('Takhsis_group',$allocation->Takhsis_group)==$option?'selected':'' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- مصرف --}}
            <div class="col-md-2">
                <label class="form-label">مصرف</label>
                <input type="text"
                       name="masraf"
                       class="form-control"
                       value="{{ old('masraf',$allocation->masraf) }}">
            </div>

            {{-- تاریخ کمیته --}}
            <div class="col-md-2">
                <label class="form-label">تاریخ کمیته</label>
                <input type="text"
                       id="comete_picker"
                       class="form-control"
                       value="{{ old('comete',$allocation->comete) }}">
                <input type="hidden"
                       name="comete"
                       id="comete"
                       value="{{ old('comete',$allocation->comete) }}">
            </div>

            {{-- شماره مصوبه --}}
            <div class="col-md-2">
                <label class="form-label">شماره مصوبه</label>
                <input type="text"
                       name="shomare"
                       class="form-control"
                       value="{{ old('shomare',$allocation->shomare) }}">
            </div>

            {{-- تاریخ مصوبه --}}
            <div class="col-md-2">
                <label class="form-label">تاریخ مصوبه</label>
                <input type="text"
                       id="date_shimare_picker"
                       class="form-control"
                       value="{{ old('date_shimare',$allocation->date_shimare) }}">
                <input type="hidden"
                       name="date_shimare"
                       id="date_shimare"
                       value="{{ old('date_shimare',$allocation->date_shimare) }}">
            </div>

            {{-- شماره جلسه --}}
            <div class="col-md-2">
                <label class="form-label">شماره جلسه</label>
                <input type="text"
                       name="session_number"
                       class="form-control"
                       value="{{ old('session_number',$allocation->session) }}">
            </div>

            <input type="hidden" name="sessionid" value="{{ $session->id }}">

        </div>

        <hr>

        <div class="row">

            <div class="col-md-2">
                <label class="form-label">حجم مصوب</label>
                <input type="number"
                       step="0.001"
                       name="V_m"
                       id="field_V_m"
                       class="form-control"
                       value="{{ old('V_m',$allocation->V_m) }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">تخصیص سند</label>
                <input type="number"
                       step="0.001"
                       name="t_mosavvab"
                       id="field_t_mosavvab"
                       class="form-control"
                       value="{{ old('t_mosavvab',$allocation->t_mosavvab) }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">جمع</label>
                <input type="number"
                       step="0.001"
                       name="sum"
                       id="field_sum"
                       class="form-control"
                       readonly
                       value="{{ old('sum',$allocation->sum) }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">باقی مانده</label>
                <input type="number"
                       step="0.001"
                       name="baghi"
                       id="field_baghi"
                       class="form-control"
                       readonly
                       value="{{ old('baghi',$allocation->baghi) }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">مصوبات</label>
                <input type="text"
                       name="mosavabat"
                       class="form-control"
                       value="{{ old('mosavabat',$allocation->mosavabat) }}">
            </div>

        </div>

        <hr>

        <div class="mb-3">
            <label class="form-label">فایل صورتجلسه</label>
            <input type="file"
                   name="minutes"
                   class="form-control">
        </div>

        </div>

        <div class="mt-4 text-end">
            <button class="btn btn-success">ذخیره تغییرات</button>
            <a href="{{ route('sessions.show', $session) }}" class="btn btn-secondary">بازگشت</a>
        </div>

    </form>
    
</div>

{{-- ================= JS ================= --}}
<link rel="stylesheet" href="{{ asset('admin/assets/vendors/css/persian_datepicker.min.css')}}">

<script src="{{ asset('admin/assets/vendors/js/jquery_3.6.0.min.js')}}"></script>
<script src="{{ asset('admin/assets/vendors/js/persian_date.min.js')}}"></script>
<script src="{{ asset('admin/assets/vendors/js/persian_datepicker.min.js')}}"></script>

<script>
$(function () {

    function toEn(v){
        if(!v) return '';
        const p = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        p.forEach((n,i)=> v = v.replace(new RegExp(n,'g'), i));
        return v;
    }

    function initDate(id, hidden){
        $("#" + id).persianDatepicker({
            format: 'YYYY/MM/DD',
            autoClose: true,
            onSelect: function(){
                $('#' + hidden).val(toEn($('#'+id).val()));
            }
        });
    }

    initDate('erja_picker','erja');
    initDate('comete_picker','comete');
    initDate('date_shimare_picker','date_shimare');

    const vm = $('#field_V_m');
    const tm = $('#field_t_mosavvab');
    const sum = $('#field_sum');
    const baghi = $('#field_baghi');

    function calc(){
        let s = parseFloat(sum.val()||0);
        let t = parseFloat(tm.val()||0);
        baghi.val((t - s).toFixed(3));
    }

    vm.on('input', function(){
        let v = parseFloat(vm.val()||0);
        sum.val(v.toFixed(3));
        calc();
    });

    tm.on('input', calc);

    calc();
});
</script>

@endsection
@endcomponent