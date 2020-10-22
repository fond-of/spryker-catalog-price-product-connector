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

            if (empty($product['prices']['DEFAULT'])) {
                $product['prices']['DEFAULT'] = $this->getDefaultPrice($product['prices'], $product['price']);
            }
        }

        return $result;
    }

    /**
     * @param array $prices
     * @param int $price
     *
     * @return int
     */
    protected function getDefaultPrice(array $prices, int $price): int
    {
        $iso = $this->getCurrencyIso();
        if (array_key_exists($iso, $prices)) {
            foreach ($prices[$iso] as $mode => $priceData) {
                if (is_array($priceData) && empty($priceData['DEFAULT']) === false) {
                    return $priceData['DEFAULT'];
                }
            }
        }

        return $price;
    }

    /**
     * @return string
     */
    protected function getCurrencyIso(): string
    {
        if ($this->currentCurrencyIso === null) {
            $this->currentCurrencyIso = $this->getFactory()->getCurrentCurrency()->getCode();
        }

        return $this->currentCurrencyIso;
    }
}
