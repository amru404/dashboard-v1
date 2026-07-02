<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentationController extends Controller
{
    public function userGuide(): View
    {
        return view('documentation.user-guide');
    }

    public function adminGuide(): View
    {
        return view('documentation.admin-guide');
    }

    public function apiDocumentation(): View
    {
        return view('documentation.api');
    }
}
