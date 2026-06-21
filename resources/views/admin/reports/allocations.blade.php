@component('admin.layouts.content')
    @section('title', 'گزارش تخصیص‌ها')

@section('content')
    <style>
        body {
            background: #f4f6f9;
        }

        .dashboard-header {
            background: #fff;
            border-radius: 18px;
            padding: 25px 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, .08);
            margin-bottom: 25px;
            direction: rtl;
        }

        .dashboard-title {
            font-size: 30px;
            font-weight: 700;
            color: #12385b;

        }

        .dashboard-subtitle {
            color: #7a8793;
            margin-top: 8px;
        }

        .stat-card {
            background: #fff;
            border-radius: 18px;
            padding: 22px;
            transition: .25s;
            box-shadow: 0 5px 18px rgba(0, 0, 0, .06);
            border: none;
            overflow: hidden;
            position: relative;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, .12);
        }

        .stat-card:before {
            content: "";
            position: absolute;
            right: 0;
            top: 0;
            width: 7px;
            height: 100%;
            background: #0d6efd;
        }

        .stat-icon {
            width: 58px;
            height: 58px;
            border-radius: 14px;
            background: #eef5ff;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 26px;
            color: #0d6efd;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 30px;
            font-weight: bold;
            color: #0d2d55;
        }

        .stat-title {
            color: #7d8b99;
            font-size: 14px;
        }

        .filter-card {

            border: none;
            border-radius: 18px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, .06);
        }

        .filter-header {
            background: #0d6efd;
            color: white;
            padding: 15px 20px;
            border-radius: 18px 18px 0 0;
            font-size: 18px;
            font-weight: bold;
        }

        .filter-body {
            padding: 25px;
        }

        .table-card {
            border: none;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 5px 18px rgba(0, 0, 0, .08);
        }

        .table-card .card-header {
            background: #12385b;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .table thead {

            background: #edf4fb;

        }

        .table thead th {

            color: #12385b;
            font-weight: bold;
            border: none;

        }

        .table tbody tr {

            transition: .2s;

        }

        .table tbody tr:hover {

            background: #eef6ff;

        }

        .btn-primary {

            border-radius: 10px;
            padding: 9px 22px;

        }

        .btn-outline-secondary {

            border-radius: 10px;
            padding: 9px 22px;

        }

        .form-label {

            font-weight: bold;
            /* color:#12385b; */

        }

        .border {

            border: 1px solid #dfe6ee !important;

        }

        /* .form-check{

                padding:4px 0;

            } */

        .form-check:hover {

            background: #f5f8fc;

        }

        .report-card {

            border: none;

            border-radius: 18px;

            overflow: hidden;

            box-shadow: 0 8px 24px rgba(20, 40, 80, .08);

        }

        .report-header {

            background: linear-gradient(90deg, #143a5e, #1e5f93);

            color: #fff;

            padding: 18px 25px;

        }

        .report-header h5 {

            margin: 0;

            font-weight: 700;

        }

        .report-header small {

            opacity: .85;

        }

        .allocation-table2 {

            margin-bottom: 0;

        }

        .allocation-table2 thead th {

            position: sticky;

            top: 0;

            z-index: 10;

            background: #edf4fb;

            color: #12385b;

            font-size: 14px;

            font-weight: 700;

            border-bottom: 2px solid #d8e6f3;

            white-space: nowrap;

            vertical-align: middle;

        }

        .allocation-table2 tbody td {

            vertical-align: middle;

            font-size: 14px;

        }

        .allocation-table2 tbody tr {

            transition: .2s;

        }

        .allocation-table2 tbody tr:hover {

            background: #eef7ff;

            transform: scale(1.002);

        }

        .allocation-table2 tbody tr:nth-child(even) {

            background: #fbfcfe;

        }

        .report-table {

            margin: 0;

            border-collapse: separate;

            border-spacing: 0;

        }

        .report-table thead {

            background: #17375e;

        }

        .report-table thead th {

            color: #fff;

            font-weight: 600;

            font-size: 14px;

            padding: 14px 10px;

            border: none;

            white-space: nowrap;

            text-align: center;

        }

        .report-table tbody td {

            padding: 12px 10px;

            border-bottom: 1px solid #eceff3;

            font-size: 14px;

            color: #3b4652;

            vertical-align: middle;

        }

        .report-table tbody tr {

            transition: .18s;

            background: #fff;

        }

        .report-table tbody tr:hover {

            background: #f7f9fc;

        }

        .report-table tbody tr:last-child td {

            border-bottom: none;

        }

        .report-table td.numeric {

            text-align: center;

            font-weight: 600;

            color: #17375e;

        }

        .total-row {

            background: #eef2f7 !important;

            font-weight: bold;

        }

        .total-row td {

            color: #17375e;

            font-size: 15px;

            border-top: 2px solid #d7dee8;

        }

        .document-name {

            font-weight: 600;

            color: #17375e;

        }

        .card.report-card {

            border: none;

            border-radius: 16px;

            overflow: hidden;

            box-shadow: 0 5px 18px rgba(0, 0, 0, .08);

        }
    </style>
    <div class="container-fluid py-3">



        {{-- تیتر صفحه --}}
        <div class="dashboard-header">

            <div class="row align-items-center">

                <div class="col-lg-8">

                    <div class="dashboard-title">

                        <i class="bi bi-speedometer2"></i>

                        داشبورد مدیریتی تخصیص منابع آب

                    </div>

                    <div class="dashboard-subtitle">

                        گزارش‌های تجمیعی و تحلیلی تخصیص منابع آب استان سمنان

                    </div>

                </div>

                <div class="col-lg-4 text-end">

                    <div class="text-muted">

                        <i class="bi bi-calendar3"></i>

                        {{ \Morilog\Jalali\Jalalian::now()->format('Y/m/d') }}

                    </div>

                </div>

            </div>

        </div>


        {{-- فرم فیلترها --}}
        <div class="card filter-card mb-4">

            <div class="filter-header d-flex justify-content-between align-items-center">

                <div>

                    <i class="bi bi-funnel-fill me-2"></i>

                    فیلترهای گزارش مدیریتی

                </div>

                <small class="opacity-75">

                    انتخاب محدوده گزارش

                </small>

            </div>

            <div class="filter-body">
                <form id="filtersForm" method="GET" action="{{ route('reports.allocations') }}"
                    class="row g-3 align-items-start text-end">

                    {{-- گروه تخصیص --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold">گروه تخصیص</label>
                        <div class="border rounded p-2" style="max-height:180px; overflow:auto;">
                            @foreach ($takhsisOptions as $opt)
                                <div class="form-check text-end">
                                    <input class="form-check-input" type="checkbox" name="Takhsis_group[]"
                                        value="{{ $opt }}" id="takhsis-{{ $loop->index }}"
                                        {{ isset($takhsis) && in_array($opt, $takhsis) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="takhsis-{{ $loop->index }}">
                                        {{ $opt }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- کد محدوده مطالعاتی --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold">کد محدوده مطالعاتی</label>
                        <div class="border rounded p-2" style="max-height:180px; overflow:auto;">
                            {{-- گزینه همه --}}
                            <div class="form-check text-end mb-2">
                                <input class="form-check-input" type="checkbox" id="code-all">
                                <label class="form-check-label fw-bold" for="code-all">
                                    همه
                                </label>
                            </div>
                            @foreach ($codesAll as $c)
                                <div class="form-check text-end">
                                    <input class="form-check-input" type="checkbox" name="code[]"
                                        value="{{ $c }}" id="code-{{ $loop->index }}"
                                        {{ isset($codes) && in_array($c, $codes) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="code-{{ $loop->index }}">
                                        {{ $c }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- دسته‌بندی‌ها (Checkbox tree) --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">دسته‌بندی‌ سندها</label>
                        <div id="category-tree" class="border rounded p-2" style="max-height:250px; overflow:auto;">
                            @foreach ($rootCategories as $cat)
                                <div class="category-node mb-1" data-id="{{ $cat->id }}" data-level="0">
                                    <input type="checkbox" class="category-checkbox" data-level="0"
                                        value="{{ $cat->id }}" id="cat-{{ $cat->id }}">
                                    <label for="cat-{{ $cat->id }}">{{ $cat->name }}</label>
                                    <div class="children-container ms-3 mt-1"></div>
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="file_category_ids" id="file_category_ids"
                            value="{{ implode(',', $fileCategoryIds ?? []) }}">
                    </div>

                    {{-- دکمه‌ها --}}
                    <div class="col-12 mt-4">

                        <hr>

                        <div class="d-flex justify-content-start text-end">

                            <button type="submit" class="btn btn-primary">

                                <i class="bi bi-bar-chart-fill"></i>

                                تولید گزارش

                            </button>

                            <a href="{{ route('reports.allocations') }}" class="btn btn-light border me-2">

                                <i class="bi bi-arrow-counterclockwise"></i>

                                بازنشانی

                            </a>



                        </div>

                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">

            <div class="col-lg-8">

                <div class="card border-0 shadow-sm rounded-4 h-100">

                    <div class="card-body">

                        <h5 class="fw-bold text-primary mb-3">

                            <i class="bi bi-info-circle-fill me-2"></i>

                            خلاصه گزارش

                        </h5>

                        <div class="row">

                            <div class="col-md-4">

                                <div class="summary-item">

                                    <div class="summary-title">

                                        تعداد اسناد

                                    </div>

                                    <div class="summary-value">

                                        {{ count($perFileRows) }}

                                    </div>

                                </div>

                            </div>

                            <div class="col-md-4">

                                <div class="summary-item">

                                    <div class="summary-title">

                                        کل متقاضیان

                                    </div>

                                    <div class="summary-value">

                                        {{ $grandApplicants }}

                                    </div>

                                </div>

                            </div>

                            <div class="col-md-4">

                                <div class="summary-item">

                                    <div class="summary-title">

                                        حجم باقی مانده

                                    </div>

                                    <div class="summary-value">

                                        {{ number_format($grandTotal->remaining ?? 0) }}

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="card border-0 shadow-sm rounded-4 h-100">

                    <div class="card-body">

                        <h5 class="fw-bold text-primary mb-4">

                            <i class="bi bi-lightning-charge-fill"></i>

                            عملیات سریع

                        </h5>

                        <div class="d-grid gap-3">

                            <button class="btn btn-primary">

                                <i class="bi bi-bar-chart-fill me-2"></i>

                                بروزرسانی گزارش

                            </button>

                            <button class="btn btn-outline-success">

                                <i class="bi bi-file-earmark-excel-fill me-2"></i>

                                خروجی Excel

                            </button>

                            <button class="btn btn-outline-danger">

                                <i class="bi bi-file-earmark-pdf-fill me-2"></i>

                                خروجی PDF

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="row mb-4">

            <div class="col-lg-3 col-md-6 mb-3">

                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="bi bi-folder2-open"></i>

                    </div>

                    <div class="stat-number">

                        {{ count($perFileRows) }}

                    </div>

                    <div class="stat-title">

                        تعداد اسناد

                    </div>

                </div>

            </div>

            <div class="col-lg-3 col-md-6 mb-3">

                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="bi bi-people-fill"></i>

                    </div>

                    <div class="stat-number">

                        {{ $grandApplicants }}

                    </div>

                    <div class="stat-title">

                        تعداد متقاضیان

                    </div>

                </div>

            </div>

            <div class="col-lg-3 col-md-6 mb-3">

                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="bi bi-droplet-half"></i>

                    </div>

                    <div class="stat-number">

                        {{ number_format($grandTotal->cost ?? 0) }}

                    </div>

                    <div class="stat-title">

                        حجم تخصیص

                    </div>

                </div>

            </div>

            <div class="col-lg-3 col-md-6 mb-3">

                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="bi bi-pie-chart-fill"></i>

                    </div>

                    <div class="stat-number">

                        {{ number_format($grandTotal->remaining ?? 0) }}

                    </div>

                    <div class="stat-title">

                        حجم باقیمانده

                    </div>

                </div>

            </div>

        </div>



        {{-- جدول گزارش --}}
        <div class="card report-card">

            <div class="report-header d-flex justify-content-between align-items-center">

                <div>

                    <h5>

                        <i class="bi bi-table"></i>

                        گزارش تجمیعی تخصیص‌ها

                    </h5>

                    <small>

                        اطلاعات بر اساس فیلترهای انتخابی

                    </small>

                </div>

                <div>

                    <span class="badge bg-light text-dark">

                        {{ count($perFileRows) }}

                        سند

                    </span>

                </div>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table report-table mb-0">

                        <thead>

                            <tr>

                                <th>نام سند</th>

                                <th>کد محدوده</th>

                                <th>گروه تخصیص</th>

                                <th>تعداد متقاضی</th>

                                <th>سقف تخصیص</th>

                                <th>حجم تخصیص یافته</th>

                                <th>حجم باقی مانده</th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($perFileRows as $r)
                                <tr>

                                    <td class="document-name">

                                        {{ $r->file_name }}

                                    </td>

                                    <td class="numeric">

                                        {{ $r->code }}

                                    </td>

                                    <td>

                                        {{ $r->Takhsis_group }}

                                    </td>

                                    <td class="numeric">

                                        {{ number_format($r->applicants_count) }}

                                    </td>

                                    <td class="numeric">

                                        {{ number_format($r->total_volume, 2) }}

                                    </td>

                                    <td class="numeric">

                                        {{ number_format($r->cost, 2) }}

                                    </td>

                                    <td class="numeric">

                                        {{ number_format($r->remaining, 2) }}

                                    </td>

                                </tr>
                            @endforeach

                            @if ($grandTotal)
                                <tr class="total-row">

                                    <td colspan="3">

                                        جمع کل گزارش

                                    </td>

                                    <td class="numeric">

                                        {{ number_format($grandApplicants) }}

                                    </td>

                                    <td class="numeric">

                                        {{ number_format($grandTotal->total_volume, 2) }}

                                    </td>

                                    <td class="numeric">

                                        {{ number_format($grandTotal->cost, 2) }}

                                    </td>

                                    <td class="numeric">

                                        {{ number_format($grandTotal->remaining, 2) }}

                                    </td>

                                </tr>
                            @endif

                        </tbody>

                    </table>

                </div>

            </div>
            {{-- <div class="card-footer d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    نمایش {{ $rows->firstItem() ?? 0 }} تا {{ $rows->lastItem() ?? 0 }} از {{ $rows->total() }}
                </div>
                <div>
                    {{ $rows->withQueryString()->links() }}
                </div>
            </div> --}}
        </div>
    </div>
@endsection





@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const tree = document.getElementById('category-tree');
            const hiddenInput = document.getElementById('file_category_ids');

            /*------------------------------
            | Helper: گرفتن فرزندان DOM
            ------------------------------*/
            function getChildrenNodes(node) {
                return [...node.querySelector(':scope > .children-container').children].filter(
                    el => el.classList.contains('category-node')
                );
            }

            function getNodeCheckbox(node) {
                return node.querySelector(':scope > .category-checkbox');
            }

            /*------------------------------
            | آپدیت hidden input
            ------------------------------*/
            function updateHiddenInput() {
                let resultIds = new Set();
                const allNodes = tree.querySelectorAll('.category-node');

                allNodes.forEach(node => {
                    const checkbox = getNodeCheckbox(node);
                    if (!checkbox.checked) return;

                    const children = getChildrenNodes(node);
                    if (children.length === 0) {
                        resultIds.add(checkbox.value); // فقط برگ‌ها
                    }
                });

                hiddenInput.value = [...resultIds].join(',');
            }

            /*------------------------------
            | Ajax → دریافت فرزندان از سرور
            ------------------------------*/
            async function fetchChildren(parentId) {
                const res = await fetch("{{ route('reports.fileCategories.children', '') }}/" + parentId);
                if (!res.ok) return [];
                return await res.json();
            }

            /*------------------------------
            | افزودن فرزندان به DOM
            ------------------------------*/
            async function addChildren(node, parentId, level) {
                const children = await fetchChildren(parentId);
                const container = node.querySelector('.children-container');
                container.innerHTML = '';

                children.forEach(child => {
                    const div = document.createElement('div');
                    div.className = 'category-node mb-1';
                    div.dataset.id = child.id;
                    div.dataset.level = level;

                    const cb = document.createElement('input');
                    cb.type = 'checkbox';
                    cb.className = 'category-checkbox';
                    cb.dataset.level = level;
                    cb.value = child.id;

                    const label = document.createElement('label');
                    label.textContent = child.name;
                    label.htmlFor = 'cat-' + child.id;

                    const childContainer = document.createElement('div');
                    childContainer.className = 'children-container ms-3 mt-1';

                    div.append(cb, label, childContainer);
                    container.appendChild(div);
                });

                return container.querySelectorAll('.category-node');
            }

            /*------------------------------
            | تیک زدن تمام فرزندان recursively
            ------------------------------*/
            async function checkAndLoadAllDescendants(node) {
                const checkbox = getNodeCheckbox(node);
                checkbox.checked = true;

                const childrenContainer = node.querySelector('.children-container');
                if (childrenContainer.children.length === 0) {
                    // اگر هنوز لود نشده، از سرور لود کن
                    await addChildren(node, checkbox.value, parseInt(checkbox.dataset.level) + 1);
                }

                const children = getChildrenNodes(node);
                for (const child of children) {
                    await checkAndLoadAllDescendants(child);
                }
            }

            /*------------------------------
            | Event listener برای change
            ------------------------------*/
            tree.addEventListener('change', async (e) => {
                if (!e.target.classList.contains('category-checkbox')) return;

                const node = e.target.closest('.category-node');

                if (e.target.checked) {
                    await checkAndLoadAllDescendants(node);
                } else {
                    // uncheck همه فرزندان و پاک کردن container
                    const children = node.querySelectorAll('.category-node .category-checkbox');
                    children.forEach(cb => cb.checked = false);
                    node.querySelector('.children-container').innerHTML = '';
                }

                updateHiddenInput();
            });

        });



        document.addEventListener('DOMContentLoaded', () => {
            const selectAllCheckbox = document.getElementById('code-all');
            const codeCheckboxes = document.querySelectorAll('input[name="code[]"]');

            selectAllCheckbox.addEventListener('change', () => {
                codeCheckboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
            });

            // اگر هر checkbox دیگری تغییر کرد، "همه" هم باید آپدیت شود
            codeCheckboxes.forEach(cb => {
                cb.addEventListener('change', () => {
                    const allChecked = Array.from(codeCheckboxes).every(cb => cb.checked);
                    selectAllCheckbox.checked = allChecked;
                });
            });
        });
    </script>
@endpush
@endcomponent
