<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Test\Unit\Helper;

use Infrangible\CatalogProductCustomerPrice\Helper\Cache;
use Infrangible\CatalogProductCustomerPrice\Helper\Data;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests the customer prices of the codes of quote item options.
 */
class DataTest extends TestCase
{
    /**
     * Returns codes of quote item options and the ID of the customer price.
     *
     * @return array
     * @phpstan-return array<string, array{mixed, int|null}>
     */
    public static function optionCodeProvider(): array
    {
        return [
            'customer price'          => ['customer_price_12', 12],
            'before version 4.0'      => ['12', 12],
            'number'                  => [12, 12],
            'other calculation'       => ['pcb_5f2b', null],
            'other prefix'            => ['customer_benefit_12', null],
            'zero'                    => ['customer_price_0', null],
            'no option'               => [null, null]
        ];
    }

    /**
     * Tests the ID of the customer price of a quote item option code.
     *
     * @param mixed $code
     * @param int|null $expectedId
     *
     * @return void
     * @dataProvider optionCodeProvider
     */
    #[DataProvider('optionCodeProvider')]
    public function testGetCustomerPriceIdByOptionCode(mixed $code, ?int $expectedId): void
    {
        $helper = new Data($this->createStub(Cache::class));

        $this->assertSame($expectedId, $helper->getCustomerPriceIdByOptionCode($code));
    }
}
