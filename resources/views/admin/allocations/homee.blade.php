@component('admin.layouts.content')
@section('content')

<style>

body{
    background:#f5f7fb;
}

.dashboard-header{
    background:#fff;
    border-radius:24px;
    padding:30px;
    box-shadow:0 5px 25px rgba(0,0,0,.05);
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.dashboard-title{
    font-size:28px;
    font-weight:700;
    color:#0f172a;
}

.dashboard-subtitle{
    color:#64748b;
    margin-top:8px;
}

.kpi-card{
    background:#ffffff;
    border-radius:20px;
    padding:24px;
    box-shadow:0 5px 20px rgba(0,0,0,.05);
    display:flex;
    align-items:center;
    gap:18px;
    height:100%;
    transition:.3s;
}

.kpi-card:hover{
    transform:translateY(-5px);
}

.kpi-icon{
    width:65px;
    height:65px;
    border-radius:18px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:28px;
}

.kpi-blue{
    background:#e0ecff;
    color:#2563eb;
}

.kpi-red{
    background:#fee2e2;
    color:#dc2626;
}

.kpi-green{
    background:#dcfce7;
    color:#16a34a;
}

.kpi-orange{
    background:#ffedd5;
    color:#ea580c;
}

.kpi-title{
    font-size:14px;
    color:#64748b;
}

.kpi-value{
    font-size:32px;
    font-weight:700;
    color:#0f172a;
}

.analytics-card{
    background:#fff;
    border-radius:24px;
    padding:24px;
    box-shadow:0 5px 20px rgba(0,0,0,.05);
    height:100%;
}

.card-title-custom{
    font-size:18px;
    font-weight:700;
    color:#0f172a;
    margin-bottom:20px;
}

.system-status{
    background:linear-gradient(135deg,#0f172a,#1e293b);
    color:#fff;
    border-radius:24px;
    padding:25px;
    box-shadow:0 10px 25px rgba(15,23,42,.15);
    height:100%;
}

.status-row{
    display:flex;
    justify-content:space-between;
    padding:12px 0;
    border-bottom:1px solid rgba(255,255,255,.1);
}

.status-row:last-child{
    border:none;
}

.session-table thead{
    background:#0f172a;
    color:#fff;
}

.session-table{
    border-radius:12px;
    overflow:hidden;
}

.session-table tbody tr:hover{
    background:#f8fafc;
}

.action-btn{
    background:#2563eb;
    color:#fff;
    padding:10px 20px;
    border-radius:12px;
    text-decoration:none;
    transition:.3s;
}

.action-btn:hover{
    color:#fff;
    background:#1d4ed8;
}

</style>

<div class="col-lg-12 grid-margin stretch-card">
<div class="card">
<div class="card-body">

<div class="card-body">

    <!-- HEADER -->

    <div class="dashboard-header">

        <div>
            <div class="dashboard-title">
                سامانه مدیریت تخصیص منابع آب
            </div>

            <div class="dashboard-subtitle">
                نمای مدیریتی وضعیت اسناد، جلسات و تخصیص‌های ثبت شده
            </div>
        </div>

        <div class="text-end">

            <div class="mb-3">
                <span class="badge bg-success p-2">
                    سیستم فعال
                </span>
            </div>

            <div class="text-muted">
                {{ \Morilog\Jalali\Jalalian::now()->format('Y/m/d') }}
            </div>

        </div>

    </div>


    <!-- KPI -->

    <div class="row g-4">

        <div class="col-lg-3">

            <div class="kpi-card">

                <div class="kpi-icon kpi-blue">
                    📄
                </div>

                <div>
                    <div class="kpi-title">
                        کل اسناد
                    </div>

                    <div class="kpi-value">
                        {{ number_format($totalDocuments) }}
                    </div>
                </div>

            </div>

        </div>

        <div class="col-lg-3">

            <div class="kpi-card">

                <div class="kpi-icon kpi-red">
                    ⚠️
                </div>

                <div>
                    <div class="kpi-title">
                        تایید نشده
                    </div>

                    <div class="kpi-value">
                        {{ number_format($draftRecords) }}
                    </div>
                </div>

            </div>

        </div>

        <div class="col-lg-3">

            <div class="kpi-card">

                <div class="kpi-icon kpi-green">
                    👥
                </div>

                <div>
                    <div class="kpi-title">
                        کاربران
                    </div>

                    <div class="kpi-value">
                        {{ number_format($totalUsers) }}
                    </div>
                </div>

            </div>

        </div>

        <div class="col-lg-3">

            <div class="system-status">

                <h5 class="mb-4">
                    وضعیت سیستم
                </h5>

                <div class="status-row">
                    <span>اسناد تایید شده</span>
                    <strong>{{ $approvedDocuments }}</strong>
                </div>

                <div class="status-row">
                    <span>جلسات برگزار شده</span>
                    <strong>{{ count($sessionStats) }}</strong>
                </div>

                <div class="status-row">
                    <span>کاربران فعال</span>
                    <strong>{{ $activeUsers }}</strong>
                </div>

            </div>

        </div>

    </div>


    <!-- CHART + TABLE -->

    <div class="row mt-4">

        <div class="col-lg-7">

            <div class="analytics-card">

                <div class="d-flex justify-content-between align-items-center">

                    <h5 id="allocationChartTitle" class="card-title-custom">
                        توزیع تخصیص‌ها بر اساس دسته‌بندی اسناد
                    </h5>

                    <button id="backBtn" class="btn btn-primary btn-sm">
                        بازگشت
                    </button>

                </div>

                <div id="allocationPieChart"></div>

            </div>

        </div>

        <div class="col-lg-5">

            <div class="analytics-card">

                <h5 class="card-title-custom">
                    آخرین جلسات برگزار شده
                </h5>

                <div class="table-responsive">

                    <table class="table session-table">

                        <thead>

                        <tr>
                            <th class="text-center">
                                شماره جلسه
                            </th>

                            <th class="text-center">
                                تاریخ
                            </th>

                            <th class="text-center">
                                تعداد بندها
                            </th>
                        </tr>

                        </thead>

                        <tbody>

                        @forelse($sessionStats as $session)

                            <tr>

                                <td class="text-center">
                                    {{ $session->session_number }}
                                </td>

                                <td class="text-center">
                                    {{ $session->date }}
                                </td>

                                <td class="text-center">
                                    {{ number_format($session->items_count) }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="3" class="text-center">
                                    جلسه‌ای ثبت نشده است
                                </td>
                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

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