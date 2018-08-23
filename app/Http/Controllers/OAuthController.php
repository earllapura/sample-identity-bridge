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
        $clientQueryResponse       = $this->link->getClientInfo($request->client_id);
        $scopeQueryResponse        = $this->link->getScopeInfo($request->scopes);
        $clientQueryResponseStatus = $clientQueryResponse->statusCode;
        $scopeQueryResponseStatus  = $clientQueryResponse->statusCode;
        if (!($clientQueryResponseStatus >= 200 && $clientQueryResponseStatus < 300)) {
            abort($clientQueryResponseStatus);
        }
        if (!($scopeQueryResponseStatus >= 200 && $scopeQueryResponseStatus < 300)) {
            abort($scopeQueryResponseStatus);
        }

        $scopeArray = array();
        foreach ($scopeQueryResponse->scopes as $rawScope) {
            $scopeArray[$rawScope->name] = $rawScope->description;
        }

        return view('oauth.authorize',
            [
                'application_name' => $clientQueryResponse->client->name,
                'scopes'           => $scopeArray,
            ]
        );
    }

    public function authorizePost(Request $request)
    {
        if (!$request->filled('client_id') ||
            !$request->filled('response_type') ||
            !$request->filled('scope')
        ) {
            abort(400);
        }
        $authorizationHeader = $request->header('Authorization');
        $headers             = !empty($authorizationHeader) ? ['Authorization' => $authorizationHeader] : [];
        $response            = $this->link->authorize($request->client_id, $request->response_type, $request->scope, $headers);
        $statusCode          = $response->statusCode;
        if (!($statusCode >= 200 && $statusCode < 300)) {
            abort($statusCode);
        }

        return redirect($response->data->redirect_uri);
    }
}
