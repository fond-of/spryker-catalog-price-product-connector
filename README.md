# Catalog Price Product Connector
[![Build Status](https://travis-ci.org/fond-of/spryker-catalog-price-product-connector.svg?branch=master)](https://travis-ci.org/fond-of/spryker-catalog-price-product-connector)
[![PHP from Travis config](https://img.shields.io/travis/php-v/fond-of/spryker-catalog-price-product-connector.svg)](https://php.net/)
[![license](https://img.shields.io/github/license/fond-of/spryker-catalog-price-product-connector.svg)](https://packagist.org/packages/fond-of-spryker/catalog-price-product-connector)

## What it does

* It extends the CurrencyAwareCatalogSearchResultFormatterPlugin
* changed from getting every price from redis to getting price only if no one available in elastic from redis


## Installation

```
composer require fond-of-spryker/catalog-price-product-connector
```

Add or replace `CurrencyAwareCatalogSearchResultFormatterPlugin` in `Pyz\Client\Catalog`

```
    /**
     * @return \Spryker\Client\Search\Dependency\Plugin\ResultFormatterPluginInterface[]
     */
    protected function createCatalogSearchResultFormatterPlugins(): array
    {
        return [
            ...
            new CurrencyAwareCatalogSearchResultFormatterPlugin(
                new RawCatalogSearchResultFormatterPlugin()
            ),
            ...
        ];
    }
```
