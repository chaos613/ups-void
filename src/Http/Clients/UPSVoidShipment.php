<?php

namespace chaos613\UpsVoid\Http\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class UpsClient
{
    protected Client $httpClient;
    protected array $config;
    protected ?string $accessToken = null;
    protected ?\DateTimeInterface $tokenExpiresAt = null;

    /**
     * Constructor.
     *
     * @param Client $httpClient The Guzzle HTTP client instance.
     * @param array $config The package configuration array.
     */
    public function __construct(Client $httpClient, array $config)
    {
        $this->httpClient = $httpClient;
        $this->config = $config;
    }

    /**
     * Get the OAuth 2.0 access token.
     *
     * @return string The access token.
     * @throws \Exception If the token cannot be obtained.
     */
    protected function getAccessToken(): string
    {
        // Check if we have a valid, non-expired token
        if ($this->accessToken && $this->tokenExpiresAt && $this->tokenExpiresAt > new \DateTime()) {
            return $this->accessToken;
        }

        // Token is expired or not set, get a new one
        try {
            $response = $this->httpClient->post($this->config['oauth_endpoint'], [
                'headers' => [
                    'x-ups-client-id' => $this->config['client_id'],
                    'x-ups-client-secret' => $this->config['client_secret'],
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'grant_type' => $this->config['grant_type'],
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['access_token'])) {
                $this->accessToken = $data['access_token'];
                // UPS usually returns expires_in in seconds
                $expiresIn = $data['expires_in'] ?? 3600; // Default to 1 hour if not provided
                $this->tokenExpiresAt = (new \DateTime())->modify("+{$expiresIn} seconds");

                return $this->accessToken;
            } else {
                throw new \Exception('Failed to obtain UPS OAuth token: ' . json_encode($data));
            }

        } catch (GuzzleException $e) {
            throw new \Exception('Guzzle error while obtaining UPS OAuth token: ' . $e->getMessage(), $e->getCode(), $e);
        } catch (\Exception $e) {
            throw new \Exception('Error obtaining UPS OAuth token: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Void a UPS package.
     *
     * @param string $shipmentIdentificationNumber The shipment identification number (tracking number).
     * @return array The response from the UPS API.
     * @throws \Exception If the API call fails.
     */
    public function voidPackage(string $shipmentIdentificationNumber): array
    {
        try {
            $accessToken = $this->getAccessToken();

            $endpoint = str_replace(
                '{shipment_identification_number}',
                $shipmentIdentificationNumber,
                $this->config['void_endpoint']
            );

            $response = $this->httpClient->delete($endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'x-ups-client-id' => $this->config['client_id'], // Include client ID again as per some API docs
                    'Content-Type' => 'application/json',
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            // Check for successful status codes (e.g., 200 OK, 204 No Content)
            if ($statusCode >= 200 && $statusCode < 300) {
                return [
                    'success' => true,
                    'message' => 'Package void request successful.',
                    'status_code' => $statusCode,
                    'data' => $data, // May contain confirmation details
                ];
            } else {
                 // Handle non-2xx responses as errors
                return [
                    'success' => false,
                    'message' => 'UPS API returned an error status code.',
                    'status_code' => $statusCode,
                    'errors' => $data, // Contains error details from UPS
                ];
            }


        } catch (GuzzleException $e) {
            // Catch Guzzle exceptions (network errors, request failures)
            return [
                'success' => false,
                'message' => 'Guzzle error during UPS void request: ' . $e->getMessage(),
                'errors' => ['exception' => $e->getMessage()],
            ];
        } catch (\Exception $e) {
            // Catch other exceptions (e.g., from getAccessToken)
             return [
                'success' => false,
                'message' => 'An error occurred during the UPS void request: ' . $e->getMessage(),
                'errors' => ['exception' => $e->getMessage()],
            ];
        }
    }
}
