<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'totalUsers' => User::count(),
            'adminCount' => User::where('role', 'admin')->count(),
            'superAdminCount' => User::where('role', 'super_admin')->count(),
        ]);
    }
}
