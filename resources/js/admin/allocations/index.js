document.addEventListener('DOMContentLoaded', function () {
    // تمام کدهای مربوط به edit, fetch, sweetalert, datepicker اینجا قرار می‌گیرد

    const editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const rowId = this.dataset.id;
            // باز کردن modal و پر کردن فرم...
        });
    });


    $(function() {
        function jalaliToGregorianString(jalaliStr) {
            if (!jalaliStr) return null;
            var s = jalaliStr.trim().replace(/-/g, '/');
            try {
                var p = new persianDate(s);
                return p.toCalendar('gregorian').format('YYYY-MM-DD');
            } catch (e) {
                console.warn('jalali->gregorian failed:', s, e);
                return null;
            }
        }

        // from picker
        $("#from_picker").persianDatepicker({
            format: 'YYYY/MM/DD',
            observer: true,
            autoClose: true,
            initialValue: false,
            toolbox: {
                calendarSwitch: {
                    enabled: false
                }
            },
            onSelect: function(unix) {
                var g = jalaliToGregorianString($('#from_picker').val());
                $('#from').val(g ? g : $('#from_picker').val());
            }
        });

        // to picker
        $("#to_picker").persianDatepicker({
            format: 'YYYY/MM/DD',
            observer: true,
            autoClose: true,
            initialValue: false,
            toolbox: {
                calendarSwitch: {
                    enabled: false
                }
            },
            onSelect: function(unix) {
                var g = jalaliToGregorianString($('#to_picker').val());
                $('#to').val(g ? g : $('#to_picker').val());
            }
        });

        // مقدار اولیه hidden با تبدیل
        (function syncInitial() {
            var f = $('#from_picker').val();
            if (f) {
                var g = jalaliToGregorianString(f);
                if (g) $('#from').val(g);
            }
            var t = $('#to_picker').val();
            if (t) {
                var g2 = jalaliToGregorianString(t);
                if (g2) $('#to').val(g2);
            }
        })();

        // قبل از submit: مطمئن شو hiddenها مقدار میلادی دارند
        $('form').on('submit', function() {
            var f = jalaliToGregorianString($('#from_picker').val());
            if (f) $('#from').val(f);
            var t = jalaliToGregorianString($('#to_picker').val());
            if (t) $('#to').val(t);
        });
    });

    $(function() {

        // تبدیل اعداد فارسی به انگلیسی
        function convertPersianNumbersToEnglish(str) {
            if (!str) return str;
            const persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
            let result = str;
            persianNumbers.forEach((num, index) => {
                result = result.replace(new RegExp(num, 'g'), index);
            });
            return result;
        }

        function updateHiddenInput(pickerId, hiddenId) {
            var val = $('#' + pickerId).val();
            if (val) {
                $('#' + hiddenId).val(convertPersianNumbersToEnglish(val));
            } else {
                $('#' + hiddenId).val('');
            }
        }

        // هنگام انتخاب تاریخ در datepicker
        $("#from_picker").on('change', function() {
            updateHiddenInput('from_picker', 'from');
        });

        $("#to_picker").on('change', function() {
            updateHiddenInput('to_picker', 'to');
        });

        // قبل از submit فرم مطمئن شو hiddenها درست هستند
        $('form').on('submit', function() {
            updateHiddenInput('from_picker', 'from');
            updateHiddenInput('to_picker', 'to');
        });

        // اگر مقدار اولیه موجود است، hidden را یکبار پر کن
        updateHiddenInput('from_picker', 'from');
        updateHiddenInput('to_picker', 'to');
    });
//Delete Record Script
    document.addEventListener('DOMContentLoaded', function() {
        // گرفتن CSRF از meta
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // helper: نمایش toast موفقیت
        function showSuccessToast(message) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: message,
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                customClass: {
                    popup: 'swal2-rtl'
                }
            });
        }

        // helper: نمایش error
        function showError(title, text) {
            Swal.fire({
                icon: 'error',
                title: title || 'خطا',
                text: text || 'مشکلی پیش آمد.',
                customClass: {
                    popup: 'swal2-rtl'
                }
            });
        }

        // اضافه کردن rtl به پیام‌ها (اختیاری: کلاس css در ادامه می‌آید)
        // handler برای دکمه‌های حذف
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.dataset.url; // آدرس روت حذف
                const form = this.closest('form.delete-form');
                const row = form ? form.closest('tr') : null;

                // نمایش پنجره تأیید به فارسی
                Swal.fire({
                    title: 'آیا از حذف این رکورد مطمئن هستید؟',
                    text: 'این عمل قابل بازگشت نیست!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'بله، حذف کن',
                    cancelButtonText: 'خیر، منصرف شدم',
                    reverseButtons: true,
                    customClass: {
                        popup: 'swal2-rtl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // ارسال درخواست DELETE با fetch
                        fetch(url, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                },
                                credentials: 'same-origin'
                            })
                            .then(async response => {
                                const isJson = response.headers.get(
                                    'content-type')?.includes(
                                    'application/json');
                                const data = isJson ? await response.json() :
                                    null;

                                if (!response.ok) {
                                    // پاسخ خطا از سرور
                                    const msg = (data && data.message) ? data
                                        .message : 'حذف انجام نشد';
                                    throw new Error(msg);
                                }

                                // حذف ردیف از جدول با انیمیشن ساده
                                if (row) {
                                    row.style.transition =
                                        'opacity 0.25s ease, height 0.25s ease';
                                    row.style.opacity = '0';
                                    setTimeout(() => {
                                        row.remove();
                                    }, 260);
                                }

                                // نمایش پیام موفقیت
                                showSuccessToast((data && data.message) ? data
                                    .message : 'رکورد حذف شد');
                            })
                            .catch(err => {
                                console.error(err);
                                showError('خطا', err.message ||
                                    'در حذف رکورد خطا رخ داد.');
                                // اگر خواستی fallback: فرم را به صورت معمولی submit کن
                                // form.submit();
                            });
                    }
                });
            });
        });
    });
//End of Delete

//Edit Script
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Helper: تبدیل اعداد فارسی -> انگلیسی
        function persianToEnglishNums(str) {
            if (!str) return str;
            const pers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
            let out = String(str);
            pers.forEach((p, i) => {
                out = out.replace(new RegExp(p, 'g'), i);
            });
            return out;
        }

        // Helper: تبدیل میلادی -> شمسی برای نمایش (با persianDate اگر موجود باشد)
        function toJalaliDisplay(isoDate) {
            if (!isoDate) return '-';
            try {
                if (typeof persianDate !== 'undefined') {
                    const p = new persianDate(new Date(isoDate));
                    return p.format('YYYY/MM/DD');
                }
                return isoDate.split('T')[0] || isoDate;
            } catch (e) {
                return isoDate.split('T')[0] || isoDate;
            }
        }

        // تابع ایمن برای بروز رسانی سطر جدول
        function updateTableRow(updatedData) {
            if (!updatedData || !updatedData.id) {
                console.warn('updateTableRow: invalid data', updatedData);
                return;
            }

            const row = document.querySelector('tr[data-id="' + updatedData.id + '"]');
            if (!row) {
                console.warn('updateTableRow: row not found for id', updatedData.id);
                return;
            }

            // نگاشت فیلد -> سلکتور
            const mappings = {
                'row': '.cell-row',
                'Shahrestan': '.cell-Shahrestan',
                'sal': '.cell-sal',
                'erja': '.cell-erja',
                'kelace': '.cell-kelace',
                'motaghasi': '.cell-motaghasi',
                'masraf': '.cell-masraf',
                'q_m': '.cell-q_m',
                'V_m': '.cell-V_m',
                'sum': '.cell-sum',
                'baghi': '.cell-baghi',
                'mosavabat': '.cell-mosavabat',

            };

            Object.keys(mappings).forEach(field => {
                const sel = mappings[field];
                const cell = row.querySelector(sel);
                if (!cell) return; // اگر سلول وجود ندارد، رد کن
                if (field === 'erja') {
                    cell.textContent = updatedData.erja ? toJalaliDisplay(updatedData.erja) : '-';
                } else {
                    const val = (updatedData[field] !== null && updatedData[field] !== undefined &&
                        updatedData[field] !== '') ? updatedData[field] : '-';
                    cell.textContent = val;
                }
            });

            // های‌لایت کوتاه برای جلب توجه
            row.style.transition = 'background-color 0.6s ease';
            row.style.backgroundColor = '#e9f7ef';
            setTimeout(() => {
                row.style.backgroundColor = '';
            }, 700);
        }

        // --- ویرایش: باز کردن modal و پر کردن فرم ---
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                if (!id) return;

                const url = "{{ url('allocations') }}/" + id;

                fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('خطا در دریافت اطلاعات');
                        return res.json();
                    })
                    .then(data => {
                        // data = allocation object
                        document.getElementById('edit_id').value = data.id ?? '';
                        document.getElementById('edit_row').value = data.row ?? '';
                        document.getElementById('edit_Shahrestan').value = data
                            .Shahrestan ?? '';
                        document.getElementById('edit_sal').value = data.sal ?? '';
                        document.getElementById('edit_kelace').value = data.kelace ?? '';
                        document.getElementById('edit_motaghasi').value = data.motaghasi ??
                            '';
                        document.getElementById('edit_masraf').value = data.masraf ?? '';
                        document.getElementById('edit_q_m').value = data.q_m ?? '';
                        document.getElementById('edit_V_m').value = data.V_m ?? '';
                        document.getElementById('edit_sum').value = data.sum ?? '';
                        document.getElementById('edit_baghi').value = data.baghi ?? '';
                        document.getElementById('edit_mosavabat').value = data.mosavabat ??
                            '';

                        // تاریخ erja: نمایش در picker به صورت شمسی
                        if (data.erja) {
                            try {
                                const p = (typeof persianDate !== 'undefined') ?
                                    new persianDate(new Date(data.erja)) : null;
                                const jalali = p ? p.format('YYYY/MM/DD') : (data.erja
                                    .split('T')[0] || data.erja);
                                $('#edit_erja_picker').val(jalali);
                                // hidden field edit_erja (میلادی) رو هم set کن اگر می‌خوای، اما قبل از ذخیره ما آن را از visible می‌خوانیم و تبدیل می‌کنیم
                                $('#edit_erja').val(data.erja.split('T')[0] || data.erja);
                            } catch (e) {
                                $('#edit_erja_picker').val('');
                                $('#edit_erja').val('');
                            }
                        } else {
                            $('#edit_erja_picker').val('');
                            $('#edit_erja').val('');
                        }

                        // نمایش modal
                        const myModal = new bootstrap.Modal(document.getElementById(
                            'editModal'));
                        myModal.show();
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'خطا',
                            text: err.message || 'خطا در دریافت اطلاعات',
                            customClass: {
                                popup: 'swal2-rtl'
                            }
                        });
                    });
            });
        });

        // مقداردهی datepicker برای modal (یکبار)
        $('#editModal').on('shown.bs.modal', function() {
            if (!$('#edit_erja_picker').data('pd')) {
                $('#edit_erja_picker').persianDatepicker({
                    format: 'YYYY/MM/DD',
                    observer: true,
                    autoClose: true,
                    initialValue: false
                }).data('pd', true);
            }
        });

        // --- ذخیره تغییرات با AJAX ---
        const saveBtn = document.getElementById('saveEditBtn');
        if (saveBtn) {
            saveBtn.addEventListener('click', function() {
                const id = document.getElementById('edit_id').value;
                if (!id) return;
                const url = "{{ url('allocations') }}/" + id;

                // جمع‌آوری داده‌ها
                const payload = {
                    row: document.getElementById('edit_row').value,
                    Shahrestan: document.getElementById('edit_Shahrestan').value,
                    sal: document.getElementById('edit_sal').value,
                    // تلاش می‌کنیم visible picker رو به انگلیسی تبدیل کنیم
                    erja: persianToEnglishNums($('#edit_erja_picker').val()) || $('#edit_erja')
                        .val() || '',
                    kelace: document.getElementById('edit_kelace').value,
                    motaghasi: document.getElementById('edit_motaghasi').value,
                    masraf: document.getElementById('edit_masraf').value,
                    q_m: document.getElementById('edit_q_m').value,
                    V_m: document.getElementById('edit_V_m').value,
                    sum: document.getElementById('edit_sum').value,
                    baghi: document.getElementById('edit_baghi').value,
                    mosavabat: document.getElementById('edit_mosavabat').value,
                };

                fetch(url, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload),
                        credentials: 'same-origin'
                    })
                    .then(async res => {
                        const json = await res.json().catch(() => null);
                        if (!res.ok) {
                            const msg = json && json.message ? json.message :
                                'خطا در ذخیره‌سازی';
                            throw new Error(msg);
                        }
                        return json;
                    })
                    .then(json => {
                        // json.data حاوی allocation به‌روز شده است
                        const updatedData = (json && json.data) ? json.data : null;
                        if (updatedData) {
                            updateTableRow(updatedData);
                        } else {
                            console.warn('No updated data returned');
                        }

                        // بستن modal
                        const modalEl = document.getElementById('editModal');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();

                        Swal.fire({
                            icon: 'success',
                            title: 'موفق',
                            text: (json && json.message) ? json.message : 'ذخیره شد',
                            toast: true,
                            position: 'top-end',
                            timer: 2000,
                            showConfirmButton: false,
                            customClass: {
                                popup: 'swal2-rtl'
                            }
                        });
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'خطا',
                            text: err.message || 'خطا در ذخیره تغییرات',
                            customClass: {
                                popup: 'swal2-rtl'
                            }
                        });
                    });
            });
        }
    });
});
//End of Edit Script -->