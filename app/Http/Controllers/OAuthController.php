<?php

namespace App\Http\Controllers;

use App\KongClient\Contracts\OAuthLinkInterface;
use Illuminate\Http\Request;

class OAuthController extends Controller
{
    /**
     * Link object to call API gateway
     * @var OAuthLinkInterface
     */
    protected $link;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(OAuthLinkInterface $link)
    {
        $this->middleware('auth');
        $this->link = $link;
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
        $response       = $this->link->getClientInfo($request->client_id);
        $responseStatus = $response->statusCode;
        if (!($responseStatus >= 200 && $responseStatus < 300)) {
            abort($responseStatus);
        }
    }
}
