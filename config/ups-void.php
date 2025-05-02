<?php

return [
    /*
    |--------------------------------------------------------------------------
    | UPS API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the UPS API integration.
    | You should obtain your Client ID and Client Secret from your UPS Developer account.
    |
    */

    'client_id' => env('UPS_CLIENT_ID'),

    'client_secret' => env('UPS_CLIENT_SECRET'),

    'grant_type' => 'client_credentials', // OAuth 2.0 Grant Type

    /*
    |--------------------------------------------------------------------------
    | UPS API Endpoints
    |--------------------------------------------------------------------------
    |
    | These are the endpoints for the UPS REST API.
    | Use the CIE (Customer Integration Environment) for testing and Production for live requests.
    |
    */

    'base_uri' => env('UPS_BASE_URI', 'https://wwwcie.ups.com/'), // Use https://wwwcie.ups.com/ for testing, https://www.ups.com/ for production

    'void_endpoint' => 'api/shipments/{shipment_identification_number}/void', // Endpoint for voiding a package

    'oauth_endpoint' => 'security/v1/oauth/token', // Endpoint for obtaining OAuth tokens
];

