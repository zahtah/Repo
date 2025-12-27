@component('admin.layouts.content')
    @section('title', 'داشبورد مدیریتی')

@section('content')
    <div class="container mt-4">
        <h4 class="mb-4 text-center">📊 نسبت مصرف به مصوب بر اساس شهرستان</h4>

        <div class="card p-4 shadow-sm mb-4">
            <div style="min-height:320px;">
                <canvas id="consumptionChart" style="width:100%;"></canvas>
            </div>
        </div>

        <!-- ردیف دو نمودار دایره ای کنار هم -->
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card p-3 shadow-sm">
                    <h6 class="mb-3 text-end">تخصیص بر حسب گروه (مجموع t_mosavvab)</h6>
                    <canvas id="groupPieChart" style="max-height:320px;"></canvas>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card p-3 shadow-sm">
                    <h6 class="mb-3 text-end">تعداد رکوردها بر حسب نوع درخواست (darkhast)</h6>
                    <canvas id="darkhastPieChart" style="max-height:320px;"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // داده‌ها از سرور (Blade)
            const barLabels = @json($stats->pluck('Shahrestan')->map(fn($s) => (string) $s)->values());
            const barValues = @json($stats->pluck('percent')->map(fn($p) => $p === null ? 0 : (float) $p)->values());

            const groupLabels = @json($groupLabels);
            const groupValues = @json($groupValues);

            const darkLabels = @json($darkLabels);
            const darkValues = @json($darkValues);

            // helper: palette ساده و قابل توسعه
            const palette = [
                '#4dc9f6', '#f67019', '#f53794', '#537bc4', '#acc236', '#166a8f', '#00a950', '#58595b',
                '#8549ba', '#e6194b', '#3cb44b', '#ffe119'
            ];

            // --- بار chart (همان قبلی) ---
            (function renderBar() {
                const canvas = document.getElementById('consumptionChart');
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                if (window._consumptionChartInstance) try {
                    window._consumptionChartInstance.destroy();
                } catch (e) {}
                window._consumptionChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: barLabels,
                        datasets: [{
                            label: 'درصد تحقق (%)',
                            data: barValues,
                            backgroundColor: barValues.map(v => v < 70 ?
                                'rgba(255,99,132,0.7)' : (v <= 100 ?
                                    'rgba(255,205,86,0.7)' : 'rgba(75,192,192,0.7)')),
                            borderColor: barValues.map(() => 'rgba(0,0,0,0.05)'),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                suggestedMax: 120,
                                title: {
                                    display: true,
                                    text: 'درصد تحقق (%)'
                                }
                            },
                            x: {
                                ticks: {
                                    autoSkip: false,
                                    maxRotation: 60,
                                    minRotation: 45
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            })();

            // --- pie for groups ---
            (function renderGroupPie() {
                const canvas = document.getElementById('groupPieChart');
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                if (window._groupPie) try {
                    window._groupPie.destroy();
                } catch (e) {}
                const bg = groupValues.map((_, i) => palette[i % palette.length]);
                window._groupPie = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: groupLabels,
                        datasets: [{
                            data: groupValues,
                            backgroundColor: bg,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) {
                                        const label = ctx.label || '';
                                        const val = ctx.parsed || 0;
                                        return label + ': ' + val.toLocaleString() + ' (محدود)';
                                    }
                                }
                            }
                        }
                    }
                });
            })();

            // --- pie for darkhast counts ---
            (function renderDarkPie() {
                const canvas = document.getElementById('darkhastPieChart');
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                if (window._darkPie) try {
                    window._darkPie.destroy();
                } catch (e) {}
                const bg = darkValues.map((_, i) => palette[(i + 3) % palette.length]);
                window._darkPie = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: darkLabels,
                        datasets: [{
                            data: darkValues,
                            backgroundColor: bg,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) {
                                        const label = ctx.label || '';
                                        const val = ctx.parsed || 0;
                                        return label + ': ' + val + ' رکورد';
                                    }
                                }
                            }
                        }
                    }
                });
            })();

        });
    </script>
@endsection
@endcomponent
