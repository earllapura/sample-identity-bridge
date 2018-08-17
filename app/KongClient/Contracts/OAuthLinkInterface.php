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

    /**
     * Gets scope information from Kong
     * @param  string $scopeName The name of the scope
     * @return object            A standard object with top level attribute
     *                           <code>statusCode</code> for HTTP status code and
     *                           <code>scope</code> with oject containing scope info
     */
    public function getScopeInfo(string $scopeName);
}
