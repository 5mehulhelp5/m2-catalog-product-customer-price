<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Test\Unit\Model\ResourceModel;

use Infrangible\CatalogProductCustomerPrice\Model\ProductCustomerPrice;
use Infrangible\CatalogProductCustomerPrice\Model\ResourceModel\ProductCustomerPrice as PriceResource;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;

/**
 * Tests the checks and the cleanup of the values before a customer price is saved.
 */
#[AllowMockObjectsWithoutExpectations]
class ProductCustomerPriceTest extends TestCase
{
    /**
     * Returns values that are saved, with the saved price, discount and limit.
     *
     * @return array
     * @phpstan-return array<string, array{array<string, mixed>, array{mixed, mixed, mixed}}>
     */
    public static function validProvider(): array
    {
        return [
            'price'                  => [['price' => '9.5', 'discount' => '', 'limit' => ''], [9.5, null, null]],
            'free'                   => [['price' => '0', 'discount' => null, 'limit' => '0'], [0., null, null]],
            'discount'               => [['price' => '', 'discount' => '15', 'limit' => '3'], [null, 15, 3]],
            'price and discount 0'   => [['price' => '5', 'discount' => '0'], [5., null, null]]
        ];
    }

    /**
     * Tests the saved values.
     *
     * @param array $data
     * @param array $expected
     * @phpstan-param array<string, mixed> $data
     * @phpstan-param array{mixed, mixed, mixed} $expected
     *
     * @return void
     * @throws ReflectionException
     * @dataProvider validProvider
     */
    #[DataProvider('validProvider')]
    public function testBeforeSave(array $data, array $expected): void
    {
        $object = $this->beforeSave($data);

        $this->assertSame(
            $expected,
            [$object->getData('price'), $object->getData('discount'), $object->getData('limit')]
        );
        $this->assertNotNull($object->getData('created_at'));
        $this->assertNotNull($object->getData('updated_at'));
    }

    /**
     * Returns values that are not saved.
     *
     * @return array
     * @phpstan-return array<string, array{array<string, mixed>}>
     */
    public static function invalidProvider(): array
    {
        return [
            'neither'           => [['price' => '', 'discount' => '']],
            'both'              => [['price' => '5', 'discount' => '10']],
            'negative price'    => [['price' => '-1']],
            'discount above'    => [['discount' => '101']],
            'not a number'      => [['price' => 'abc']],
            'negative limit'    => [['price' => '5', 'limit' => '-2']]
        ];
    }

    /**
     * Tests that invalid values are not saved.
     *
     * @param array $data
     * @phpstan-param array<string, mixed> $data
     *
     * @return void
     * @throws ReflectionException
     * @dataProvider invalidProvider
     */
    #[DataProvider('invalidProvider')]
    public function testBeforeSaveWithInvalidValues(array $data): void
    {
        $this->expectException(LocalizedException::class);

        $this->beforeSave($data);
    }

    /**
     * Calls the checks before saving with the data.
     *
     * @param array $data
     * @phpstan-param array<string, mixed> $data
     *
     * @return ProductCustomerPrice
     * @throws ReflectionException
     */
    private function beforeSave(array $data): ProductCustomerPrice
    {
        $resource = $this->getMockBuilder(PriceResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $object = $this->getMockBuilder(ProductCustomerPrice::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $object->setData($data);

        (new ReflectionMethod(PriceResource::class, '_beforeSave'))->invoke($resource, $object);

        return $object;
    }
}
