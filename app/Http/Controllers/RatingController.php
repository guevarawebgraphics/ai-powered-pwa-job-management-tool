<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index() {
        $rules = \DB::table('system_settings')->where('id', 10)->first();
        return json_decode($rules->value);
    }
}
