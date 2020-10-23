<?php

namespace FondOfSpryker\Client\CatalogPriceProductConnector\Plugin;

use Elastica\ResultSet;
use Spryker\Client\CatalogPriceProductConnector\Plugin\CurrencyAwareCatalogSearchResultFormatterPlugin as SprykerCurrencyAwareCatalogSearchResultFormatterPlugin;

/**
 * @method \FondOfSpryker\Client\CatalogPriceProductConnector\CatalogPriceProductConnectorFactory getFactory()
 */
class CurrencyAwareCatalogSearchResultFormatterPlugin extends SprykerCurrencyAwareCatalogSearchResultFormatterPlugin
{
    /**
     * @var string
     */
    protected $currentCurrencyIso;

    /**
     * @param \Elastica\ResultSet $searchResult
     * @param array $requestParameters
     *
     * @return mixed|array
     */
    protected function formatSearchResult(ResultSet $searchResult, array $requestParameters)
    {
        $result = $this->rawCatalogSearchResultFormatterPlugin->formatResult($searchResult, $requestParameters);

        if (!$this->isPriceProductDimensionEnabled()) {
            return $this->formatSearchResultWithoutPriceDimensions($result);
        }

        $priceProductClient = $this->getFactory()->getPriceProductClient();
        $priceProductStorageClient = $this->getFactory()->getPriceProductStorageClient();
        foreach ($result as &$product) {
            if (empty($product['price']) || empty($product['prices'])) {
                $currentProductPriceTransfer = $this->getPriceProductAbstractTransfers(
                    $product['id_product_abstract'],
                    $priceProductClient,
                    $priceProductStorageClient
                );
                $product['price'] = $currentProductPriceTransfer->getPrice();
                $product['prices'] = $currentProductPriceTransfer->getPrices();

                continue;
            }

            $product = $this->getFactory()->createPriceResolver()->resolve($product);
        }

        return $result;
    }
}
