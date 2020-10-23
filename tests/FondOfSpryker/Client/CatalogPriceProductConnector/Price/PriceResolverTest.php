<?php

namespace FondOfSpryker\Client\CatalogPriceProductConnector\Price;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CurrencyTransfer;
use Spryker\Client\CatalogPriceProductConnector\Dependency\CatalogPriceProductConnectorToCurrencyClientInterface;
use Spryker\Client\CatalogPriceProductConnector\Dependency\CatalogPriceProductConnectorToPriceClientInterface;

class PriceResolverTest extends Unit
{
    /**
     * @var \Spryker\Client\CatalogPriceProductConnector\Dependency\CatalogPriceProductConnectorToPriceClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $priceClientMock;

    /**
     * @var \Spryker\Client\CatalogPriceProductConnector\Dependency\CatalogPriceProductConnectorToCurrencyClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $currencyClientMock;

    /**
     * @var \Generated\Shared\Transfer\CurrencyTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $currencyTransferMock;

    /**
     * @var \FondOfSpryker\Client\CatalogPriceProductConnector\Price\ResolverInterface
     */
    protected $resolver;

    /**
     * @return void
     */
    public function _before()
    {
        parent::_before();
        $this->priceClientMock = $this->getMockBuilder(CatalogPriceProductConnectorToPriceClientInterface::class)->disableOriginalConstructor()->getMock();
        $this->currencyClientMock = $this->getMockBuilder(CatalogPriceProductConnectorToCurrencyClientInterface::class)->disableOriginalConstructor()->getMock();
        $this->currencyTransferMock = $this->getMockBuilder(CurrencyTransfer::class)->disableOriginalConstructor()->getMock();

        $this->resolver = new PriceResolver($this->priceClientMock, $this->currencyClientMock);
    }

    /**
     * @return void
     */
    public function testResolveNothingToDo(): void
    {
        $data = [
            'prices' => ['DEFAULT' => 100],
        ];

        $this->assertSame($data, $this->resolver->resolve($data));
    }

    /**
     * @return void
     */
    public function testResolveWithPriceAsFallback(): void
    {
        $this->priceClientMock->expects($this->once())->method('getCurrentPriceMode')->willReturn('GROSS');
        $this->currencyClientMock->expects($this->once())->method('getCurrent')->willReturn($this->currencyTransferMock);
        $this->currencyTransferMock->expects($this->once())->method('getCode')->willReturn('EUR');
        $data = [
            'price' => 100,
            'prices' => ['EUR' => ['GROSS' => 110]],
        ];

        $expectedData = [
            'price' => 100,
            'prices' => ['DEFAULT' => 100],
        ];

        $this->assertSame($expectedData, $this->resolver->resolve($data));
    }

    /**
     * @return void
     */
    public function testResolve(): void
    {
        $this->priceClientMock->expects($this->once())->method('getCurrentPriceMode')->willReturn('GROSS');
        $this->currencyClientMock->expects($this->once())->method('getCurrent')->willReturn($this->currencyTransferMock);
        $this->currencyTransferMock->expects($this->once())->method('getCode')->willReturn('EUR');
        $data = [
            'price' => 100,
            'prices' => ['EUR' => ['GROSS' => ['DEFAULT' => 110]]],
        ];

        $expectedData = [
            'price' => 100,
            'prices' => ['DEFAULT' => 110],
        ];

        $this->assertSame($expectedData, $this->resolver->resolve($data));
    }
}
