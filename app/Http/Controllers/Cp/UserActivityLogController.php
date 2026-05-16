<?php

namespace App\Http\Controllers\Cp;

use App\Http\Controllers\Controller;
use App\Models\UserActivityLog;
use Illuminate\View\View;

class UserActivityLogController extends Controller
{
    public function index(): View
    {
        $logs = UserActivityLog::query()
            ->with('user:id,name,email')
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();

        return view('cp.user-activity-logs', [
            'logs' => $logs,
        ]);
    }
}
