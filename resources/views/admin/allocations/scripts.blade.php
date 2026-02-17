{{-- === اسکریپت راه‌انداز datepicker و تبدیل شمسی → میلادی === --}}
<script>
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
</script>
<script>
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
</script>
<!-- For Delete Record -->
<script>
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
</script>

<style>
    /* SweetAlert2 rtl support small tweak (make texts RTL) */
    .swal2-rtl .swal2-title,
    .swal2-rtl .swal2-html-container {
        direction: rtl;
        text-align: right;
        font-family: 'BNazanin', Tahoma, sans-serif;
        color: brown;
        /* اگر BNazanin رو افزودی */
    }

    /* دکمه های sweetalert در rtl */
    .swal2-rtl .swal2-actions {
        direction: rtl;
    }
</style>
<!-- End of Delete Script -->

<!-- Edit Script (اصلاح شده) -->
<!-- Edit Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        function persianToEnglishNums(str) {
            if (!str) return str;
            const pers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
            let out = String(str);
            pers.forEach((p, i) => {
                out = out.replace(new RegExp(p, 'g'), i);
            });
            return out;
        }

        function toJalaliDisplay(isoDate) {
            if (!isoDate) return '-';
            try {
                if (typeof persianDate !== 'undefined') {
                    return new persianDate(new Date(isoDate)).format('YYYY/MM/DD');
                }
                return isoDate.split('T')[0] || isoDate;
            } catch (e) {
                return isoDate.split('T')[0] || isoDate;
            }
        }

        function getModalFloat(el) {
            if (!el) return 0;
            const v = persianToEnglishNums(el.value || '');
            const n = parseFloat(v);
            return Number.isFinite(n) ? n : 0;
        }

        async function computeSumModal() {
            const file = document.getElementById('edit_file_name')?.value || '';
            const code = document.getElementById('edit_code').value || null;
            const tak = document.getElementById('edit_Takhsis_group').value || null;
            const vm = getModalFloat(document.getElementById('edit_V_m'));

            try {
                const res = await fetch("{{ route('allocations.computeSum') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        file_name: file || null,
                        code,
                        Takhsis_group: tak,
                        V_m: vm
                    })
                });
                if (res.ok) {
                    const json = await res.json();
                    document.getElementById('edit_sum').value = parseFloat(json.sum || 0).toFixed(3);
                    const t = getModalFloat(document.getElementById('edit_t_mosavvab'));
                    const s = getModalFloat(document.getElementById('edit_sum'));
                    document.getElementById('edit_baghi').value = (t - s).toFixed(3);
                }
            } catch (e) {
                console.error(e);
            }
        }

        async function fetchTMosavvabModal() {
            const file = document.getElementById('edit_file_name')?.value || '';
            const code = document.getElementById('edit_code').value || '';
            const tak = document.getElementById('edit_Takhsis_group').value || '';
            if (!file || !code || !tak) {
                document.getElementById('edit_t_mosavvab').value = 0;
                return;
            }

            try {
                const res = await fetch("{{ route('allocations.computeTMosavvab') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        file_name: file,
                        code,
                        Takhsis_group: tak
                    })
                });
                if (res.ok) {
                    const json = await res.json();
                    document.getElementById('edit_t_mosavvab').value = parseFloat(json.t_mosavvab || 0)
                        .toFixed(3);
                }
            } catch (e) {
                console.error(e);
            }
        }

        // باز کردن modal و پر کردن فرم
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', async function() {
                const id = this.dataset.id;
                if (!id) return;

                const url = "/allocations/" + encodeURIComponent(id) + "/data";
                try {
                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    });
                    if (!res.ok) throw new Error('خطا در دریافت اطلاعات (' + res.status +
                        ')');
                    const data = await res.json();

                    // پر کردن selectها با fetch گزینه‌ها
                    const fileName = data.file_name || '';
                    if (fileName) {
                        try {
                            const optsRes = await fetch(
                                `/allocations/filter-options?file_name=` +
                                encodeURIComponent(fileName), {
                                    credentials: 'same-origin'
                                });
                            if (optsRes.ok) {
                                const opts = await optsRes.json();
                                const codeSelect = document.getElementById('edit_code');
                                codeSelect.innerHTML =
                                    '<option value="">انتخاب کنید</option>';
                                opts.codes.forEach(c => codeSelect.innerHTML +=
                                    `<option value="${c}">${c}</option>`);
                                codeSelect.value = data.code ?? '';

                                const takSelect = document.getElementById(
                                    'edit_Takhsis_group');
                                takSelect.innerHTML =
                                    '<option value="">انتخاب کنید</option>';
                                opts.takhsis.forEach(t => takSelect.innerHTML +=
                                    `<option value="${t}">${t}</option>`);
                                takSelect.value = data.Takhsis_group ?? '';
                            }
                        } catch (e) {
                            console.error(e);
                        }
                    }

                    // سایر inputها
                    document.getElementById('edit_id').value = data.id ?? '';
                    document.getElementById('edit_row').value = data.row ??
                        "{{ old('row') }}";
                    document.getElementById('edit_Shahrestan').value = data.Shahrestan ??
                        "{{ old('Shahrestan') }}";
                    document.getElementById('edit_sal').value = data.sal ?? '';
                    document.getElementById('edit_kelace').value = data.kelace ?? '';
                    document.getElementById('edit_motaghasi').value = data.motaghasi ?? '';
                    document.getElementById('edit_masraf').value = data.masraf ?? '';
                    document.getElementById('edit_q_m').value = data.q_m ?? '';
                    document.getElementById('edit_V_m').value = data.V_m ?? '';
                    document.getElementById('edit_sum').value = data.sum ?? '';
                    document.getElementById('edit_baghi').value = data.baghi ?? '';
                    document.getElementById('edit_mosavabat').value = data.mosavabat ?? '';
                    document.getElementById('edit_file_name').value = data.file_name ?? '';
                    document.getElementById('edit_t_mosavvab').value = data.t_mosavvab ?? 0;

                    // تاریخ
                    // init datepicker if not already inited (use jQuery.data on the element, not on plugin return)
                    if (!$('#edit_erja_picker').data('pd')) {
                        $('#edit_erja_picker').persianDatepicker({
                            format: 'YYYY/MM/DD',
                            observer: true,
                            autoClose: true,
                            initialValue: false,
                            onSelect: function() {
                                const cleaned = persianToEnglishNums($(
                                    '#edit_erja_picker').val());
                                $('#edit_erja').val(cleaned);
                            }
                        });
                        $('#edit_erja_picker').data('pd', true);
                    }
                    // set visible + hidden values for erja (do this regardless of init)
                    if (data.erja) {
                        const jalali = (typeof persianDate !== 'undefined') ? (
                                new persianDate(new Date(data.erja))).format('YYYY/MM/DD') :
                            (data.erja.split('T')[0] || data.erja);
                        $('#edit_erja_picker').val(jalali);
                        $('#edit_erja').val(data.erja.split('T')[0] || data.erja);
                    } else {
                        $('#edit_erja_picker').val('');
                        $('#edit_erja').val('');
                    }


                    // نمایش modal
                    new bootstrap.Modal(document.getElementById('editModal')).show();

                } catch (err) {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'خطا',
                        text: err.message || 'خطا در دریافت اطلاعات',
                        customClass: {
                            popup: 'swal2-rtl'
                        }
                    });
                }
            });
        });

        // محاسبات sum و t_mosavvab هنگام تغییر
        document.getElementById('edit_code').addEventListener('change', async () => {
            await fetchTMosavvabModal();
            await computeSumModal();
        });
        document.getElementById('edit_Takhsis_group').addEventListener('change', async () => {
            await fetchTMosavvabModal();
            await computeSumModal();
        });
        document.getElementById('edit_V_m').addEventListener('input', async () => {
            await computeSumModal();
        });

    });
</script>
<!-- End of Edit Script -->


<!-- End Edit Script (اصلاح شده) -->

<!-- Scroll Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const topBar = document.querySelector('.table-scroll-top');
        const topInner = topBar ? topBar.querySelector('.table-scroll-inner') : null;
        const tableContainer = document.getElementById('table-container');

        if (!topBar || !topInner || !tableContainer) return;

        function tableElement() {
            return tableContainer.querySelector('table');
        }

        // تنظیم عرض داخلی نوار بالا برابر با عرض واقعی جدول
        function adjustTopBarWidth() {
            const table = tableElement();
            if (!table) {
                topInner.style.width = '1px';
                return;
            }
            topInner.style.width = table.scrollWidth + 'px';
        }

        // اسکرول را نسبت به عرض سایر المانها سینک میکند (راهکار قابل اطمینان برای LTR و RTL)
        function syncScrollFrom(source, target) {
            // محاسبه نسبت اسکرول منبع
            const maxSource = Math.max(1, source.scrollWidth - source.clientWidth);
            const ratio = source.scrollLeft / maxSource; // در بازه 0..1
            const maxTarget = Math.max(1, target.scrollWidth - target.clientWidth);
            target.scrollLeft = Math.round(ratio * maxTarget);
        }

        // رویدادهای همگام‌سازی دوطرفه
        let isSyncingFromTop = false;
        let isSyncingFromTable = false;

        topBar.addEventListener('scroll', function() {
            if (isSyncingFromTable) return;
            isSyncingFromTop = true;
            syncScrollFrom(topBar, tableContainer);
            // کوچکترین تأخیر برای جلوگیری از حلقه‌ی بازگشتی
            setTimeout(() => {
                isSyncingFromTop = false;
            }, 0);
        });

        tableContainer.addEventListener('scroll', function() {
            if (isSyncingFromTop) return;
            isSyncingFromTable = true;
            syncScrollFrom(tableContainer, topBar);
            setTimeout(() => {
                isSyncingFromTable = false;
            }, 0);
        });

        // وقتی جدول تغییر می‌کند (مثلاً صفحه‌بندی یا فیلتر) باید دوباره اندازه‌گیری کنیم
        const observer = new MutationObserver(function() {
            adjustTopBarWidth();
        });

        // observe تغییرات داخل container (محتوا یا جدول)
        observer.observe(tableContainer, {
            childList: true,
            subtree: true,
            characterData: true
        });

        // resize پنجره را هم هندل کن
        window.addEventListener('resize', adjustTopBarWidth);

        // اگر جدول بعد از تصاویر یا محتوای async ساخته می‌شود، چند بار تلاش کن
        function tryAdjust(retries = 5, delay = 150) {
            adjustTopBarWidth();
            if (retries <= 0) return;
            // اگر عرض داخلی بسیار کوچک است احتمالاً جدول هنوز کامل رندر نشده
            if (topInner.getBoundingClientRect().width <= 2) {
                setTimeout(() => tryAdjust(retries - 1, delay * 1.5), delay);
            }
        }

        tryAdjust(8, 150);
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // وقتی روی "همه" کلیک می‌شود
    document.querySelectorAll('.select-all').forEach(allBox => {
        allBox.addEventListener('change', function () {
            const target = this.dataset.target;
            document.querySelectorAll('.' + target + '-item')
                .forEach(cb => cb.checked = this.checked);
        });
    });

    // اگر همه آیتم‌ها تیک خورد → "همه" فعال شود
    document.querySelectorAll('.filter-box').forEach(box => {
        const items = box.querySelectorAll('input[type=checkbox]:not(.select-all)');
        const all = box.querySelector('.select-all');

        if (!all) return;

        const syncAll = () => {
            all.checked = [...items].every(i => i.checked);
        };

        items.forEach(i => i.addEventListener('change', syncAll));
        syncAll(); // وضعیت اولیه
    });

});
</script>

<!-- For Approve Record -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const csrfToken = document.querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');

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

    document.querySelectorAll('.btn-approve').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const url = this.dataset.url;
            const form = this.closest('form.approve-form');
            const row = form ? form.closest('tr') : null;
            const approveBtn = this;

            Swal.fire({
                title: 'تأیید نهایی رکورد',
                text: 'آیا از تأیید این رکورد مطمئن هستید؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'بله، تأیید شود',
                cancelButtonText: 'انصراف',
                reverseButtons: true,
                customClass: {
                    popup: 'swal2-rtl'
                }
            }).then((result) => {
                if (result.isConfirmed) {

                    fetch(url, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin'
                    })
                    .then(async response => {
                        const isJson = response.headers
                            .get('content-type')
                            ?.includes('application/json');

                        const data = isJson ? await response.json() : null;

                        if (!response.ok) {
                            const msg = (data && data.message)
                                ? data.message
                                : 'تأیید انجام نشد';
                            throw new Error(msg);
                        }

                        // تغییر ظاهر ردیف بعد از تأیید
                        if (row) {
                            row.classList.remove('table-warning');
                            row.classList.add('table-success');
                        }

                        // حذف دکمه تأیید
                        approveBtn.remove();

                        // تغییر متن وضعیت (اگر ستون وضعیت داری)
                        const statusBadge = row?.querySelector('.allocation-status');
                        if (statusBadge) {
                            statusBadge.innerHTML =
                                '<span class="badge bg-success">تأیید شده</span>';
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'موفق',
                            text: (data && data.message)
                                ? data.message
                                : 'رکورد با موفقیت تأیید شد',
                            confirmButtonText: 'باشه',
                            customClass: {
                                popup: 'swal2-rtl'
                            }
                        }).then(() => {
                            location.reload();
                        });

                    })
                    .catch(err => {
                        console.error(err);
                        showError('خطا', err.message || 'در تأیید رکورد خطا رخ داد.');
                    });
                }
            });
        });
    });

});
</script>
<!-- End of Approve Script -->


