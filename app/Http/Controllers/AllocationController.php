<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AllocationsImport;
use App\Models\Allocation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;
use App\Exports\AllocationsExport;
use App\Models\AllocationVote;
use App\Models\FileCategory;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Document;
use App\Models\Session;
use Illuminate\Auth\Events\Validated;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class AllocationController extends Controller
{
    public function homee(){
	$fileNames = Allocation::select('file_name')->distinct()->pluck('file_name');
        // 1️⃣ تعداد کل سندها (file_name یکتا)
        $totalDocuments = Allocation::distinct('file_name')->count('file_name');
        $totalRecords = Allocation::count();
    $draftRecords = Allocation::where('status','draft')->count();
    $approvedDocuments = Allocation::where('status','approved')->count();
    $totalUsers = User::count();

    $todayDocuments = Allocation::whereDate('created_at', today())->count();
    $monthDocuments = Allocation::whereMonth('created_at', now()->month)->count();
    $activeUsers = User::where('is_staff',1)->count(); // اگر فیلد active داری

    // $latestDocuments = Allocation::with('user')
    //     ->latest()
    //     ->take(5)
    //     ->get();

    // chart
    $chartLabels = [];
    $chartData = [];

    for ($i=29; $i>=0; $i--) {
        $date = Carbon::today()->subDays($i);
        $chartLabels[] = $date->format('m/d');
        $chartData[] = Allocation::whereDate('created_at',$date)->count();
    }

    $sessionStats = Session::select(
        'sessions.session_number',
        'sessions.date',
        DB::raw('COUNT(allocations.id) as items_count')
    )
    ->leftJoin(
        'allocations',
        'allocations.session',
        '=',
        'sessions.session_number'
    )
    ->groupBy(
        'sessions.id',
        'sessions.session_number',
        'sessions.date'
    )
    ->orderByDesc('sessions.date')
    ->take(10)
    ->get();

        return view('admin.allocations.homee',compact(
            'totalDocuments',
        'draftRecords',
	    'totalRecords',
        'approvedDocuments',
        'totalUsers',
        'todayDocuments',
        'monthDocuments',
        'activeUsers',
        'chartLabels',
        'chartData',
        'sessionStats'
            ));
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        Excel::import(new AllocationsImport($originalName), $request->file('file'));

        return back()->with('success', 'اطلاعات با موفقیت وارد شد.'.$originalName);
    }

public function index(Request $request)
    {
        $query = Allocation::query();

    // فیلتر بر اساس file_name از پارامتر query
        $fileFilter = $request->query('file_name');
        if ($fileFilter) {
            $query->whereIn('file_name', $fileFilter);
        }

        if ($request->filled('code')) {
        $query->whereIn('code', $request->code);
        }

        if ($request->filled('takhsis_group')) {
            $query->whereIn('Takhsis_group', $request->takhsis_group);
        }

        // فیلتر متناظر با فیلدهای فرم
        if ($request->filled('Shahrestan')) {
            $query->whereIn('Shahrestan', $request->get('Shahrestan'));
        }

        if ($request->filled('masraf')) {
            $query->whereIn('masraf', $request->get('masraf'));
        }
         if ($request->filled('session')) {
            $query->whereIn('session', $request->get('session'));
        }

        // بازه تاریخ برای ستون erja (فرمت ورودی باید YYYY-MM-DD از فرم date باشد)
        // دریافت ورودی خام از درخواست (ممکن است شمسی یا میلادی باشد)
        $rawFrom = $request->get('from');
        $rawTo   = $request->get('to');

        $from = $this->parseToGregorianDate($rawFrom);
        $to   = $this->parseToGregorianDate($rawTo);

        // اگر هر دو تاریخ معتبر باشند از whereBetween استفاده کن (توجه: ترتیب از -> تا)
        if ($from && $to) {
            // اطمینان از ترتیب درست (اگر کاربر اشتباها معکوس زده باشد)
            if ($from <= $to) {
                $query->whereBetween('erja', [$from, $to]);
            } else {
                $query->whereBetween('erja', [$to, $from]);
            }
        } elseif ($from) {
            $query->where('erja', '>=', $from);
        } elseif ($to) {
            $query->where('erja', '<=', $to);
        }


        // جستجوی عمومی (ردیف، کلاسه، متقاضی و... )
        if ($request->filled('q')) {
            $q = $request->get('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('row', 'like', "%{$q}%")
                    ->orWhere('kelace', 'like', "%{$q}%")
                    ->orWhere('motaghasi', 'like', "%{$q}%")
                    ->orWhere('Shahrestan', 'like', "%{$q}%");
            });
        }

        // کلاسه (متنی)
        if ($request->filled('kelace')) {
            $query->where('kelace', 'like', '%' . $request->kelace . '%');
        }

        // مرتب‌سازی دلخواه (مثال: ?sort=sal&direction=asc)
        $allowedSorts = ['id','row','Shahrestan','sal','erja','kelace','q_m','V_m','sum','baghi'];
        $sort = $request->get('sort', 'id');
        $direction = strtolower($request->get('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        if (! in_array($sort, $allowedSorts)) {
            $sort = 'id';
        }
        $query->orderBy($sort, $direction);

        // صفحه‌بندی: پارامتر per_page قابل تنظیم است
        $perPage = (int) $request->get('per_page', 10);
        if ($perPage <= 0 || $perPage > 500) $perPage = 10;

        $cumulativeSql = DB::raw("(
        SELECT ROUND(COALESCE(SUM(t2.V_m),0), 2)
        FROM allocations t2
        WHERE COALESCE(t2.code, '') = COALESCE(allocations.code, '')
          AND COALESCE(t2.Takhsis_group, '') = COALESCE(allocations.Takhsis_group, '')
          AND COALESCE(t2.file_name, '__NO_FILE__') = COALESCE(allocations.file_name, '__NO_FILE__')
          AND t2.id <= allocations.id
        ) as cumulative_vm");

        // این select اصلی: همه ستون‌ها + ستون محاسبه‌شده
        $rowsQuery = $query->select('allocations.*', $cumulativeSql)
        ->orderBy($sort, $direction);


        $allocations = $query->paginate($perPage)->appends($request->query());

        $takhsis = $request->get('Takhsis_group');
        $code = $request->get('code');
        $fileFilter = $request->get('file_name');
        $session = $request->get('session');

        // Base query for rows (with optional filters)
        $base = Allocation::query();

        if ($takhsis && $takhsis !== 'all') {
            $base->where('Takhsis_group', $takhsis);
        }

        if ($code && $code !== 'all') {
            $base->where('code', $code);
        }

        if ($fileFilter && $fileFilter !== 'all') {
            $base->where('file_name', $fileFilter);
        }
        // زیرکوئری: محاسبه مجموع `sum` برای هر file_name (با مدیریت NULL)
    $fileSums = DB::table('allocations')
        ->select(DB::raw("IFNULL(file_name, '__NO_FILE__') as fn"), DB::raw('ROUND(SUM(`sum`), 2) as file_sum'))
        ->groupBy(DB::raw("IFNULL(file_name, '__NO_FILE__')"));

    // الحاق زیرکوئری به ردیف‌ها تا هر ردیف یک ستون file_sum داشته باشد
    $rowsQuery = $base->select('allocations.*')
        ->leftJoinSub($fileSums, 'fs', DB::raw("IFNULL(allocations.file_name, '__NO_FILE__')"), '=', 'fs.fn')
        ->addSelect('fs.file_sum as file_sum');
    $perFileRows = Allocation::query()
    ->selectRaw('file_name, code, Takhsis_group,
                 COUNT(*) as applicants_count,
                 SUM(t_mosavvab) as total_volume,
                 SUM(V_m) as cost,
                 (SUM(t_mosavvab) - SUM(V_m)) as remaining')
    ->groupBy('file_name', 'code', 'Takhsis_group')
    ->get();

    // pagination و اجرای نهایی
    $rows = $rowsQuery->orderBy('created_at','desc')->paginate(25)->withQueryString();

        // داده‌های کمکی برای فیلترها (مثلا dropdown)
        $shahrestans = Allocation::select('Shahrestan')
            ->whereNotNull('Shahrestan')
            ->distinct()
            ->orderBy('Shahrestan')
            ->pluck('Shahrestan');

        $masrafs = Allocation::select('masraf')
            ->whereNotNull('masraf')
            ->distinct()
            ->orderBy('masraf')
            ->pluck('masraf');
        $fileNames = Allocation::select('file_name')->distinct()->pluck('file_name');
        $codes = Allocation::select('code')->distinct()->pluck('code');
        $takhsisGroups = Allocation::select('Takhsis_group')->distinct()->pluck('Takhsis_group');
        return view('admin.allocations.index', compact('rows','allocations', 'shahrestans','takhsis','code', 'masrafs','session','fileNames','fileFilter','codes',
        'takhsisGroups','perFileRows'));
    }

    public function create( Request $request,Session $session1)
    {
        $sessionNumber = $request->session_number; // یا $request->session_number
        //$session1 = $request->session1;
        $sessionid=$session1->id;
    // گرفتن بیشترین مقدار عددی ستون row (در صورتی که row به صورت string ذخیره شده)
    $maxRow = DB::table('allocations')
        ->select(DB::raw('MAX(CAST(`row` AS UNSIGNED)) as max_row'))
        ->value('max_row');

    $nextRow = $maxRow ? ((int)$maxRow + 1) : 1;

    // گزینه‌های شهرستان موجود در دیتابیس
    $shahrOptions = Allocation::query()
        ->select('Shahrestan')
        ->distinct()
        ->whereNotNull('Shahrestan')
        ->pluck('Shahrestan');

    // گزینه‌های نوع درخواست موجود در دیتابیس
    $darkhastOptions = Allocation::query()
        ->select('darkhast')
        ->distinct()
        ->whereNotNull('darkhast')
        ->pluck('darkhast');

    // گزینه‌های تخصیص موجود در دیتابیس
    $takhsisOptions = Allocation::query()
        ->select('Takhsis_group')
        ->distinct()
        ->whereNotNull('Takhsis_group')
        ->pluck('Takhsis_group');

    // گزینه‌های منطقه موجود در دیتابیس
    $mantagheOptions = Allocation::query()
        ->select('mantaghe')
        ->distinct()
        ->whereNotNull('mantaghe')
        ->pluck('mantaghe');

        // گزینه‌های کد موجود در دیتابیس
    $codeOptions = Allocation::query()
        ->select('code')
        ->distinct()
        ->whereNotNull('code')
        ->pluck('code');

    // گزینه‌های واحد دبی موجود در دیتابیس
    $vahedOptions = Allocation::query()
        ->select('vahed')
        ->distinct()
        ->whereNotNull('vahed')
        ->pluck('vahed');

    $fileOptions = FileCategory::whereDoesntHave('children')
        ->orderBy('name')
        ->get();
    $users = $session1->users()->orderBy('name')->get();

    return view('admin.allocations.create', compact('nextRow','darkhastOptions', 'takhsisOptions', 'shahrOptions','mantagheOptions','vahedOptions','fileOptions','codeOptions','sessionNumber','sessionid','session1','users'));
}

public function nextRow(Request $request)
    {
        $fileCategoryId = $request->file_category_id;

        $nextRow = Allocation::where('file_category_id', $fileCategoryId)
        ->max('row');

        $nextRow = $nextRow ? $nextRow + 1 : 1;


        return response()->json(['nextRow' => $nextRow]);
    }

    // ---------------------- computeTMosavvab ----------------------
    // AllocationController.php
    public function computeTMosavvab(Request $request)
    {
        $fileCategoryId = $request->file_category_id;

        //Allocation::where('file_category_id', $fileCategoryId)


        $fileName = FileCategory::findOrFail($fileCategoryId)->name;

        $code = $request->input('code');
        $takhsis = $request->input('Takhsis_group');

        $query = Allocation::query();

        $query->where('file_category_id', $request->file_category_id);

        if ($request->code) {
            $query->where('code', $request->code);
        }

        if ($request->Takhsis_group) {
            $query->where('Takhsis_group', $request->Takhsis_group);
        }

        $t_mosavvab = $query->max('t_mosavvab');

        return response()->json([
            't_mosavvab' => $t_mosavvab ?? 0
        ]);

    }


    // ---------------------- computeSum ----------------------
    public function computeSum(Request $request)
    {
        $fileCategoryId = $request->file_category_id;

        //Allocation::where('file_category_id', $fileCategoryId)


        $fileName = FileCategory::findOrFail($fileCategoryId)->name;
        //$fileName = $request->input('file_name');
        $code = $request->input('code');
        $takhsis = $request->input('Takhsis_group');
        $V_m = floatval($request->input('V_m', 0));

        // مثال ساده: جمع تمام V_mهای موجود با همان file_name + code + Takhsis_group
        $sum = Allocation::where('file_category_id', $request->file_category_id)
        ->when($request->code, fn($q) => $q->where('code', $request->code))
        ->when($request->Takhsis_group, fn($q) => $q->where('Takhsis_group', $request->Takhsis_group))
        ->sum('V_m');

        // جمع فعلی + مقدار ورودی جدید
        $sum += $V_m;

        return response()->json(['sum' => $sum]);
    }

    public function computeEditSum(Request $request)
    {
        $fileCategoryId = $request->file_category_id;

        //Allocation::where('file_category_id', $fileCategoryId)


        $fileName = FileCategory::findOrFail($fileCategoryId)->name;
        $id = $request->input('id');
         $allocation = Allocation::findOrFail($id);
        $fileName = $request->input('file_name');
        $code = $request->input('code');
        $takhsis = $request->input('Takhsis_group');
        $V_m = floatval($request->input('V_m', 0));


        $currentRow = is_numeric($allocation->row) ? $allocation->row : intval($allocation->row);
        $sumBefore = Allocation::when($fileName, fn($q) => $q->where('file_name', $fileName))
                           ->when($code, fn($q) => $q->where('code', $code))
                           ->when($takhsis, fn($q) => $q->where('Takhsis_group', $takhsis))
                           ->where('row', '<', $currentRow)
                           ->sum('V_m');

        //$sum = floatval($allocation->sum);

        $sum = $sumBefore + $V_m;
        
        return response()->json(['sum' => $sum]);
    }

    



public function store(Request $request)
{
    $this->authorize('create', Allocation::class);

    $validated = $request->validate([
        'row' => 'nullable',
        'Shahrestan' => 'nullable|string|max:255',
        'sal' => 'nullable|integer',
        'erja' => 'nullable|date',
        'code' => 'nullable|integer',
        'mantaghe' => 'nullable|string|max:255',
        'Abadi' => 'nullable|string|max:255',
        'kelace' => 'nullable|string|max:255',
        'motaghasi' => 'nullable|string|max:255',
        'darkhast' => 'nullable|string|max:255',
        'Takhsis_group' => 'nullable|string|max:255',
        'masraf' => 'nullable|string|max:255',
        'comete' => 'nullable|date',
        'shomare' => 'nullable|string|max:255',
        'date_shimare' => 'nullable|date',
        'vahed' => 'nullable|string|max:255',
        'q_m' => 'nullable|integer',
        'V_m' => 'nullable',
        't_mosavvab' => 'nullable',
        'mosavabat' => 'nullable|string|max:255',
        'file_name' => 'nullable|string|max:255',
        'file_category_id' => 'required|exists:file_categories,id',
        'minutes' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
        'session_number' => 'nullable|string|max:255',
    ]);

    // تبدیل تاریخ‌ها
    $dateFields = ['erja', 'comete', 'date_shimare'];
    foreach ($dateFields as $field) {
        if (!empty($validated[$field])) {
            $val = str_replace(
                ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'],
                ['0','1','2','3','4','5','6','7','8','9'],
                $validated[$field]
            );

            try {
                $validated[$field] = Jalalian::fromFormat('Y/m/d', $val)
                    ->toCarbon()
                    ->format('Y-m-d');
            } catch (\Throwable $e) {
                $validated[$field] = Carbon::parse($val)->format('Y-m-d');
            }
        }
    }

    // نرمال‌سازی عدد
    $normalizeNumber = function ($v) {
        if ($v === null || $v === '') return null;
        $v = str_replace(['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹','٫','،',','],
                         ['0','1','2','3','4','5','6','7','8','9','.','.',''], $v);
        $v = preg_replace('/[^\d\.\-]/', '', $v);
        return is_numeric($v) ? (float)$v : null;
    };

    $currentVm = $normalizeNumber($validated['V_m'] ?? 0);
    $t_mosavvab = $normalizeNumber($validated['t_mosavvab'] ?? 0);

    $code = $validated['code'] ?? null;
    $takhsis = $validated['Takhsis_group'] ?? null;

    $sessionid = $request->sessionid;
    $session1 = Session::findOrFail($sessionid);

    try {

        $fileCategoryId = $validated['file_category_id'];
        $fileName = FileCategory::find($fileCategoryId)?->name;
        $session = $validated['session_number'];

        // upload file
        $minutesPath = null;
        if ($request->hasFile('minutes')) {
            $file = $request->file('minutes');
            $filename = time().'_'.$file->getClientOriginalName();
            $minutesPath = $file->storeAs('minutes', $filename, 'public');
        }

        $allocation = DB::transaction(function () use (
            $validated,
            $fileName,
            $currentVm,
            $t_mosavvab,
            $code,
            $takhsis,
            $minutesPath,
            $session,
            $request,
            $fileCategoryId
        ) {

            // 🔹 next row
            $maxRowQuery = Allocation::query();
            $fileName
                ? $maxRowQuery->where('file_name', $fileName)
                : $maxRowQuery->whereNull('file_name');

            $maxRow = $maxRowQuery
                ->select(DB::raw('MAX(CAST(`row` AS UNSIGNED)) as max_row'))
                ->lockForUpdate()
                ->value('max_row');

            $nextRow = $maxRow ? ((int)$maxRow + 1) : 1;

            // 🔹 sum
            $sumQuery = Allocation::query();

            $code
                ? $sumQuery->where('code', $code)
                : $sumQuery->whereNull('code');

            $takhsis
                ? $sumQuery->where('Takhsis_group', $takhsis)
                : $sumQuery->whereNull('Takhsis_group');

            $fileName
                ? $sumQuery->where('file_name', $fileName)
                : $sumQuery->whereNull('file_name');

            $otherSum = (float) $sumQuery->lockForUpdate()->sum('V_m');

            $finalSum = round($otherSum + $currentVm, 3);
            $baghi = round($t_mosavvab - $finalSum, 3);

            // 🔹 create data
            $toCreate = $validated;
            $toCreate['minutes'] = $minutesPath;
            $toCreate['row'] = $nextRow;
            $toCreate['file_name'] = $fileName;
            $toCreate['file_category_id'] = $fileCategoryId;
            $toCreate['V_m'] = $currentVm;
            $toCreate['t_mosavvab'] = $t_mosavvab;
            $toCreate['sum'] = $finalSum;
            $toCreate['baghi'] = $baghi;
            $toCreate['session'] = $session;
            $toCreate['status'] = 'draft';
            $toCreate['created_by'] = auth()->id();

            // 🔥 فقط یک بار create
            $allocation = Allocation::create($toCreate);

            // 🔹 votes (فقط همینجا)
            if ($request->filled('votes')) {
                foreach ($request->votes as $userId => $value) {
                    if ($value == 1) {
                        AllocationVote::create([
                            'allocation_id' => $allocation->id,
                            'user_id' => $userId,
                            'vote' => 1,
                            'comment' => null,
                        ]);
                    }
                }
            }

            return $allocation;
        });

    } catch (\Throwable $e) {
        return back()->withInput()->withErrors([
            'general' => 'خطا هنگام ذخیره رکورد'
        ]);
    }

    return redirect()
        ->route('sessions.show', $session1)
        ->with('success', 'رکورد با موفقیت ایجاد شد.');
}


public function edit($id, Session $session)
{
$allocation = Allocation::with('votes')->findOrFail($id);
$sessionid = $session->id;
$sessionNumber = $session->session_number;

$users = $session->users()
    ->orderBy('name')
    ->get();

$selectedUsers = $allocation->votes
    ->pluck('user_id')
    ->toArray();

$voteComments = $allocation->votes
    ->keyBy('user_id');

$shahrOptions = Allocation::query()
    ->select('Shahrestan')
    ->distinct()
    ->whereNotNull('Shahrestan')
    ->pluck('Shahrestan');

$darkhastOptions = Allocation::query()
    ->select('darkhast')
    ->distinct()
    ->whereNotNull('darkhast')
    ->pluck('darkhast');

$takhsisOptions = Allocation::query()
    ->select('Takhsis_group')
    ->distinct()
    ->whereNotNull('Takhsis_group')
    ->pluck('Takhsis_group');

$mantagheOptions = Allocation::query()
    ->select('mantaghe')
    ->distinct()
    ->whereNotNull('mantaghe')
    ->pluck('mantaghe');

$codeOptions = Allocation::query()
    ->select('code')
    ->distinct()
    ->whereNotNull('code')
    ->pluck('code');

$vahedOptions = Allocation::query()
    ->select('vahed')
    ->distinct()
    ->whereNotNull('vahed')
    ->pluck('vahed');

$fileOptions = FileCategory::whereDoesntHave('children')
    ->orderBy('name')
    ->get();

return view('admin.allocations.edit', compact(
    'allocation',
    'session',
    'sessionid',
    'sessionNumber',
    'shahrOptions',
    'darkhastOptions',
    'takhsisOptions',
    'mantagheOptions',
    'codeOptions',
    'vahedOptions',
    'fileOptions',
    'users',
    'selectedUsers',
    'voteComments'
));

}


public function show(Request $request, $id)
{
    try {
        // تست ساده: فقط id را برگردان
        return response()->json(['ok' => true, 'id' => $id]);
    } catch (\Throwable $e) {
        Log::error('show simple test error: '.$e->getMessage());
        return response()->json(['error'=>'server'], 500);
    }
}

public function update(Request $request, $id, Session $session)
{
$allocation = Allocation::findOrFail($id);


$validated = $request->validate([
    'row' => 'nullable',
    'Shahrestan' => 'nullable|string|max:255',
    'sal' => 'nullable|integer',
    'erja' => 'nullable',
    'code' => 'nullable|integer',
    'mantaghe' => 'nullable|string|max:255',
    'Abadi' => 'nullable|string|max:255',
    'kelace' => 'nullable|string|max:255',
    'motaghasi' => 'nullable|string|max:255',
    'darkhast' => 'nullable|string|max:255',
    'Takhsis_group' => 'nullable|string|max:255',
    'masraf' => 'nullable|string|max:255',
    'comete' => 'nullable',
    'shomare' => 'nullable|string|max:255',
    'date_shimare' => 'nullable',
    'vahed' => 'nullable|string|max:255',
    'q_m' => 'nullable|integer',
    'V_m' => 'nullable',
    't_mosavvab' => 'nullable',
    'mosavabat' => 'nullable|string|max:255',
    'file_category_id' => 'required|exists:file_categories,id',
    'minutes' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
    'session_number' => 'nullable|string|max:255',
]);

$dateFields = ['erja', 'comete', 'date_shimare'];

foreach ($dateFields as $field) {

    if (!empty($validated[$field])) {

        $val = str_replace(
            ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'],
            ['0','1','2','3','4','5','6','7','8','9'],
            $validated[$field]
        );

        try {

            $validated[$field] = Jalalian::fromFormat('Y/m/d', $val)
                ->toCarbon()
                ->format('Y-m-d');

        } catch (\Throwable $e) {

            $validated[$field] = Carbon::parse($val)
                ->format('Y-m-d');
        }
    }
}

$normalizeNumber = function ($v) {

    if ($v === null || $v === '') {
        return null;
    }

    $v = str_replace(
        ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹','٫','،',','],
        ['0','1','2','3','4','5','6','7','8','9','.','.',''],
        $v
    );

    $v = preg_replace('/[^\d\.\-]/', '', $v);

    return is_numeric($v) ? (float)$v : null;
};

$currentVm = $normalizeNumber($validated['V_m'] ?? 0);
$t_mosavvab = $normalizeNumber($validated['t_mosavvab'] ?? 0);

try {

    DB::transaction(function () use (
        $allocation,
        $validated,
        $request,
        $currentVm,
        $t_mosavvab
    ) {

        $fileCategoryId = $validated['file_category_id'];

        $fileName = FileCategory::find($fileCategoryId)?->name;

        $minutesPath = $allocation->minutes;

        if ($request->hasFile('minutes')) {

            $file = $request->file('minutes');

            $filename = time().'_'.$file->getClientOriginalName();

            $minutesPath = $file->storeAs(
                'minutes',
                $filename,
                'public'
            );
        }

        $sumQuery = Allocation::query()
            ->where('id', '!=', $allocation->id);

        if (!empty($validated['code'])) {
            $sumQuery->where('code', $validated['code']);
        }

        if (!empty($validated['Takhsis_group'])) {
            $sumQuery->where(
                'Takhsis_group',
                $validated['Takhsis_group']
            );
        }

        if ($fileName) {
            $sumQuery->where('file_name', $fileName);
        }

        $otherSum = (float) $sumQuery->sum('V_m');

        $finalSum = round(
            $otherSum + ($currentVm ?? 0),
            3
        );

        $baghi = round(
            ($t_mosavvab ?? 0) - $finalSum,
            3
        );

        $allocation->update([

            ...$validated,

            'minutes' => $minutesPath,
            'file_name' => $fileName,

            'V_m' => $currentVm,
            't_mosavvab' => $t_mosavvab,

            'sum' => $finalSum,
            'baghi' => $baghi,

            'status' => 'draft',

            'updated_by' => auth()->id(),
        ]);

        $allocation->votes()->delete();

        if ($request->filled('votes')) {

            foreach ($request->votes as $userId => $value) {

                if ($value == 1) {

                    AllocationVote::create([
                        'allocation_id' => $allocation->id,
                        'user_id' => $userId,
                        'vote' => 1,
                        'comment' => null,
                    ]);
                }
            }
        }
    });

} catch (\Throwable $e) {

    return back()
        ->withInput()
        ->withErrors([
            'general' => $e->getMessage()
        ]);
}

return redirect()
    ->route('sessions.show', $session)
    ->with('success', 'رکورد با موفقیت ویرایش شد و مجدداً در انتظار تایید قرار گرفت.');


}


public function filterOptions(Request $request)
{
    $file = $request->query('file_name');
    $codes = Allocation::where('file_name', $file)->distinct()->pluck('code')->filter()->values();
    $takhsis = Allocation::where('file_name', $file)->distinct()->pluck('Takhsis_group')->filter()->values();
    // می‌تونی بقیه فیلدها را هم اضافه کنی
    return response()->json(['codes' => $codes, 'takhsis' => $takhsis]);
}



public function destroy(Request $request, $id)
{
    $allocation = Allocation::findOrFail($id);
    $allocation->delete();

    // اگر درخواست AJAX یا خواستنده JSON است، پاسخ JSON بده
    if ($request->wantsJson() || $request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'رکورد با موفقیت حذف شد'
        ], 200);
    }

    return back()->with('success', 'رکورد با موفقیت حذف شد.');
}


// validation helper
protected function validateData(Request $request, $id = null){
    return $request->validate([
      'row' => ['required','string', Rule::unique('allocations','row')->ignore($id)],
      'Shahrestan' => 'nullable|string',
      'sal' => 'nullable|integer',
      'erja' => 'nullable|date',
      'kelace' => ['nullable','string', Rule::unique('allocations','kelace')->ignore($id)],
      // ... بقیه فیلدها
    ]);
}

// تبدیل تاریخ اگر ورودی جلالی است (نمونه)
protected function convertDatesIfJalali(array $data){
    if(!empty($data['erja']) && preg_match('/^\d{4}\/\d{1,2}\/\d{1,2}$/',$data['erja'])){
        // مثال: 1402/01/12 -> به میلادی تبدیل کن (نیاز به بسته Morilog\Jalali)
        try{
            $data['erja'] = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $data['erja'])->toCarbon()->format('Y-m-d');
        }catch(\Throwable $e){}
    }
    // تکرار برای comete و date_shimare
    return $data;
}



/**
 * تبدیل یک رشته تاریخ (شمسی یا میلادی) به تاریخ میلادی فرمت Y-m-d
 * - اگر ورودی شبیه 1403/10/22 یا 1403-10-22 باشه -> فرض می‌کنیم شمسی و تبدیل می‌کنیم.
 * - اگر سال <= 1400 (مثلاً 2025-01-12) باشه -> فرض می‌کنیم میلادی و به Carbon تبدیل می‌کنیم.
 * - در صورت نامعتبر بودن => بازگشت null
 */
private function parseToGregorianDate(?string $value): ?string
{
    if (empty($value)) return null;

    // نرمال‌سازی: تبدیل '-' به '/' و ترمیم فاصله‌ها
    $v = trim(str_replace('-', '/', $value));

    // اگر فرمت عددی با 3 بخش باشد (YYYY/MM/DD)
    if (preg_match('/^\d{2,4}\/\d{1,2}\/\d{1,2}$/', $v)) {
        $parts = explode('/', $v);
        $year = (int) $parts[0];

        // اگر سال بزرگتر یا مساوی 1300 فرض شمسی (سال‌های شمسی معمولاً 13xx یا 14xx)
        if ($year >= 1300) {
            try {
                // Jalalian::fromFormat نیاز به فرمت یکنواخت دارد
                $jalali = str_pad($parts[0], 4, '0', STR_PAD_LEFT) . '/'
                        . str_pad($parts[1], 2, '0', STR_PAD_LEFT) . '/'
                        . str_pad($parts[2], 2, '0', STR_PAD_LEFT);

                return Jalalian::fromFormat('Y/m/d', $jalali)->toCarbon()->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        } else {
            // فرض میلادی (مثلاً 2025/01/12)
            try {
                return Carbon::createFromFormat('Y/m/d', $v)->format('Y-m-d');
            } catch (\Throwable $e) {
                // سعی کن با پارس معمولی هم تبدیل کنی
                try {
                    return Carbon::parse($v)->format('Y-m-d');
                } catch (\Throwable $e2) {
                    return null;
                }
            }
        }
    }

    // اگر رشته‌ای است که Carbon می‌تواند parse کند (مثلاً 2025-01-12)
    try {
        return Carbon::parse($value)->format('Y-m-d');
    } catch (\Throwable $e) {
        return null;
    }
}

public function export()
{
    return Excel::download(new AllocationsExport , 'allocations.xlsx');
}

public function approve(Allocation $allocation)
{
    $this->authorize('approve', $allocation);

    $allocation->update([
        'status' => 'approved',
        'approved_by' => auth()->id(),
        'approved_at' => now(),
    ]);

    return response()->json([
            'message' => 'رکورد با موفقیت تأیید شد'
        ]);}
        

public function download($id){
    $allocation = Allocation::findOrFail($id);
    $file=$allocation->minutes;
    $fullPath=storage_path('app/public/'.$file);

    if(!file_exists($fullPath)){
        return abort(404,'فایل یافت نشد');
    }
    return response()->download($fullPath);
}

public function voteStore(Request $request)
{
    // 1. گرفتن آرایه votes از فرم
    $votes = $request->input('votes', []);

    if (empty($votes)) {
        return back()->with('error', 'هیچ رأیی انتخاب نشده است!');
    }

    // 2. حذف رأی‌های قبلی از session (اگر چندبار فرم ارسال شود)
    session()->forget('allocation_votes_temp');

    // 3. برای هر کاربر تیک خورده یک رکورد vote بساز (در حالت Session)
    $vote_records = [];

    foreach ($votes as $userId => $value) {
        $vote_records[] = [
            'user_id' => $userId,
            'vote' => 1, // چون تیک یعنی موافق
            'comment' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // 4. ذخیره موقت در session تا بعداً هنگام ساخت allocation استفاده کنیم
    session(['allocation_votes_temp' => $vote_records]);

    return back()->with('success', 'رأی‌ها با موفقیت ثبت شدند. حالا می‌توانید اطلاعات تخصیص را وارد و ثبت نهایی کنید.');
}

public function allocationChart($id = null)
{
    $categories = $id
        ? FileCategory::where('parent_id', $id)->get()
        : FileCategory::whereNull('parent_id')->get();

    $result = [];

    foreach ($categories as $category) {

        $leafIds = $this->getLeafIds($category);

        // برای هر file_name + Takhsis_group + code
        // فقط بزرگترین sum در نظر گرفته شود
        $value = Allocation::whereIn('file_category_id', $leafIds)
            ->select(
                'file_name',
                'Takhsis_group',
                'code',
                DB::raw('MAX(`sum`) as max_sum')
            )
            ->groupBy(
                'file_name',
                'Takhsis_group',
                'code'
            )
            ->get()
            ->sum('max_sum');

        $result[] = [
            'id' => $category->id,
            'name' => $category->name,
            'value' => round($value, 3),
            'is_leaf' => $category->children()->count() == 0,
        ];
    }

    /*
     * اگر هیچ فرزندی نداشت (برگ بود)
     * نمودار بر اساس گروه تخصیص نمایش داده شود
     */
    if ($id && empty($result)) {

        $groups = Allocation::where('file_category_id', $id)
            ->select(
                'Takhsis_group',
                'file_name',
                'code',
                DB::raw('MAX(`sum`) as max_sum')
            )
            ->groupBy(
                'Takhsis_group',
                'file_name',
                'code'
            )
            ->get()
            ->groupBy('Takhsis_group');

        foreach ($groups as $takhsisGroup => $items) {

            $result[] = [
                'id' => null,
                'name' => $takhsisGroup,
                'value' => round($items->sum('max_sum'), 3),
                'is_leaf' => true,
            ];
        }
    }

    return response()->json($result);
}

private function getLeafIds($category)
{
    if ($category->children()->count() == 0) {
        return [$category->id];
    }

    $ids = [];

    foreach ($category->children as $child) {
        $ids = array_merge($ids, $this->getLeafIds($child));
    }

    return $ids;
}
}
