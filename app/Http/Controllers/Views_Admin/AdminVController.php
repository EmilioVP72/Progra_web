<?php

namespace App\Http\Controllers\Views_Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminVController extends Controller
{
    public function index()
    {
        return view('Panel_Admin.index');
    }

    public function create()
    {
        return view('Panel_Admin.form_create');
    }

    public function edit(string $id)
    {
        return view('Panel_Admin.form_update', compact('id'));
    }
}
