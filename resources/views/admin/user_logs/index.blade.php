@component('admin.layouts.content')
    @section('title', 'لاگ ورود و خروج ')

@section('content')
<div class="p-6">
    <div class="bg-white shadow-lg rounded-xl overflow-hidden">

        <div class="p-4 border-b flex justify-between items-center">
            <h2 class="text-lg font-bold">گزارش ورود و خروج کاربران</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-right">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">کاربر</th>
                        <th class="px-6 py-3">نوع عملیات</th>
                        <th class="px-6 py-3">تاریخ و ساعت</th>
                        <th class="px-6 py-3">IP</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @foreach($logs as $log)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-500">
                                {{ $log->user->name }}
                            </td>

                            <td class="px-6 py-4 text-gray-500">
                                @if($log->type === 'login')
                                    <span class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">
                                        ورود
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">
                                        خروج
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ \Morilog\Jalali\Jalalian::fromDateTime($log->logged_at)->format('Y/m/d H:i') }}
                            </td>

                            <td class="px-6 py-4 text-gray-500">
                                {{ $log->ip_address }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4">
            {{ $logs->links() }}
        </div>

    </div>
</div>
@endsection
@endcomponent