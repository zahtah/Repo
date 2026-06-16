@component('admin.layouts.content')
@section('content')

<div class="col-lg-12 grid-margin stretch-card">
<div class="card">
<div class="card-body">

{{-- ===================== WELCOME SECTION ===================== --}}
{{-- <div class="mb-6 p-6 rounded-2xl bg-white shadow-md flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">
            سلام {{ auth()->user()->name }} 👋
        </h2>
        <p class="text-gray-500 mt-1">
            امروز {{  \Carbon\Carbon::now()->format('Y/m/d') }}
        </p>
    </div>
    <div>
        <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
            ثبت رکورد جدید
        </a>
    </div>
</div> --}}


{{-- ===================== MAIN KPI CARDS (همان کارت‌های شما) ===================== --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <!-- Card 1 -->
                    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 to-blue-500 p-6 text-white shadow-lg">
                        <div class="absolute right-4 top-4 opacity-20">
                            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M7 2h8l5 5v15H4V2h3z"/>
                            </svg>
                        </div>

                        <p class="text-sm opacity-80">تعداد کل سندها</p>
                        <h3 class="text-4xl font-extrabold mt-3 tracking-tight">
                            {{ number_format($totalDocuments) }}
                        </h3>
                        <p class="text-xs mt-2 opacity-70">بر اساس فایل‌های ثبت‌شده</p>
                    </div>


                    <!-- Card 2 -->
                    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-red-600 to-red-500 p-6 text-white shadow-lg">
                        <div class="absolute right-4 top-4 opacity-20">
                            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 9v2m0 4h.01M5 19h14l-7-14-7 14z"/>
                            </svg>
                        </div>

                        <p class="text-sm opacity-80">رکوردهای تایید نشده</p>
                        <h3 class="text-4xl font-extrabold mt-3 tracking-tight">
                            {{ number_format($draftRecords) }}
                        </h3>
                        <p class="text-xs mt-2 opacity-70">نیازمند بررسی</p>
                    </div>


                    <!-- Card 3 -->
                    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-500 p-6 text-white shadow-lg">
                        <div class="absolute right-4 top-4 opacity-20">
                            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87M12 12a4 4 0 100-8 4 4 0 000 8z"/>
                            </svg>
                        </div>

                        <p class="text-sm opacity-80">کل کاربران سیستم</p>
                        <h3 class="text-4xl font-extrabold mt-3 tracking-tight">
                            {{ number_format($totalUsers) }}
                        </h3>
                        <p class="text-xs mt-2 opacity-70">کاربران ثبت‌شده</p>
                    </div>

                </div>

{{-- ===================== SMALL STATS ===================== --}}
{{-- <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">

    <div class="bg-white p-4 rounded-xl shadow text-center">
        <p class="text-gray-500 text-sm">تخصیص های امروز</p>
        <h4 class="text-xl font-bold mt-2 text-black">{{ $todayDocuments }}</h4>
    </div>

    <div class="bg-white p-4 rounded-xl shadow text-center">
        <p class="text-gray-500 text-sm">تخصیص های این ماه</p>
        <h4 class="text-xl font-bold mt-2 text-black">{{ $monthDocuments }}</h4>
    </div>

    <div class="bg-white p-4 rounded-xl shadow text-center">
        <p class="text-gray-500 text-sm">کاربران فعال</p>
        <h4 class="text-xl font-bold mt-2 text-black" >{{ $activeUsers }}</h4>
    </div>

    <div class="bg-white p-4 rounded-xl shadow text-center">
        <p class="text-gray-500 text-sm">تخصیص های تایید شده</p>
        <h4 class="text-xl font-bold mt-2 text-black">{{ $approvedDocuments }}</h4>
    </div>

</div> --}}


{{-- ===================== CHART ===================== --}}
{{-- <div class="mt-8 bg-white p-6 rounded-2xl shadow-md">
    <h4 class="text-lg font-bold mb-4 text-black">آمار ثبت اسناد در ۳۰ روز اخیر</h4>
    <canvas id="documentsChart"></canvas>
</div> --}}


<div class="mt-8 bg-white p-6 rounded-2xl shadow-md">
    <h4 class="text-lg font-bold mb-4 text-black">آخرین جلسات برگزار شده</h4>

    <div class="overflow-x-auto ">
        <table class="min-w-full table-auto ">
            <thead>
                <tr class="bg-gray-100 relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-500 p-6 text-white shadow-lg">
                    <th class="px-4 py-2 text-center">شماره جلسه</th>
                    <th class="px-4 py-2 text-center">تاریخ تشکیل</th>
                    <th class="px-4 py-2 text-center">تعداد بندها</th>
                </tr>
            </thead>

            <tbody>
                @forelse($sessionStats as $session)
    <tr class="border-b hover:bg-gray-50 bg-gray-100 relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-500 p-6 text-white shadow-lg">
        <td class="px-4 py-2 text-center">
            {{ $session->session_number }}
        </td>

        <td class="px-4 py-2 text-center">
            {{ $session->date}}
        </td>

        <td class="px-4 py-2 text-center">
            {{ number_format($session->items_count) }}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="3" class="text-center py-4">
            جلسه‌ای ثبت نشده است.
        </td>
    </tr>
@endforelse
            </tbody>
        </table>
    </div>
</div>



<div class="mt-8 bg-white p-6 rounded-2xl shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h4 id="allocationChartTitle" class="text-lg font-bold text-black">
            توزیع تخصیص‌ها بر اساس دسته‌بندی اسناد
        </h4>

        <button id="backBtn"
                class=" relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 to-blue-500 p-1 text-white shadow-lg">
            بازگشت
        </button>
    </div>

    <div id="allocationPieChart"></div>
</div>



{{-- ===================== QUICK ACTIONS ===================== --}}
{{-- <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
    <a href="#" class="bg-white p-4 rounded-xl shadow text-center hover:shadow-lg transition">
        ➕ ثبت سند
    </a>
    <a href="#" class="bg-white p-4 rounded-xl shadow text-center hover:shadow-lg transition">
        👤 مدیریت کاربران
    </a>
    <a href="#" class="bg-white p-4 rounded-xl shadow text-center hover:shadow-lg transition">
        📊 گزارشات
    </a>
    <a href="#" class="bg-white p-4 rounded-xl shadow text-center hover:shadow-lg transition">
        ⚙ تنظیمات
    </a>
</div> --}}


</div>
</div>
</div>


{{-- ===================== CHART JS ===================== --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
<script src="{{ asset('admin/assets/vendors/js/chart.js')}}"></script>
<script>
const ctx = document.getElementById('documentsChart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($chartLabels),
        datasets: [{
            label: 'تعداد اسناد',
            data: @json($chartData),
            borderWidth: 2,
            tension: 0.3
        }]
    }
});
</script>
<script src="{{ asset('admin/assets/vendors/js/apexcharts.js')}}"></script>

<script>
let chart = null;
let historyStack = [];

async function loadAllocationChart(categoryId = null, title = null) {

    let url = "{{ route('allocation.chart') }}";

    if (categoryId !== null) {
        url += "/" + categoryId;
    }

    const response = await fetch(url);
    const data = await response.json();

    if (data.length === 0) {
        return;
    }

    const labels = data.map(item => item.name);
    const series = data.map(item => Number(item.value));

    const ids = data.map(item => item.id);
    const isLeaf = data.map(item => item.is_leaf);

    if (chart) {
        chart.destroy();
    }

    const options = {
        chart: {
            type: 'pie',
            height: 450,

            events: {
                dataPointSelection: function(event, chartContext, config) {

                    const index = config.dataPointIndex;

                    if (index === -1) {
                        return;
                    }

                    const id = ids[index];

                    if (id === null) {
                        return;
                    }

                    historyStack.push({
                        id: categoryId,
                        title: document.getElementById('allocationChartTitle').innerText
                    });

                    document.getElementById('backBtn').classList.remove('hidden');

                    loadAllocationChart(id, labels[index]);
                }
            }
        },

        labels: labels,

        series: series,

        legend: {
            position: 'bottom'
        },

        tooltip: {
            y: {
                formatter: function(value) {
                    return value.toLocaleString();
                }
            }
        },

        dataLabels: {
            enabled: true,

            formatter: function(val, opts) {

                const value = opts.w.config.series[opts.seriesIndex];

                return (
                    opts.w.globals.labels[opts.seriesIndex] +
                    "\n" +
                    val.toFixed(1) +
                    "%\n" +
                    value.toLocaleString()
                );
            }
        },

        noData: {
            text: 'داده‌ای یافت نشد'
        }
    };

    chart = new ApexCharts(
        document.querySelector("#allocationPieChart"),
        options
    );

    chart.render();

    if (title) {
        document.getElementById('allocationChartTitle').innerText =
            "توزیع تخصیص‌ها - " + title;
    } else {
        document.getElementById('allocationChartTitle').innerText =
            "توزیع تخصیص‌ها بر اساس دسته‌بندی اسناد";
    }
}

document.getElementById('backBtn').addEventListener('click', function() {

    if (historyStack.length === 0) {
        return;
    }

    const previous = historyStack.pop();

    loadAllocationChart(previous.id, previous.title);

    if (historyStack.length === 0) {
        this.classList.add('hidden');
    }
});

loadAllocationChart();
</script>

@endsection
@endcomponent