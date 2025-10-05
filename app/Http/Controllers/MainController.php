<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MainController extends Controller {
    public function index(Request $request): Factory|View {
        return view('welcome');
    }
}
