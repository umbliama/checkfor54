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
        $notApproved = User::where('isAdmin',0)->where('isApproved',0)->get();

        return Inertia::render('Admin/Index',[
            'employees' => $employees,
            'notApproved' => $notApproved
        ]);
    }

    public function approveUser($id)
    {
        $user = User::findOrFail($id);
    
        $user->isApproved = 1;
        $user->save(); 
    
        return redirect()->back()->with('success','Пользователь одобрен');
    }
}
