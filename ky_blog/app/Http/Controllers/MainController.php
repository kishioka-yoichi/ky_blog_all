<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller {
    public function main(Request $request) {
        return view('main.main');
    }
    public function mainPost(Request $request) {
        return redirect('/main');
    }
}