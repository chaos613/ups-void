<?php

namespace chaos613\UpsVoid\Facades; // Updated namespace

use Illuminate\Support\Facades\Facade;
use chaos613\UpsVoid\Http\Clients\UpsClient; // Updated use statement

/**
 * @method static array voidPackage(string $shipmentIdentificationNumber) Void a UPS package.
 * @see \chaos613\UpsVoid\Http\Clients\UpsClient // Updated docblock reference
 */
class UpsVoid extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ups-void'; // This should match the alias in the ServiceProvider
    }
}
