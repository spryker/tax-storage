<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ClientUnit\Spryker\Client\Cart;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Client\Cart\Session\QuoteSessionInterface;
use Spryker\Client\Cart\Zed\CartStubInterface;

/**
 * @group Spryker
 * @group Client
 * @group Cart
 * @group Service
 * @group CartClient
 */
class CartClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testGetCartMustReturnInstanceOfQuoteTransfer()
    {
        $quoteTransfer = new QuoteTransfer();
        $sessionMock = $this->getSessionMock();
        $sessionMock->expects($this->once())
            ->method('getQuote')
            ->will($this->returnValue($quoteTransfer));

        $factoryMock = $this->getFactoryMock($sessionMock);
        $cartClientMock = $this->getCartClientMock($factoryMock);

        $this->assertSame($quoteTransfer, $cartClientMock->getQuote());
    }

    /**
     * @return void
     */
    public function testClearCartMustSetItemCountInSessionToZero()
    {
        $sessionMock = $this->getSessionMock();
        $sessionMock->expects($this->once())
            ->method('clearQuote')
            ->will($this->returnValue($sessionMock));

        $factoryMock = $this->getFactoryMock($sessionMock);
        $cartClientMock = $this->getCartClientMock($factoryMock);

        $cartClientMock->clearQuote();
    }

    /**
     * @return void
     */
    public function testClearCartMustSetCartTransferInSessionToAnEmptyInstance()
    {
        $sessionMock = $this->getSessionMock();
        $sessionMock->expects($this->once())
            ->method('clearQuote')
            ->will($this->returnValue($sessionMock));

        $factoryMock = $this->getFactoryMock($sessionMock);
        $cartClientMock = $this->getCartClientMock($factoryMock);

        $cartClientMock->clearQuote();
    }

    /**
     * @return void
     */
    public function testGetItemCountMustReturnItemCountFromSession()
    {
        $sessionMock = $this->getSessionMock();
        $sessionMock->expects($this->once())
            ->method('getItemCount')
            ->will($this->returnValue(0));

        $factoryMock = $this->getFactoryMock($sessionMock);
        $cartClientMock = $this->getCartClientMock($factoryMock);

        $this->assertSame(0, $cartClientMock->getItemCount());
    }

    /**
     * @return void
     */
    public function testAddItemMustOnlyExceptTransferInterfaceAsArgument()
    {
        $itemTransfer = new ItemTransfer();
        $quoteTransfer = new QuoteTransfer();
        $sessionMock = $this->getSessionMock();
        $sessionMock->expects($this->once())
            ->method('getQuote')
            ->will($this->returnValue($quoteTransfer));

        $stubMock = $this->getStubMock();
        $stubMock->expects($this->once())
            ->method('addItem')
            ->will($this->returnValue($quoteTransfer));

        $factoryMock = $this->getFactoryMock($sessionMock, $stubMock);
        $cartClientMock = $this->getCartClientMock($factoryMock);

        $quoteTransfer = $cartClientMock->addItem($itemTransfer);

        $this->assertInstanceOf('Generated\Shared\Transfer\QuoteTransfer', $quoteTransfer);
    }


    /**
     * @return void
     */
    public function testChangeItemQuantityMustCallRemoveItemQuantityWhenPassedItemQuantityIsLowerThenInCartGivenItem()
    {
        $itemTransfer = new ItemTransfer();
        $itemTransfer->setQuantity(2);
        $itemTransfer->setSku('sku');

        $quoteTransfer = new QuoteTransfer();
        $quoteTransfer->addItem($itemTransfer);

        $sessionMock = $this->getSessionMock();
        $sessionMock->expects($this->exactly(3))
            ->method('getQuote')
            ->will($this->returnValue($quoteTransfer));

        $stubMock = $this->getStubMock();
        $stubMock->expects($this->once())
            ->method('removeItem')
            ->will($this->returnValue($quoteTransfer));
        $stubMock->expects($this->never())
            ->method('addItem')
            ->will($this->returnValue($quoteTransfer));

        $factoryMock = $this->getFactoryMock($sessionMock, $stubMock);
        $cartClientMock = $this->getCartClientMock($factoryMock);

        $itemTransfer = new ItemTransfer();
        $itemTransfer->setSku('sku');

        $quoteTransfer = $cartClientMock->changeItemQuantity('sku', null, 1);

        $this->assertInstanceOf('Generated\Shared\Transfer\QuoteTransfer', $quoteTransfer);
    }

    /**
     * @return void
     */
    public function testChangeItemQuantityMustCallAddItemQuantityWhenPassedItemQuantityIsLowerThenInCartGivenItem()
    {
        $itemTransfer = new ItemTransfer();
        $itemTransfer->setQuantity(1);
        $itemTransfer->setSku('sku');

        $quoteTransfer = new QuoteTransfer();
        $quoteTransfer->addItem($itemTransfer);

        $sessionMock = $this->getSessionMock();
        $sessionMock->expects($this->exactly(3))
            ->method('getQuote')
            ->will($this->returnValue($quoteTransfer));

        $stubMock = $this->getStubMock();
        $stubMock->expects($this->never())
            ->method('removeItem')
            ->will($this->returnValue($quoteTransfer));

        $stubMock->expects($this->once())
            ->method('addItem')
            ->will($this->returnValue($quoteTransfer));

        $factoryMock = $this->getFactoryMock($sessionMock, $stubMock);
        $cartClientMock = $this->getCartClientMock($factoryMock);

        $itemTransfer = new ItemTransfer();
        $itemTransfer->setSku('sku');

        $quoteTransfer = $cartClientMock->changeItemQuantity('sku', null, 2);

        $this->assertInstanceOf('Generated\Shared\Transfer\QuoteTransfer', $quoteTransfer);
    }

    /**
     * @param \Spryker\Client\Cart\Session\QuoteSessionInterface|null $cartSession
     * @param \Spryker\Client\Cart\Zed\CartStubInterface|null $cartStub
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getFactoryMock(
        QuoteSessionInterface $cartSession = null,
        CartStubInterface $cartStub = null
    ) {
        $factoryMock = $this->getMock(
            'Spryker\Client\Kernel\AbstractFactory',
            ['createSession', 'createZedStub'],
            [],
            '',
            false
        );

        if ($cartSession !== null) {
            $factoryMock->expects($this->any())
                ->method('createSession')
                ->will($this->returnValue($cartSession));
        }
        if ($cartStub !== null) {
            $factoryMock->expects($this->any())
                ->method('createZedStub')
                ->will($this->returnValue($cartStub));
        }

        return $factoryMock;
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $factoryMock
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Client\Cart\CartClient
     */
    private function getCartClientMock($factoryMock)
    {
        $cartClientMock = $this->getMock(
            'Spryker\Client\Cart\CartClient',
            ['getFactory'],
            [],
            '',
            false
        );

        $cartClientMock->expects($this->any())
            ->method('getFactory')
            ->will($this->returnValue($factoryMock));

        return $cartClientMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getSessionMock()
    {
        $sessionMock = $this->getMock('Spryker\Client\Cart\Session\QuoteSessionInterface', [
            'getQuote',
            'setQuote',
            'getItemCount',
            'setItemCount',
            'clearQuote',
        ]);

        return $sessionMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Client\Cart\Zed\CartStubInterface
     */
    private function getStubMock()
    {
        return $this->getMock('Spryker\Client\Cart\Zed\CartStubInterface', [
            'addItem',
            'removeItem',
            'storeQuote',
        ]);
    }

}
