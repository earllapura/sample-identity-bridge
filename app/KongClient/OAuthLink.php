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
        $response = $this->client->request('GET', config('api.gateway') . '/oauth2',
            [
                'client_id' => $clientId,
            ]
        );
        $parsed = json_decode($response->getBody());
        $clientInfo = (!empty($parsed) && property_exists($parsed, 'data')) ? $parsed->data[0] : null;
        return (object)['client'=>$clientInfo, 'statusCode'=>$response->getStatusCode()];
    }
}
