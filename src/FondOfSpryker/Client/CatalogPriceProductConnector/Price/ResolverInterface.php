<?php

namespace FondOfSpryker\Client\CatalogPriceProductConnector\Price;

interface ResolverInterface
{
    /**
     * @param array $data
     *
     * @return array
     */
    public function resolve(array $data): array;
}
