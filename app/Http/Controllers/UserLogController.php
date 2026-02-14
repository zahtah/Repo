<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserLog;

class UserLogController extends Controller
{
    //
    public function index()
    {
        $logs = UserLog::with('user')
            ->latest('logged_at')
            ->paginate(20);

        return view('admin.user_logs.index', compact('logs'));
    }
}
