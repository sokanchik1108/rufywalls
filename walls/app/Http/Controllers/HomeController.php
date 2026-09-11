<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // Владелец всегда остаётся на /admin
        if ((int) $user->is_owner === 1) {
            return view('admin.home');
        }

        // Аналитик попадает в меню аналитики
        if ((int) $user->can_view_analytics === 1) {
            return redirect()->route('admin.analytics.menu');
        }

        // Остальные пользователи тоже остаются на /admin
        return view('admin.home');
    }
}