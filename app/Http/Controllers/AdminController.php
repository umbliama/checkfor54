<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;


class AdminController extends Controller
{
    public function index()
    {
        $employees = User::where('isAdmin', 0)->get();

        return Inertia::render('Admin/Index',[
            'employees' => $employees
        ]);
    }
}
