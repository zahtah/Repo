<?php
namespace App\Http\Controllers;

use App\Models\Allocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        // گرفتن لیست file_name و code از دیتابیس
        $fileNames = Allocation::select('file_name')
            ->distinct()
            ->orderBy('file_name')
            ->pluck('file_name');

        $codes = Allocation::select('code')
            ->distinct()
            ->orderBy('code')
            ->pluck('code');

        // گرفتن مقدار انتخاب شده
        $selectedFile = $request->input('file_name');
        $selectedCode = $request->input('code');

        $groupLabels = [];
        $groupPercentages = [];

        if ($selectedFile && $selectedCode) {

            $data = Allocation::where('file_name', $selectedFile)
                ->where('code', $selectedCode)
                ->select('Takhsis_group', DB::raw('COUNT(*) as total'))
                ->groupBy('Takhsis_group')
                ->get();

            $totalCount = $data->sum('total');

            foreach ($data as $row) {
                $groupLabels[] = $row->Takhsis_group ?? 'نامشخص';
                $groupPercentages[] = $totalCount > 0
                    ? round(($row->total / $totalCount) * 100, 2)
                    : 0;
            }
        }

        return view('admin.dashboard.index', compact(
            'fileNames',
            'codes',
            'selectedFile',
            'selectedCode',
            'groupLabels',
            'groupPercentages'
        ));
    }



}
