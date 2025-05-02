<?php

namespace chaos613\UpsVoid\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array voidPackage(string $shipmentIdentificationNumber) Void a UPS package.
 * @see \YourVendorName\UpsVoid\Http\Clients\UpsClient
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
