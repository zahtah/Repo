<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // For transactions
use Illuminate\Support\Facades\Auth; // If you need to track who approved

class SessionController extends Controller
{
    /**
     * لیست جلسات
     */
    public function index()
    {
        // همراه با تعداد اعضا و تعداد تخصیص‌ها
         $sessions = Session::withCount(['users', 'allocations'])->get();

        return view('admin.sessions.index', compact('sessions'));
    }

    /**
     * فرم ایجاد جلسه
     */
    public function create()
    {
        // همه کاربران برای انتخاب اعضای جلسه
        $users = User::orderBy('name')->get(); // اگر فیلد name نداری، بگو عوضش کنیم

        return view('admin.sessions.create', compact('users'));
    }

    /**
     * ذخیره جلسه جدید
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'session_number' => 'required|integer',
            'title'          => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'date'           => 'required|date',
            'time'           => 'required',
            'users'          => 'nullable|array',
            'users.*'        => 'exists:users,id',
        ]);

        // ساخت جلسه
        $session = Session::create([
            'session_number' => $data['session_number'],
            'title'          => $data['title'] ?? null,
            'description'    => $data['description'] ?? null,
            'date'           => $data['date'],
            'time'           => $data['time'],
        ]);

        // اتصال اعضای جلسه
        if (!empty($data['users'])) {
            $session->users()->sync($data['users']);
        }

        return redirect()
            ->route('sessions.index')
            ->with('success', 'جلسه با موفقیت ایجاد شد.');
    }

    /**
     * نمایش جزئیات یک جلسه
     */
    public function show(Session $session)
    {
        $session->load('users', 'allocations','allocations.votes.user'); 
    
        return view('admin.sessions.show', compact('session'));
    }

    /**
     * فرم ویرایش جلسه
     */
    public function edit(Session $session)
    {
        $users      = User::orderBy('name')->get();
        $selectedUsers = $session->users()->pluck('users.id')->toArray();

        return view('admin.sessions.edit', compact('session', 'users', 'selectedUsers'));
    }

    /**
     * بروزرسانی جلسه
     */
    public function update(Request $request, Session $session)
    {
        $data = $request->validate([
            'session_number' => 'nullable|integer',
            'title'          => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'date'           => 'nullable|date',
            'time'           => 'nullable',
            'users'          => 'nullable|array',
            'users.*'        => 'exists:users,id',
        ]);

        // حذف مقادیر خالی تا مقدار قبلی حفظ شود
        $filteredData = array_filter($data, function ($value) {
            return $value !== null && $value !== '';
        });

        $session->fill($filteredData);
        $session->save();

        // فقط اگر users ارسال شده باشد بروزرسانی شود
        if ($request->has('users')) {
            $session->users()->sync($data['users'] ?? []);
        }

        return redirect()
            ->route('sessions.index')
            ->with('success', 'جلسه با موفقیت بروزرسانی شد.');
    }

    /**
     * حذف جلسه
     */
    public function destroy(Session $session)
    {
        $session->delete();

        return redirect()
            ->route('sessions.index')
            ->with('success', 'جلسه حذف شد.');
    }

    public function finalApproveAndReport(Session $session)
    {
        // Start a database transaction to ensure atomicity
        DB::beginTransaction();

        try {
            // 1. Update status of all allocations for this session to 'approved'
            // Ensure your Allocation model has a status field and the relationship is set up.
            // Assuming Session hasMany Allocations relationship
            $session->allocations()->update(['status' => 'approved']);

            // Reload the allocations with their votes after updating the status
            // This ensures the report has the latest data.
            $session->load('allocations.votes.user');

            // 2. Prepare data for the report
            // We already have $session loaded with its allocations and votes.
            // The $session->users relationship should also be loaded for the signature part.
            $session->load('users'); // Ensure users are loaded

            // 3. Generate the report (PDF/DOCX)
            // We'll use the canvas tool for this.
            // The content will be generated dynamically.

            // Construct the report title
            $reportTitle = "صورتجلسه نهایی - جلسه شماره " . $session->session_number;

            // Construct the report body dynamically
            $reportBody = "<h1>صورتجلسه نهایی</h1>";
            $reportBody .= "<p><strong>شماره جلسه:</strong> " . $session->session_number . "</p>";
            $reportBody .= "<p><strong>عنوان جلسه:</strong> " . $session->title . "</p>";
            $reportBody .= "<p><strong>تاریخ:</strong> " . $session->date . "</p>";
            $reportBody .= "<p><strong>زمان:</strong> " . $session->time . "</p>";
            $reportBody .= "<p><strong>توضیحات:</strong> " . nl2br(e($session->description)) . "</p>"; // nl2br for line breaks

            $reportBody .= "<h2>تخصیص‌های نهایی شده</h2>";
            $reportBody .= "<table border='1' style='width:100%; border-collapse: collapse;'>";
            $reportBody .= "<thead><tr>";
            $reportBody .= "<th>#</th>";
            $reportBody .= "<th>نام واحد / متقاضی</th>";
            // Add other allocation headers if needed
            $reportBody .= "<th>وضعیت</th>";
            $reportBody .= "</tr></thead>";
            $reportBody .= "<tbody>";

            foreach ($session->allocations as $index => $allocation) {
                $reportBody .= "<tr>";
                $reportBody .= "<td>" . ($index + 1) . "</td>";
                $reportBody .= "<td>" . e($allocation->motaghasi ?? '') . "</td>";
                // Add other allocation data
                $reportBody .= "<td>" . ($allocation->status === 'approved' ? 'تایید شده' : 'پیش‌نویس') . "</td>";
                $reportBody .= "</tr>";
            }

            foreach ($session->allocations as $allocation) {
                $allocation->status = 'approved';
                $allocation->approved_by = auth()->id(); // شناسه کاربری که عملیات را انجام داده
                $allocation->approved_at = Carbon::now();
                $allocation->save();
            }

            $reportBody .= "</tbody></table>";

            $reportBody .= "<h2>امضاها و موافقت‌ها</h2>";

            // Loop through users to show their signatures and approvals
            if ($session->users->count()) {
                foreach ($session->users as $user) {
                    $reportBody .= "<h3>" . e($user->name) . "</h3>";
                    $reportBody .= "<p><strong>تاریخ:</strong> " . \Carbon\Carbon::now()->format('Y-m-d H:i') . "</p>"; // Placeholder for signature date

                    $approvedAllocations = [];
                    $disapprovedAllocations = [];
                    $noVoteAllocations = [];

                    foreach ($session->allocations as $allocation) {
                        // Find the vote for this user and this allocation
                        $vote = $allocation->votes->firstWhere('user_id', $user->id);

                        if ($vote) {
                            if ($vote->vote == 1) { // Assuming 1 is 'approved'
                                $approvedAllocations[] = $allocation->id; // Store allocation ID or iteration number
                            } elseif ($vote->vote == 0) { // Assuming 0 is 'disapproved'
                                $disapprovedAllocations[] = $allocation->id;
                            }
                        } else {
                            $noVoteAllocations[] = $allocation->id;
                        }
                    }

                    // Display approvals
                    if (!empty($approvedAllocations)) {
                        $reportBody .= "<p><strong>تخصیص‌های مورد موافقت:</strong> " . implode(', #', $approvedAllocations) . "</p>";
                    }
                    if (!empty($disapprovedAllocations)) {
                        $reportBody .= "<p><strong>تخصیص‌های مورد مخالفت:</strong> " . implode(', #', $disapprovedAllocations) . "</p>";
                    }
                    if (!empty($noVoteAllocations)) {
                        $reportBody .= "<p><strong>تخصیص‌هایی که رأی داده نشده:</strong> " . implode(', #', $noVoteAllocations) . "</p>";
                    }
                    $reportBody .= "<br>"; // Spacer between user signatures
                }
            } else {
                $reportBody .= "<p>هیچ عضوی برای این جلسه ثبت نشده است.</p>";
            }

            $session->load( 'allocations.votes.user');

            // 4. Commit تراکنش
            DB::commit();
            foreach ($session->allocations as $allocation) {

    $allocation->mahdoude = match($allocation->code) {
        1502 => 'قائم شهر ـ جويبار',
        1503 => 'ساری-نکا',
        1601 => 'گرگان',
        1602 => 'رباط قره بيل ـ دانيال نبي',
        4101 => 'درياچه نمك',
        4134 => 'ورامين',
        4701 => 'دشت کویر',
        4702 => 'كوير سمنان',
        4703 => 'سرخه',
        4704 => 'سمنان',
        4705 => 'گرمسار',
        4706 => 'فيروزكوه',
        4707 => 'مباركيه (كوير گرمسار)',
        4708 => 'ايوانكي',
        4710 => 'چويانان',
        4711 => 'جندق',
        4716 => 'درونه',
        4731 => 'ترود',
        4732 => 'بيارجمند',
        4733 => 'كوير خارطوران',
        4734 => 'داورزن ـ فرومد',
        4740 => 'جوين',
        4742 => 'جاجرم',
        4746 => 'ميامي',
        4747 => 'دامغان',
        4748 => 'كوير حاج علي قلي (كوير دامغان)',
        4749 => 'شاهرود',
        4750 => 'بسطام',
        default => '-',
    };

    $allocation->parentCategory =
        optional($allocation->fileCategory?->parent)->name ?? 'نام پیدا نشد';

    $allocation->eblaghDate =
        $allocation->date_shimare_jalali ?? '-';
}


            $usersReport = [];

            foreach ($session->users as $user) {

                $approvedAllocations = [];

                foreach ($session->allocations as $allocation) {

                    $vote = $allocation->votes->firstWhere('user_id', $user->id);

                    if ($vote && $vote->vote == 1) {
                        $approvedAllocations[] = [
                            'id' => $allocation->id,
                            'title' => $allocation->file_name ?? $allocation->motaghasi,
                        ];
                    }
                }

                $usersReport[] = [
                    'user' => $user,
                    'approved_allocations' => $approvedAllocations,
                ];
            }
                                       
            // 5. بازگرداندن View گزارش با داده‌های جلسه
            // فرض بر این است که فایل view شما در resources/views/sessions/final_report.blade.php قرار دارد
            return response()->view('admin.sessions.final_report', [
                'session_data' => $session->toArray(),'usersReport' => $usersReport,  // ارسال کل داده های جلسه به همراه تخصیص ها و رای ها
            ]);
            // ... (در صورت خطا)
            return response()->json([
                'error' => 'خطا در تایید نهایی جلسه و تولید گزارش: ' . $e->getMessage(),
                'session' => $session,
            ], 500);

        } catch (\Exception $e) {
            // If any error occurs, rollback the transaction
            DB::rollBack();
            // Log the error or handle it as appropriate
            // \Log::error("Failed to approve session and generate report: " . $e->getMessage());
            return back()->with('error', 'خطا در تایید نهایی جلسه و تولید گزارش: ' . $e->getMessage());
        }
        @include('admin.allocations.scripts');
    }
    
    
}
