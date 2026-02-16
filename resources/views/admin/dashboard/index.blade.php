@component('admin.layouts.content')
    @section('title', 'داشبورد مدیریتی')

@section('content')
    <div class="card p-3 shadow-sm mb-4">
        <form method="GET" class="row g-3 align-items-end">

            <div class="col-md-4">
                <label class="form-label">انتخاب فایل</label>
                <select name="file_name" class="form-select">
                    <option value="">-- انتخاب کنید --</option>
                    @foreach($fileNames as $file)
                        <option value="{{ $file }}"
                            {{ $selectedFile == $file ? 'selected' : '' }}>
                            {{ $file }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">انتخاب کد</label>
                <select name="code" class="form-select">
                    <option value="">-- انتخاب کنید --</option>
                    @foreach($codes as $code)
                        <option value="{{ $code }}"
                            {{ $selectedCode == $code ? 'selected' : '' }}>
                            {{ $code }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    نمایش نمودار
                </button>
            </div>

        </form>
    </div>

    @if($selectedFile && $selectedCode)

    <div class="card p-4 shadow-sm">
        <h6 class="mb-3 text-center">
            توزیع درصدی گروه تخصیص
            ({{ $selectedFile }} - {{ $selectedCode }})
        </h6>

        <div style="max-width:500px; margin:auto;">
            <canvas id="takhsisPieChart"></canvas>
        </div>
    </div>

@endif


@endsection

@section('scripts')
    @if($selectedFile && $selectedCode)
<script>
document.addEventListener('DOMContentLoaded', function() {

    const labels = @json($groupLabels);
    const values = @json($groupPercentages);

    const palette = [
        '#2563eb','#16a34a','#dc2626','#ca8a04',
        '#7c3aed','#0ea5e9','#f97316','#14b8a6'
    ];

    const ctx = document.getElementById('takhsisPieChart').getContext('2d');

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: palette.slice(0, values.length),
                hoverOffset: 6
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' +
                                   context.parsed + '٪';
                        }
                    }
                }
            }
        }
    });

});
</script>
@endif

@endsection
@endcomponent
