<?php

namespace App\KongClient\Contracts;

interface OAuthLinkInterface
{
    /**
     * Sends a request for application info
     * @param  string $clientId  The ID of the client as registered in Kong
     * @return object            A standard object with top level attribute
     *                           <code>statusCode</code> for HTTP status code and
     *                           <code>client</code> with oject containing client info
     */
    public function getClientInfo(string $clientId);
}
