<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();

        // Если пользователь аналитик,
        // но НЕ владелец — отправляем его в меню аналитики
        if ($user->can_view_analytics && !$user->is_owner) {
            return redirect()->route('admin.analytics.menu');
        }

        return view('admin.home');
    }
}