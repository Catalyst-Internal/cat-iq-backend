<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class SiteUsersController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->with('latestLogin')
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('admin.site-users.index', [
            'users' => $users,
        ]);
    }
}
