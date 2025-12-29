@component('admin.layouts.content')
    @section('title', 'گزارش تخصیص‌ها')

@section('content')
    <div class="container-fluid py-3">

        {{-- تیتر صفحه --}}
        <div class="mb-3 text-center">
            <h3 class="fw-bold dash">داشبورد تخصصی منابع آب سمنان</h3>
        </div>

        {{-- فرم فیلترها --}}
        <div class="card mb-3">
            <div class="card-body">
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
                    <div class="col-12 text-end mt-2">
                        <button type="submit" class="btn btn-primary btn-sm me-1">دریافت گزارش تجمیعی </button>
                        <a href="{{ route('reports.allocations') }}" class="btn btn-outline-secondary btn-sm">ریست</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- جدول گزارش --}}
        <div class="card">
            <div class="card-body p-2">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered mb-0 text-center allocation-table">
                        <thead class="table-light">
                            <tr>
                                <th>نام سند</th>
                                <th>کد محدوده مطالعاتی</th>
                                <th>گروه تخصیص</th>
                                <th>تعداد متقاضیان</th>
                                <th>سقف تخصیص ابلاغی (مترمکعب در سال)</th>
                                <th>هزینه (جمع حجم مصوب به مترمکعب در سال)</th>
                                <th>حجم باقی مانده (مترمکعب در سال)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($perFileRows as $r)
                                <tr>
                                    <td>{{ $r->file_name }}</td>
                                    <td>{{ $r->code }}</td>
                                    <td>{{ $r->Takhsis_group }}</td>
                                    <td>{{ $r->applicants_count }}</td>
                                    <td>{{ number_format($r->total_volume * 1000) }}</td>
                                    <td>{{ number_format($r->cost * 1000) }}</td>
                                    <td>{{ number_format($r->remaining * 1000) }}</td>
                                </tr>
                            @endforeach

                            
                            @forelse($rows as $r)
                               {{-- سطر جمع کل --}}
                            @if($grandTotal)
                                <tr class="table-warning fw-bold">
                                    <td colspan="3">جمع کل همه سندها</td>
                                    <td>{{ $grandApplicants }}</td>
                                    <td>{{ number_format($grandTotal->total_volume * 1000) }}</td>
                                    <td>{{ number_format($grandTotal->cost * 1000) }}</td>
                                    <td>{{ number_format($grandTotal->remaining * 1000) }}</td>
                                </tr>
                                
                            @endif
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">رکوردی برای نمایش وجود ندارد.</td>
                                </tr>
                            @endforelse
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
    </script>
@endpush
@endcomponent
