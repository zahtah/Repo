@component('admin.layouts.content')
@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                {{-- <div class="d-flex justify-content-between">
                    <h4 class="card-title">خانه</h4>
                     <a class="nav-link btn btn-success create-new-button" href="{{ route('createCategories') }}">+ Create New
                        Category</a> 
                </div> --}}
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

                
            </div>
        </div>
    </div>
    @endsection
@endcomponent
