<?php

namespace FondOfSpryker\Client\CatalogPriceProductConnector;

use Generated\Shared\Transfer\CurrencyTransfer;
use Spryker\Client\CatalogPriceProductConnector\CatalogPriceProductConnectorFactory as SprykerCatalogPriceProductConnectorFactory;

class CatalogPriceProductConnectorFactory extends SprykerCatalogPriceProductConnectorFactory
{
    /**
     * @return \Generated\Shared\Transfer\CurrencyTransfer
     */
    public function getCurrentCurrency(): CurrencyTransfer
    {
        return $this->getCurrencyClient()->getCurrent();
    }
}
