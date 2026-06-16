<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>صورت‌جلسه نهایی - جلسه شماره {{ $session_data['session_number'] }}</title>

    <style>
        body {
            font-family: 'B Nazanin', Tahoma, sans-serif;
            margin: 20px;
            line-height: 1.6;
            color: #333;
        }
        

        .container {
            max-width: 900px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h1, h2, h3 {
            color: #003366;
            text-align: center;
            margin-bottom: 20px;
        }

        p {
            margin-bottom: 15px;
            text-align: justify;
        }

        .allocation-details,
        .signatures {
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .allocation-details p,
        .signatures p {
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #e6f2ff;
            color: #003366;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .header-table {
            margin-bottom: 20px;
        }

        .header-table td {
            border: 1px solid #000;
            background: #fff;
        }

        .header-title {
            font-size: 18px;
            margin: 0;
            color: #003366;
            line-height: 1.8;
        }

        .logo-cell {
            width: 15%;
            text-align: center;
        }

        .title-cell {
            width: 55%;
            text-align: center;
        }

        .info-cell {
            width: 30%;
            padding: 0;
        }

        .info-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: right;
        }

        .signature-item {
            margin-bottom: 10px;
            padding: 8px;
            border-bottom: 1px dashed #ddd;
        }

        .signature-item:last-child {
            border-bottom: none;
        }

        .button-container {
            text-align: center;
            margin-top: 30px;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .signatures-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 20px;
        }

        .signature-box {
            border: 1px solid #000;
            padding: 15px;
            min-height: 220px;
            box-sizing: border-box;
            background: #fff;
        }

        .signature-box .signature-area {
            margin-top: 40px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            word-break: break-word;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 6mm;
            }
            .button-container {
                display: none;
            }

            .header-table {
                page-break-inside: avoid;
            }

            .allocation-details,
            .signatures {
                page-break-inside: avoid;
            }
             body {
                margin: 0;
                padding: 0;
                font-size: 10px;
            }

            .container {
                max-width: 100%;
                width: 100%;
                margin: 0;
                padding: 5px;
                border: none;
                box-shadow: none;
            }

            table {
                width: 100%;
                table-layout: fixed;
                font-size: 9px;
            }

            th,
            td {
                font-size: 8px;
                padding: 3px;
                word-wrap: break-word;
                overflow-wrap: break-word;
            }

            .button-container {
                display: none;
            }
            .signatures-grid {
                grid-template-columns: repeat(3, 1fr); /* 6 کاربر = 3 ستون، 2 ردیف */
                gap: 4px;
                margin-top: 10px;
            }

            .signature-box {
                min-height: 100px;
                padding: 6px;
                font-size: 9px;
            }

            .signature-area {
                margin-top: 15px !important;
            }

            .signature-area br {
                display: none;
            }
            .button-container {
                display: none;
            }
             th:nth-child(1),
            td:nth-child(1) {
                width: 25px;
            }

            th:nth-child(2),
            td:nth-child(2) {
                width: 90px;
            }

            th:nth-child(10),
            td:nth-child(10) {
                width: 80px;
            }

            th:nth-child(11),
            td:nth-child(11),
            th:nth-child(12),
            td:nth-child(12),
            th:nth-child(13),
            td:nth-child(13),
            th:nth-child(14),
            td:nth-child(14) {
                width: 55px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <!-- هدر صورتجلسه -->
    <table class="header-table">
        <tr>

            <!-- لوگو -->
            <td class="logo-cell">
                {{-- <img src="https://example.com/logo.png"
                     alt="لوگو"
                     style="max-width:80px; max-height:80px;"> --}}
                <img src="{{ asset('assets/images/WaterLogo.png') }}"
                    alt="logo" style="max-width:80px; max-height:80px;" />
            </td>

            <!-- عنوان -->
            <td class="title-cell">
                <h1 class="header-title">
                    صورت‌جلسه شماره {{ $session_data['session_number'] }}
                    کمیته مدیریت منابع آب شرکت
                    در سال
                    {{ (int) explode('/', $session_data['date'])[0] }}
                </h1>
            </td>

            <!-- اطلاعات سند -->
            <td class="info-cell">
                <table class="info-table">
                    <tr>
                        <td><strong>شماره سند</strong></td>
                        <td>{{ $session_data['session_number'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>تاریخ جلسه</strong></td>
                        <td>{{ $session_data['date'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>ساعت</strong></td>
                        <td>
                            {{ \Carbon\Carbon::parse($session_data['time'])->format('H:i') }}
                        </td>
                    </tr>
                </table>
            </td>

        </tr>

        <tr>
            <td colspan="3" style="text-align:right;">
                <strong>شماره جلسه:</strong>
                {{ $session_data['session_number'] }}
            </td>
        </tr>

        <tr>
            <td colspan="3" style="text-align:right;">
                <strong>دستور کار جلسه:</strong>
                {{ $session_data['title'] }}
            </td>
        </tr>
    </table>

    @if(!empty($session_data['description']))
        <p>
            <strong>توضیحات:</strong>
            {{ $session_data['description'] }}
        </p>
    @endif

    <!-- جدول تخصیص‌ها -->
    <div class="allocation-details">
        <h2>تخصیص‌های نهایی شده</h2>

        @if(!empty($session_data['allocations']))
            <table>
                <thead>
                <tr>
                    <th>بند </th>
                    <th>نام واحد / متقاضی</th>
                    <th>کلاسه پرونده</th>
                    <th>شهرستان</th>
                    <th>محدوده مطالعاتی</th>
                    <th>عنوان طرح</th>
                    <th>نوع مصرف</th>
                    <th>نوع منبع تامین آب</th>
                    <th>محل تخصیص</th>
                    <th>شماره و تاریخ ابلاغیه تخصیص</th>
                    <th>حجم آب تخصیص ابلاغی</th>
                    <th>حجم تخصیص داده شده تا کنون</th>
                    <th>میزان آب مصوب</th>
                    <th>میزان آب باقی مانده در بانک تخصیص</th>
                </tr>
                </thead>

                <tbody>
                    @php
                        $allocationNumbers = [];

                        foreach($session_data['allocations'] as $index => $allocation){
                            $allocationNumbers[$allocation['id']] = $index + 1;
                        }
                    @endphp
                @foreach($session_data['allocations'] as $allocation)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $allocation['motaghasi'] }}</td>
                        <td>{{ $allocation['kelace'] }}</td>
                        <td>{{ $allocation['Shahrestan'] }}</td>
                        <td>{{ $allocation['mahdoude'] }}</td>
                        <td>{{ $allocation['masraf'] }}</td>
                        <td>{{ $allocation['Takhsis_group'] }}</td>
                        <td>{{ $allocation['parentCategory'] }}</td>
                        <td>{{ $allocation['file_name'] }}</td>
                        <td>
                            {{ $allocation['shomare'] }}
                            <br>
                            {{ $allocation['eblaghDate'] }}
                        </td>
                        <td>{{ $allocation['t_mosavvab'] }}</td>
                        <td>{{ $allocation['sum'] }}</td>
                        <td>{{ $allocation['V_m'] ?? '-' }}</td>
                        <td>{{ $allocation['baghi'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <p>هیچ تخصیص نهایی شده‌ای یافت نشد.</p>
        @endif
    </div>

    <!-- امضاها -->
    <div class="signatures">
        {{-- <h2>امضا اعضای جلسه</h2> --}}

        <div class="signatures-grid">
            @foreach($usersReport as $item)
                <div class="signature-box">

                    <p>
                        <strong>
                            {{ $item['user']->address }}
                            :
                            {{ $item['user']->name }}
                        </strong>
                    </p>

                    <p>
                        <strong>موافق با:</strong>

                        @forelse($item['approved_allocations'] as $allocation)
                            {{-- #{{ $allocation['id'] }}
                            ({{ $allocation['title'] }}) --}}
                             بند {{ $allocationNumbers[$allocation['id']] ?? '-' }}
                            ،
                        @empty
                            هیچ تخصیصی
                        @endforelse
                    </p>

                    <div class="signature-area">
                        <p>امضاء</p>
                    </div>

                </div>
            @endforeach
        </div>
    </div>

    <div class="button-container">
        <button onclick="window.print();">
            چاپ این صفحه
        </button>
    </div>

</div>

</body>
</html>