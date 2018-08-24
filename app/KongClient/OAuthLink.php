<?php

namespace App\KongClient;

use App\KongClient\Contracts\OAuthLinkInterface;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\Facades\Auth;
use Psr\Http\Message\ResponseInterface;

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
        if(empty(config('api.client_path'))) {
            return (object) ['data' => null, 'error'=>"No path for client information configured in the system. Please contact the developer.", 'statusCode' => 500];
        }
        $response = $this->queryGateway(
            config('api.client_path'),
            ['client_id' => $clientId]
        );
        return $this->formatDataToJson($response, 'client', 0);
    }

    /**
     * @inheritDoc
     */
    public function getScopeInfo(string $scopeName)
    {
        if(empty(config('api.scope_path'))) {
            return (object) ['data' => null, 'error'=>"No path for scope information configured in the system. Please contact the developer.", 'statusCode' => 500];
        }
        $response = $this->queryGateway(
            config('api.scope_path'),
            ['scope_name' => $scopeName]
        );
        return $this->formatDataToJson($response, 'scopes');
    }

    /**
     * @inheritDoc
     */
    public function authorize(string $clientId, string $responseType, string $scope, array $headers = [])
    {
        $parameters['client_id']     = $clientId;
        $parameters['response_type'] = $responseType;
        $parameters['scope']         = $scope;
        if(empty(config('api.provision_key'))) {
            return (object) ['data' => null, 'error'=>"No provision key configured in the system. Please contact the developer.", 'statusCode' => 500];
        }
        if(empty(config('api.path'))) {
            return (object) ['data' => null, 'error'=>"No API path configured in the system. Please contact the developer.", 'statusCode' => 500];
        }
        $parameters['provision_key'] = config('api.provision_key');
        $parameters['authenticated_userid'] = Auth::user()->username;
        if (!empty($headers)) {
            $parameters['headers'] = $headers;
        }

        $response = $this->queryGateway(
            config('api.path'),
            $parameters,
            "POST"
        );
        $parsed = json_decode($response->getBody());
        return (object) ['data' => $parsed, 'statusCode' => $response->getStatusCode()];
    }

    /**
     * Helper function to get first result of a gateway query
     * @param  string $path            The relative path on the gateway
     * @param  array  $queryParameters The query parameters
     * @return ResponseInterface       The response from the gateway
     */
    private function queryGateway(string $path, array $queryParameters, string $method = "GET")
    {
        return $this->client->request(
            $method,
            config('api.gateway') . $path,
            $queryParameters
        );
    }

    /**
     * Formats raw data to a standard object with status code and data
     * @param  ResponseInterface $response The Guzzle Response object
     * @param  string            $dataKey  The data key
     * @param  int|null          $index    The index of the object in the data array.
     *                                     Null means all.
     * @return object                      A standard object with top level attribute
     *                                     <code>statusCode</code> for HTTP status code and
     *                                     <code>dataKey</code> with object containing the data
     */
    private function formatDataToJson(ResponseInterface $response, string $dataKey, int $index = null)
    {
        $parsed = json_decode($response->getBody());
        $data   = $this->getValueOfData($parsed, $index);
        return (object) [$dataKey => $data, 'statusCode' => $response->getStatusCode()];
    }

    /**
     * Gets the true value of the data, replacing non-existent values with null, and
     * returns data based on index.
     * @param  object   $parsedResponse The response object
     * @param  int|null $index          The index of the data in the data array
     * @return object|null              The parsed data object
     */
    private function getValueOfData(object $parsedResponse, int $index = null)
    {
        if (empty($parsedResponse) || !property_exists($parsedResponse, 'data')) {
            return null;
        }

        if (is_null($index)) {
            return $parsedResponse->data;
        }

        return $parsedResponse->data[$index];
    }
}
