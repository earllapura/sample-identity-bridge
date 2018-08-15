<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OAuthController extends Controller
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
     * Show the authorize endpoint.
     *
     * @return \Illuminate\Http\Response
     */
    public function authorizeIndex(Request $request)
    {
        if (!$request->filled('client_id')) {
            abort(400);
        }
    }
}
