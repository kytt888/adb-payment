<?php

namespace Tests\Unit\Model\Adminhtml\Source;

use PHPUnit\Framework\TestCase;

use MercadoPago\PaymentMagento\Tests\Unit\Mocks\Gateway\Config\PaymentMethodsResponseMock;
use MercadoPago\PaymentMagento\Tests\Unit\Mocks\Model\Adminhtml\Source\PaymentMethodsOff\MountPaymentMethodsOffMock;
use MercadoPago\PaymentMagento\Tests\Unit\Mocks\Model\Adminhtml\Source\PaymentMethodsOff\ToOptionArrayMock;

use MercadoPago\PaymentMagento\Gateway\Config\ConfigPaymentMethodsOff;
use MercadoPago\PaymentMagento\Gateway\Config\Config as MercadoPagoConfig;
use MercadoPago\PaymentMagento\Model\Adminhtml\Source\PaymentMethodsOff;
use Magento\Framework\App\RequestInterface;

class PaymentMethodsOffTest extends TestCase {
    
    /**
     * @var paymentMethodsOff
     */
    private $paymentMethodsOff;

    /**
     * @var PaymentMethodsOffMock
     */
    private $paymentMethodsOffMock;

    /**
     * @var RequestInterface
     */
    private $requestMock;
     /**
     * @var MercadoPagoConfig
     */
    private $mercadopagoConfigMock;

    public function setUp(): void
    {
        $this->mercadopagoConfigMock = $this->getMockBuilder(MercadoPagoConfig::class)->disableOriginalConstructor()->getMock();

        $this->requestMock = $this->getMockBuilder(RequestInterface::class)->disableOriginalConstructor()->getMock();
        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->with('store', 0)
            ->willReturn(1);
        
        $this->paymentMethodsOffMock = $this->getMockBuilder(PaymentMethodsOff::class)->setConstructorArgs([
            'request' => $this->requestMock,
            'mercadopagoConfig' => $this->mercadopagoConfigMock
        ])->getMock();

        $this->paymentMethodsOff = new PaymentMethodsOff(
            $this->requestMock,
            $this->mercadopagoConfigMock
        );
    }


    /**
     * Tests function toOptionArray()
     */

    public function testToOptionArrayJustDefaultValue(): void
    {        
        $this->mercadopagoConfigMock->expects($this->any())
            ->method('getMpPaymentMethods')
            ->with(1)
            ->willReturn(PaymentMethodsResponseMock::SUCCESS_FALSE);

        $this->paymentMethodsOff = new PaymentMethodsOff(
            $this->requestMock,
            $this->mercadopagoConfigMock
        );

        $result = $this->paymentMethodsOff->toOptionArray();

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('value', $result[0]);
        $this->assertArrayHasKey('label', $result[0]);
        $this->assertNull($result[0]['value']);
    }

    public function testToOptionArrayWithoutPaymentPlaces(): void
    {
        $this->mercadopagoConfigMock->expects($this->any())
            ->method('getMpPaymentMethods')
            ->with(1)
            ->willReturn(PaymentMethodsResponseMock::WITHOUT_PAYMENT_PLACES);

        $this->paymentMethodsOff = new PaymentMethodsOff(
            $this->requestMock,
            $this->mercadopagoConfigMock
        );

        $result = $this->paymentMethodsOff->toOptionArray();

        $this->assertEquals(ToOptionArrayMock::EXPECTED_WITHOUT_PAYMENT_PLACES, $result);
    }

    public function testToOptionArrayWithPaymentPlaces(): void
    {
        $this->mercadopagoConfigMock->expects($this->any())
            ->method('getMpPaymentMethods')
            ->with(1)
            ->willReturn(PaymentMethodsResponseMock::WITH_PAYMENT_PLACES);

        $this->paymentMethodsOff = new PaymentMethodsOff(
            $this->requestMock,
            $this->mercadopagoConfigMock
        );

        $result = $this->paymentMethodsOff->toOptionArray();

        $this->assertEquals(ToOptionArrayMock::EXPECTED_WITH_PAYMENT_PLACES, $result);
    }

    public function testToOptionArrayWithoutPaymentPlacesAndWithInactive(): void
    {
        $this->mercadopagoConfigMock->expects($this->any())
            ->method('getMpPaymentMethods')
            ->with(1)
            ->willReturn(PaymentMethodsResponseMock::WITHOUT_PAYMENT_PLACES_AND_WITH_INACTIVE);

        $this->paymentMethodsOff = new PaymentMethodsOff(
            $this->requestMock,
            $this->mercadopagoConfigMock
        );

        $result = $this->paymentMethodsOff->toOptionArray();

        $this->assertEquals(ToOptionArrayMock::EXPECTED_WITHOUT_PAYMENT_PLACES_AND_WITH_INACTIVE, $result);
    }

    public function testToOptionArrayWithPaymentPlacesAndInactive(): void
    {
        $this->mercadopagoConfigMock->expects($this->any())
            ->method('getMpPaymentMethods')
            ->with(1)
            ->willReturn(PaymentMethodsResponseMock::WITH_PAYMENT_PLACES_AND_INACTIVE);

        $this->paymentMethodsOff = new PaymentMethodsOff(
            $this->requestMock,
            $this->mercadopagoConfigMock
        );

        $result = $this->paymentMethodsOff->toOptionArray();

        $this->assertEquals(ToOptionArrayMock::EXPECTED_WITH_PAYMENT_PLACES_AND_INACTIVE, $result);
    }

    /**
     * Tests function mountPaymentMethodsOff()
     */

    public function testMountPaymentMethodsOffEmpty(): void
    {        
        $result = $this->paymentMethodsOff->mountPaymentMethodsOff([]);

        $this->assertEmpty($result);
    }
 
    public function testMountPaymentMethodsOffWithoutPaymentPlaces(): void
    {
        $response = PaymentMethodsResponseMock::WITHOUT_PAYMENT_PLACES['response'];
        $result = $this->paymentMethodsOff->mountPaymentMethodsOff($response);

        $this->assertEquals(MountPaymentMethodsOffMock::EXPECTED_WITHOUT_PAYMENT_PLACES, $result);
    }
 
    public function testMountPaymentMethodsOffWithPaymentPlaces(): void
    {
        $response = PaymentMethodsResponseMock::WITH_PAYMENT_PLACES['response'];
        $result = $this->paymentMethodsOff->mountPaymentMethodsOff($response);

        $this->assertEquals(MountPaymentMethodsOffMock::EXPECTED_WITH_PAYMENT_PLACES, $result);
    }
 
    public function testMountPaymentMethodsOffWithoutPaymentPlacesAndWithInactive(): void
    {
        $response = PaymentMethodsResponseMock::WITHOUT_PAYMENT_PLACES_AND_WITH_INACTIVE['response'];
        $result = $this->paymentMethodsOff->mountPaymentMethodsOff($response);

        $this->assertEquals(MountPaymentMethodsOffMock::EXPECTED_WITHOUT_PAYMENT_PLACES_AND_WITH_INACTIVE, $result);
    }
 
    public function testMountPaymentMethodsOffWithPaymentPlacesAndInactive(): void
    {
        $response = PaymentMethodsResponseMock::WITH_PAYMENT_PLACES_AND_INACTIVE['response'];
        $result = $this->paymentMethodsOff->mountPaymentMethodsOff($response);

        $this->assertEquals(MountPaymentMethodsOffMock::EXPECTED_WITH_PAYMENT_PLACES_AND_INACTIVE, $result);
    }
}