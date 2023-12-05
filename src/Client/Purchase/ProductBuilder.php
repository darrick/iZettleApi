<?php

declare(strict_types=1);

namespace LauLamanApps\IzettleApi\Client\Purchase;

use LauLamanApps\IzettleApi\API\Image;
use LauLamanApps\IzettleApi\API\Purchase\Product;
use Money\Currency;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class ProductBuilder implements ProductBuilderInterface
{
    /**
     * @return Product[]
     */
    public function buildFromArray(array $products, Currency $currency): array
    {
        $data = [];

        foreach ($products as $product) {
            $data[] = $this->build($product, $currency);
        }

        return $data;
    }

    private function build(array $product, Currency $currency): Product
    {
        if ($this->getFromKey('vatPercentage', $product) == null && !empty($this->getFromKey('taxRates', $product))) {
            $tax_rates = $this->getFromKey('taxRates', $product);
            $product['vatPercentage'] = $tax_rates[0]['percentage'];
        }   else {
            $product['vatPercentage'] = 0.0;
        }
        return new Product(
            $this->getUuidFromKey('productUuid', $product),
            $this->getUuidFromKey('variantUuid', $product),
            $this->getFromKey('name', $product),
            $this->getFromKey('variantName', $product),
            $this->getIntFromKey('quantity', $product),
            $this->getMoneyFromKey('unitPrice', $currency, $product),
            $this->getFromKey('vatPercentage', $product),
            $this->getMoneyFromKey('rowTaxableAmount', $currency, $product),
            $this->getImageFromKey('imageLookupKey', $product),
            $this->getFromKey('autoGenerated', $product),
            $this->getFromKey('libraryProduct', $product)
        );
    }

    private function getFromKey($key, array $data)
    {
        if (!array_key_exists($key, $data)) {
            return null;
        }

        return $data[$key];
    }

    private function getIntFromKey(string $key, array $data): int
    {
        return (int) $this->getFromKey($key, $data);
    }

    private function getUuidFromKey(string $key, array $data): ?UuidInterface
    {
        $data = $this->getFromKey($key, $data);
        if (!is_null($data)) {
            return Uuid::fromString($data);
        }

        return $data;
    }

    private function getMoneyFromKey(string $key, Currency $currency, array $data): Money
    {
        return new Money($this->getFromKey($key, $data), $currency);
    }

    private function getImageFromKey(string $key, array $data): ?Image
    {
        $data = $this->getFromKey($key, $data);
        if (!is_null($data)) {
            return new Image($data);
        }

        return $data;
    }
}
