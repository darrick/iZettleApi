<?php

declare(strict_types=1);

namespace LauLamanApps\iZettleApi\Client\Purchase;

use LauLamanApps\iZettleApi\API\Image;
use LauLamanApps\iZettleApi\API\Purchase\Product;
use Money\Currency;
use Money\Money;
use Ramsey\Uuid\Uuid;

final class ProductParser
{
    /**
     * @return Product[]
     */
    public static function parseArray(array $products, Currency $currency): array
    {
        $data = [];

        foreach ($products as $product) {
            $data[] = self::parse($product, $currency);
        }

        return $data;
    }

    private static function parse(array $product, Currency $currency): Product
    {
        extract($product);

        return new Product(
            isset($productUuid) ? Uuid::fromString($productUuid) : null,
            isset($variantUuid) ? Uuid::fromString($variantUuid) : null,
            isset($name) ? $name : null,
            isset($variantName) ? $variantName : null,
            isset($quantity) ? (int) $quantity : null,
            isset($unitPrice) ? new Money($unitPrice, $currency) : null,
            isset($vatPercentage) ? $vatPercentage : null,
            isset($rowTaxableAmount) ? new Money($rowTaxableAmount, $currency) : null,
            isset($imageLookupKey) ? new Image($imageLookupKey) : null,
            isset($autoGenerated) ? $autoGenerated : false,
            isset($libraryProduct) ? $libraryProduct : false
        );
    }
}
