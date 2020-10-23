<?php

namespace FondOfSpryker\Client\CatalogPriceProductConnector\Price;

use Spryker\Client\CatalogPriceProductConnector\Dependency\CatalogPriceProductConnectorToCurrencyClientInterface;
use Spryker\Client\CatalogPriceProductConnector\Dependency\CatalogPriceProductConnectorToPriceClientInterface;

class PriceResolver implements ResolverInterface
{
    protected const PRICE = 'price';
    protected const PRICES = 'prices';
    protected const DEFAULT = 'DEFAULT';
    /**
     * @var \Spryker\Client\CatalogPriceProductConnector\Dependency\CatalogPriceProductConnectorToPriceClientInterface
     */
    protected $priceClient;

    /**
     * @var \Spryker\Client\CatalogPriceProductConnector\Dependency\CatalogPriceProductConnectorToCurrencyClientInterface
     */
    protected $currencyClient;

    /**
     * @var string
     */
    protected $currentCurrencyIsoCode;

    /**
     * @var string
     */
    protected $priceMode;

    /**
     * @param \Spryker\Client\CatalogPriceProductConnector\Dependency\CatalogPriceProductConnectorToPriceClientInterface $priceClient
     * @param \Spryker\Client\CatalogPriceProductConnector\Dependency\CatalogPriceProductConnectorToCurrencyClientInterface $currencyClient
     */
    public function __construct(
        CatalogPriceProductConnectorToPriceClientInterface $priceClient,
        CatalogPriceProductConnectorToCurrencyClientInterface $currencyClient
    ) {
        $this->priceClient = $priceClient;
        $this->currencyClient = $currencyClient;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function resolve(array $data): array
    {
        if (empty($data[static::PRICES][static::DEFAULT]) && array_key_exists(static::PRICE, $data)) {
            $data[static::PRICES] = [
                static::DEFAULT => $this->getDefaultPrice(
                    $data[static::PRICES],
                    $data[static::PRICE]
                ),
            ];
        }

        return $data;
    }

    /**
     * @param array $prices
     * @param int $price
     *
     * @return int
     */
    protected function getDefaultPrice(array $prices, int $price): int
    {
        $iso = $this->getCurrentCurrencyIsoCode();
        $priceMode = $this->getCurrentPriceMode();
        if (
            array_key_exists($iso, $prices)
            && array_key_exists($priceMode, $prices[$iso])
            && is_array($prices[$iso][$priceMode])
            && array_key_exists(static::DEFAULT, $prices[$iso][$priceMode])
        ) {
            return $prices[$iso][$priceMode][static::DEFAULT];
        }

        return $price;
    }

    /**
     * @return string
     */
    protected function getCurrentCurrencyIsoCode(): string
    {
        if ($this->currentCurrencyIsoCode === null) {
            $this->currentCurrencyIsoCode = $this->currencyClient->getCurrent()->getCode();
        }

        return $this->currentCurrencyIsoCode;
    }

    /**
     * @return string
     */
    protected function getCurrentPriceMode(): string
    {
        if ($this->priceMode === null) {
            $this->priceMode = $this->priceClient->getCurrentPriceMode();
        }

        return $this->priceMode;
    }
}
