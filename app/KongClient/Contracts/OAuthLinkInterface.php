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

    /**
     * Sends an authorize request to Kong
     * @param  string $clientId     The ID of the client
     * @param  string $responseType The OAuth response type
     * @param  string $scope        The scope
     * @return object               A standard object with top level attribute
     *                              <code>statusCode</code> for HTTP status code and
     *                              <code>data</code> with oject containing data
     */
    public function authorize(string $clientId, string $responseType, string $scope);
}
