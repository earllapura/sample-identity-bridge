<?php

namespace App\KongClient;

use App\KongClient\Contracts\OAuthLinkInterface;
use GuzzleHttp\ClientInterface;

class OAuthLink implements OAuthLinkInterface
{
    /**
     * Constructor
     * @param ClientInterface $client The HTTP client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @inheritDoc
     */
    public function getClientInfo(string $clientId)
    {
        return $this->queryGatewayFirst(
            '/oauth2',
            [
                'client_id' => $clientId,
            ],
            'client'
        );
    }

    /**
     * @inheritDoc
     */
    public function getScopeInfo(string $scopeName)
    {
        return $this->queryGatewayFirst(
            '/auth/scopes',
            [
                'scope_name' => $scopeName,
            ],
            'scope'
        );
    }

    /**
     * Helper function to get first result of a gateway query
     * @param  string $path            The relative path on the gateway
     * @param  array  $queryParameters The query parameters
     * @param  string $dataKey         The key for the data object
     * @return object                  A standard object with top level attribute
     *                                 <code>statusCode</code> for HTTP status code and
     *                                 <code>dataKey</code> with object containing the data
     */
    private function queryGatewayFirst(string $path, array $queryParameters, string $dataKey)
    {
        $response = $this->client->request(
            'GET',
            config('api.gateway') . $path,
            $queryParameters
        );
        $parsed = json_decode($response->getBody());
        $data = (!empty($parsed) && property_exists($parsed, 'data')) ? $parsed->data[0] : null;
        return (object)[$dataKey=>$data, 'statusCode'=>$response->getStatusCode()];
    }
}
