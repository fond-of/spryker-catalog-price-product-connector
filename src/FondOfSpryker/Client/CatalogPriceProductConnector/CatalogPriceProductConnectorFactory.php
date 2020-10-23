<?php

namespace FondOfSpryker\Client\CatalogPriceProductConnector;

use FondOfSpryker\Client\CatalogPriceProductConnector\Price\PriceResolver;
use FondOfSpryker\Client\CatalogPriceProductConnector\Price\ResolverInterface;
use Spryker\Client\CatalogPriceProductConnector\CatalogPriceProductConnectorFactory as SprykerCatalogPriceProductConnectorFactory;

class CatalogPriceProductConnectorFactory extends SprykerCatalogPriceProductConnectorFactory
{
    /**
     * @var \FondOfSpryker\Client\CatalogPriceProductConnector\Price\ResolverInterface
     */
    protected $priceResolver;

    /**
     * @return \FondOfSpryker\Client\CatalogPriceProductConnector\Price\ResolverInterface
     */
    public function createPriceResolver(): ResolverInterface
    {
        if ($this->priceResolver === null) {
            $this->priceResolver = new PriceResolver($this->getPriceClient(), $this->getCurrencyClient());
        }

        return $this->priceResolver;
    }
}
