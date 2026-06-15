@csrf

<div class="mb-3">
    <label for="session_number" class="form-label">شماره جلسه</label>
    <input type="number" name="session_number" id="session_number"
           class="form-control @error('session_number') is-invalid @enderror"
           value="{{ old('session_number', $session->session_number ?? '') }}" required>
    @error('session_number')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="title" class="form-label">عنوان جلسه</label>
    <input type="text" name="title" id="title"
           class="form-control @error('title') is-invalid @enderror"
           value="{{ old('title', $session->title ?? '') }}">
    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">توضیحات</label>
    <textarea name="description" id="description" rows="3"
              class="form-control @error('description') is-invalid @enderror">{{ old('description', $session->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
         <label for="date" class="form-label">تاریخ</label>
        {{--<input type="date" name="date" id="date"
               class="form-control @error('date') is-invalid @enderror"
               value="{{ old('date', $session->date ?? '') }}" required>
        @error('date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror --}}

        <input type="text" id="date_picker" class="form-control text-end"
            value="{{ old('date') }}" placeholder="  انتخاب کنید. (امروز {{ \Morilog\Jalali\Jalalian::now()->format('Y/m/d') }})">
        <input type="hidden" name="date" id="date" value="{{ old('date') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label for="time" class="form-label">ساعت</label>
        <input type="time" name="time" id="time"
               class="form-control @error('time') is-invalid @enderror"
               value="{{ old('time', $session->time ?? '') }}" required step="2">
        @error('time')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label for="users" class="form-label">اعضای شرکت‌کننده</label>
    <select name="users[]" id="users" class="form-select" multiple>
        @php
            $selectedUsers = old('users', $selectedUsers ?? []);
        @endphp
        @foreach ($users as $user)
            <option value="{{ $user->id }}"
                {{ in_array($user->id, $selectedUsers) ? 'selected' : '' }}>
                {{ $user->name }}
            </option>
        @endforeach
    </select>
    <div class="form-text">
        برای انتخاب چند کاربر، کلید Ctrl (یا Command در مک) را نگه دارید.
    </div>
    @error('users')
        <div class="text-danger small">{{ $message }}</div>
    @enderror
</div>

<button type="submit" class="btn btn-success">
    {{ $submitLabel ?? 'ذخیره' }}
</button>
<a href="{{ route('sessions.index') }}" class="btn btn-secondary">بازگشت</a>


<link rel="stylesheet" href="{{ asset('admin/assets/vendors/css/persian_datepicker.min.css')}}">
        <script src="{{ asset('admin/assets/vendors/js/jquery_3.6.0.min.js')}}"></script>
        <script src="{{ asset('admin/assets/vendors/js/persian_date.min.js')}}"></script>
        <script src="{{ asset('admin/assets/vendors/js/persian_datepicker.min.js')}}"></script>
        <script src="{{ asset('admin/assets/vendors/js/sweetalert2@11.js')}}"></script>

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

                initDatepicker('date_picker', 'erja');

                $('form').on('submit', function() {
                    $('#date').val(convertPersianNumbersToEnglish($('#date_picker').val()));
                });
            });
        </script>
        @push('scripts')
<script>
    flatpickr("#time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        defaultDate: "{{ old('time', $session->time ?? '') }}",
    });
</script>
@endpush