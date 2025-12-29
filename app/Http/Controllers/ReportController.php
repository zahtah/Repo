<?php

namespace App\Http\Controllers;

use App\Models\Allocation;
use App\Models\FileCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * گرفتن تمام فرزندان یک نود (بازگشتی)
     */
    private function getAllDescendantIds(FileCategory $category)
    {
        $ids = [];

        foreach ($category->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge(
                $ids,
                $this->getAllDescendantIds($child)->toArray()
            );
        }

        return collect($ids);
    }

    public function index(Request $request)
{
    // =============================
    // 1) ریشه‌های هایرآرکی (برای Blade)
    // =============================
    //$rootCategories = FileCategory::whereNull('parent_id')->get();
    $rootCategories = FileCategory::withCount('children')
    ->whereNull('parent_id')
    ->get();

    
    // همینجا یک نسخه برای JS tree آماده می‌کنیم
    $allCategories = $rootCategories; // برای JS

    // =============================
    // 2) فیلترها
    // =============================
    $codes   = $request->input('code', []);
    $takhsis = $request->input('Takhsis_group', []);

    // --- FIX مهم: file_category_ids ---
    $fileCategoryIds = $request->input('file_category_ids', []);

    // اگر به صورت "15,16" آمده
    if (is_string($fileCategoryIds)) {
        $fileCategoryIds = array_filter(explode(',', $fileCategoryIds));
    }

    $fileCategoryIds = is_array($fileCategoryIds) ? $fileCategoryIds : [];

    $codes   = is_array($codes)   ? array_filter($codes)   : [$codes];
    $takhsis = is_array($takhsis) ? array_filter($takhsis) : [$takhsis];

    // =============================
    // 3) استخراج نام نمایشی فایل‌ها (Breadcrumb)
    // =============================
    // -----------------------------
// لیبل نمایشی فایل
// -----------------------------
$fileNameLabel = 'همه';

if (!empty($fileCategoryIds)) {
    $categories = FileCategory::with('parent')
        ->whereIn('id', $fileCategoryIds)
        ->get();

    $grouped = $categories->groupBy(fn($c) => $c->parent?->name ?? $c->name);

    $parts = [];

    foreach ($grouped as $parentName => $children) {
        if ($children->first()->parent_id) {
            $parts[] = $parentName . ' → ' . $children->pluck('name')->join('، ');
        } else {
            $parts[] = $parentName;
        }
    }

    $fileNameLabel = implode(' | ', $parts);
}

// -----------------------------
// لیبل نمایشی code
// -----------------------------
$codeLabel = 'همه';

if (!empty($codes) && !in_array('all', $codes)) {
    $codeLabel = implode('، ', $codes);
}

// -----------------------------
// لیبل نمایشی Takhsis_group
// -----------------------------
$takhsisLabel = 'همه';

if (!empty($takhsis) && !in_array('all', $takhsis)) {
    $takhsisLabel = implode('، ', $takhsis);
}


    if (!empty($fileCategoryIds)) {

        $categories = FileCategory::with('parent')
            ->whereIn('id', $fileCategoryIds)
            ->get();

        // گروه‌بندی بر اساس والد
        $grouped = $categories->groupBy(fn($c) => $c->parent?->name ?? $c->name);

        $parts = [];

        foreach ($grouped as $parentName => $children) {
            if ($children->first()->parent_id) {
                $parts[] = $parentName . ' → ' . $children->pluck('name')->join('، ');
            } else {
                $parts[] = $parentName;
            }
        }

        $fileNameLabel = implode(' | ', $parts);
    }

    // =============================
    // 4) SubQuery (محاسبه درست per فایل)
    // =============================
    $subQuery = Allocation::query()
        ->when(!empty($codes) && !in_array('all', $codes),
            fn($q) => $q->whereIn('code', $codes)
        )
        ->when(!empty($takhsis) && !in_array('all', $takhsis),
            fn($q) => $q->whereIn('Takhsis_group', $takhsis)
        )
        ->when(!empty($fileCategoryIds),
            fn($q) => $q->whereIn('file_category_id', $fileCategoryIds)
        )
        ->select(
            'code',
            'Takhsis_group',
            'file_category_id',
            DB::raw('MAX(t_mosavvab) AS t_mosavvab'),
            DB::raw('SUM(V_m) AS sum_v_m')
        )
        ->groupBy('code', 'Takhsis_group', 'file_category_id');

    // =============================
    // 5) آیا فقط یک انتخاب داریم؟
    // =============================
    $singleSelection =
        count($codes) === 1 &&
        count($takhsis) === 1 &&
        count($fileCategoryIds) === 1;

    // =============================
    // 6) Query نهایی گزارش
    // =============================
    $rows = DB::table(DB::raw("({$subQuery->toSql()}) AS sub"))
        ->mergeBindings($subQuery->getQuery())
        ->select(
        DB::raw("'" . $fileNameLabel . "' AS file_name"),
        DB::raw("'" . $codeLabel . "' AS code"),
        DB::raw("'" . $takhsisLabel . "' AS Takhsis_group"),
        DB::raw('SUM(sub.t_mosavvab) AS total_volume'),
        DB::raw('ROUND(SUM(sub.sum_v_m), 2) AS cost'),
        DB::raw('ROUND(SUM(sub.t_mosavvab) - SUM(sub.sum_v_m), 2) AS remaining')
    )

        ->paginate(25)
        ->appends($request->query());


     // =============================
    // محاسبه تعداد متقاضیان
    // =============================
    $applicantsPerFile = Allocation::query()
    ->when(!empty($codes) && !in_array('all', $codes),
        fn($q) => $q->whereIn('code', $codes)
    )
    ->when(!empty($takhsis) && !in_array('all', $takhsis),
        fn($q) => $q->whereIn('Takhsis_group', $takhsis)
    )
    ->when(!empty($fileCategoryIds),
        fn($q) => $q->whereIn('file_category_id', $fileCategoryIds)
    )
    ->select(
        'file_category_id',
        'code',
        'Takhsis_group',
        DB::raw('COUNT(DISTINCT kelace) AS applicants_count')
    )
    ->groupBy('file_category_id', 'code', 'Takhsis_group')
    ->get()
    ->keyBy(fn ($r) =>
        $r->file_category_id . '|' . $r->code . '|' . $r->Takhsis_group
    );


    // =============================
    // 6-A) گزارش تجمیعی برای هر سند
    // =============================
    $perFileRows = DB::table(DB::raw("({$subQuery->toSql()}) AS sub"))
    ->mergeBindings($subQuery->getQuery())
    ->join('file_categories as fc', 'fc.id', '=', 'sub.file_category_id')
    ->select(
        'fc.name AS file_name',
        'sub.file_category_id',
        'sub.code',
        'sub.Takhsis_group',
        DB::raw('SUM(sub.t_mosavvab) AS total_volume'),
        DB::raw('ROUND(SUM(sub.sum_v_m), 2) AS cost'),
        DB::raw('ROUND(SUM(sub.t_mosavvab) - SUM(sub.sum_v_m), 2) AS remaining')
    )
    ->groupBy('fc.name', 'sub.file_category_id', 'sub.code', 'sub.Takhsis_group')
    ->get()
    ->map(function ($row) use ($applicantsPerFile) {
        $key = $row->file_category_id . '|' . $row->code . '|' . $row->Takhsis_group;
        $row->applicants_count = $applicantsPerFile[$key]->applicants_count ?? 0;
        return $row;
    });


        // =============================
    // 6-B) جمع کل همه سندها
    // =============================
    $grandTotal = DB::table(DB::raw("({$subQuery->toSql()}) AS sub"))
        ->mergeBindings($subQuery->getQuery())
        ->select(
            DB::raw('SUM(sub.t_mosavvab) AS total_volume'),
            DB::raw('ROUND(SUM(sub.sum_v_m), 2) AS cost'),
            DB::raw('ROUND(SUM(sub.t_mosavvab) - SUM(sub.sum_v_m), 2) AS remaining')
        )
        ->first();

     // =============================
    // 6-B) جمع کل همه متقاضیان
    // =============================

        $grandApplicants = Allocation::query()
    ->when(!empty($codes) && !in_array('all', $codes),
        fn($q) => $q->whereIn('code', $codes)
    )
    ->when(!empty($takhsis) && !in_array('all', $takhsis),
        fn($q) => $q->whereIn('Takhsis_group', $takhsis)
    )
    ->when(!empty($fileCategoryIds),
        fn($q) => $q->whereIn('file_category_id', $fileCategoryIds)
    )
    ->distinct('kelace')
    ->count('kelace');




    // =============================
    // 7) داده‌های View
    // =============================
    $codesAll = Allocation::select('code')
        ->distinct()
        ->orderBy('code')
        ->pluck('code');

    $takhsisOptions = Allocation::select('Takhsis_group')
        ->distinct()
        ->orderBy('Takhsis_group')
        ->pluck('Takhsis_group');

    return view('admin.reports.allocations', compact(
        'rows',
        'perFileRows',   // 👈 جدول اصلی (هر سند یک سطر)
        'grandTotal',    // 👈 سطر آخر
        'grandApplicants',
        'codesAll',
        'takhsisOptions',
        'codes',
        'takhsis',
        'fileCategoryIds',
        'rootCategories',
        'allCategories'
    ));
}



    /**
     * Ajax → دریافت کدها بر اساس گروه تخصیص
     */
    public function codesForGroup(Request $request)
    {
        $takhsis = $request->get('Takhsis_group');

        $q = Allocation::query();

        if ($takhsis && $takhsis !== 'all') {
            $q->where('Takhsis_group', $takhsis);
        }

        $codes = $q->select('code')
            ->distinct()
            ->whereNotNull('code')
            ->orderBy('code')
            ->pluck('code');

        return response()->json(['codes' => $codes]);
    }

    public function fileCategoryChildren($parentId)
{
    return FileCategory::where('parent_id', $parentId)
        ->select('id', 'name')
        ->withCount('children')
        ->get()
        ->map(fn ($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'has_children' => $c->children_count > 0,
        ]);
}
public function leafCategoryIds($id)
{
    $category = FileCategory::with('children')->findOrFail($id);

    $leafIds = [];

    $walk = function ($cat) use (&$walk, &$leafIds) {
        if ($cat->children->isEmpty()) {
            $leafIds[] = $cat->id;
            return;
        }

        foreach ($cat->children as $child) {
            $child->load('children');
            $walk($child);
        }
    };

    $walk($category);

    return response()->json($leafIds); // همیشه همه leafها
}


}
