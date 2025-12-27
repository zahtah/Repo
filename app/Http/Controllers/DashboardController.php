<?php
namespace App\Http\Controllers;

use App\Models\Allocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
     public function index(Request $request)
{
    $takhsisFilter = $request->input('Takhsis_group', 'all');
    $codeFilter = $request->input('code', 'all');
    $fileFilter = $request->input('file_name', 'all');

    $query = Allocation::query();

    if ($takhsisFilter !== 'all') $query->where('Takhsis_group', $takhsisFilter);
    if ($codeFilter !== 'all') $query->where('code', $codeFilter);
    if ($fileFilter !== 'all') $query->where('file_name', $fileFilter);

    // جمع‌گیری بر اساس file_name فقط (در صورت نیاز می‌توان code/Takhsis_group را هم اضافه کرد)
    $rows = $query->select(
        'file_name',
        DB::raw('COUNT(*) as items_count'),
        DB::raw('SUM(V_m) as total_volume'),           // مجموع واقعی اجزا
        // اگر sum در هر رکورد خودش مجموع کل فایل است، از MAX استفاده کن تا جمع مضاعف نشود
        DB::raw('MAX(`sum`) as cost'),                 // یا SUM(`sum`) اگر می‌خواهی جمع رکوردها
        DB::raw('MAX(baghi) as remaining'),            // یا MIN/AVG بسته به منطق
        DB::raw('SUM(t_mosavvab) as total_mosavvab')
    )
    ->groupBy('file_name')
    ->paginate(25);

    // درصد تحقق (cost بر حسب total_mosavvab)
    $rows->getCollection()->transform(function ($item) {
        $item->percent = $item->total_mosavvab > 0
            ? round(($item->cost / $item->total_mosavvab) * 100, 2)
            : 0;
        return $item;
    });

    // باقی کدهای pie charts — مثل قبل (با اعمال فیلترها)
    $groupQuery = Allocation::query();
    if ($takhsisFilter !== 'all') $groupQuery->where('Takhsis_group', $takhsisFilter);
    if ($codeFilter !== 'all') $groupQuery->where('code', $codeFilter);
    if ($fileFilter !== 'all') $groupQuery->where('file_name', $fileFilter);

    $groupData = $groupQuery->select('Takhsis_group', DB::raw('SUM(t_mosavvab) as total_mos'))
        ->whereNotNull('Takhsis_group')
        ->groupBy('Takhsis_group')
        ->orderByDesc('total_mos')
        ->get();

    // build labels/values (همان قبلی)
    $topN = 8;
    $topGroups = $groupData->take($topN);
    $othersTotal = $groupData->slice($topN)->sum('total_mos');

    $groupLabels = $topGroups->pluck('Takhsis_group')->map(fn($v) => (string)$v)->toArray();
    $groupValues = $topGroups->pluck('total_mos')->map(fn($v) => (float)$v)->toArray();
    if ($othersTotal > 0) { $groupLabels[] = 'سایر'; $groupValues[] = (float)$othersTotal; }

    // darkhast pie همان قبلی
    $darkQuery = Allocation::query();
    if ($takhsisFilter !== 'all') $darkQuery->where('Takhsis_group', $takhsisFilter);
    if ($codeFilter !== 'all') $darkQuery->where('code', $codeFilter);
    if ($fileFilter !== 'all') $darkQuery->where('file_name', $fileFilter);

    $darkhastData = $darkQuery->select('darkhast', DB::raw('COUNT(*) as cnt'))
        ->groupBy('darkhast')
        ->orderByDesc('cnt')
        ->get();

    $topM = 8;
    $topDark = $darkhastData->take($topM);
    $othersDCnt = $darkhastData->slice($topM)->sum('cnt');

    $darkLabels = $topDark->pluck('darkhast')->map(fn($v) => $v ? (string)$v : 'نامشخص')->toArray();
    $darkValues = $topDark->pluck('cnt')->map(fn($v) => (int)$v)->toArray();
    if ($othersDCnt > 0) { $darkLabels[] = 'سایر'; $darkValues[] = (int)$othersDCnt; }

    return view('admin.dashboard.index', compact(
        'rows', 'groupLabels', 'groupValues', 'darkLabels', 'darkValues',
        'takhsisFilter', 'codeFilter', 'fileFilter'
    ));
}


}
