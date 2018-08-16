<?php

namespace App\KongClient\Contracts;

use GuzzleHttp\Psr7\Response;

interface OAuthLinkInterface
{
    /**
     * Sends a request for application info
     * @param  String $clientId                    The ID of the client as registered in Kong
     * @return \GuzzleHttp\Psr7\Response           [description]
     */
    public function getClientInfo(Response $clientId);
}
