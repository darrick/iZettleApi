<?php

declare(strict_types=1);

namespace LauLamanApps\IzettleApi\Client;

use LauLamanApps\IzettleApi\API\Inventory\LocationInventory;
use LauLamanApps\IzettleApi\API\Inventory\ProductBalance;
use LauLamanApps\IzettleApi\API\Inventory\VariantChangeHistory;
use LauLamanApps\IzettleApi\API\Product\Product;
use LauLamanApps\IzettleApi\Client\Filter\Inventory\HistoryFilter;
use LauLamanApps\IzettleApi\Client\Inventory\VariantChangeHistoryBuilderInterface;
use LauLamanApps\IzettleApi\Client\Inventory\LocationInventoryBuilderInterface;
use LauLamanApps\IzettleApi\Client\Inventory\InventoryBuilderInterface;
use LauLamanApps\IzettleApi\Client\Inventory\Post\StartTrackingRequest;
use LauLamanApps\IzettleApi\Client\Inventory\ProductBalanceBuilderInterface;
use LauLamanApps\IzettleApi\Exception\UnprocessableEntityException;
use LauLamanApps\IzettleApi\IzettleClientInterface;
use Ramsey\Uuid\UuidInterface;

use LauLamanApps\IzettleApi\Client\Inventory\InventoryBuilder;
use LauLamanApps\IzettleApi\Client\Inventory\LocationInventoryBuilder;
use LauLamanApps\IzettleApi\Client\Inventory\ProductBalanceBuilder;
use LauLamanApps\IzettleApi\Client\Inventory\VariantChangeHistoryBuilder;

final class InventoryClient
{
    private const DEFAULT_ORGANIZATION_UUID = 'self';

    const BASE_URL = 'https://inventory.izettle.com/v3';

    const GET_STOCK = self::BASE_URL . '/stock';
    const GET_STOCK_UPDATE = self::BASE_URL . '/stock/updates';
    const GET_STOCK_BALANCE = self::BASE_URL . '/stock/%s';
    const GET_STOCK_PRODUCT_BALANCE = self::BASE_URL . '/stock/%s/products/%s';
    const POST_STOCK_PRODUCT_BALANCE = self::BASE_URL . '/stock/%s/products';

    const GET_ALL_INVENTORIES = self::BASE_URL . '/inventories';
    const POST_INVENTORY = self::BASE_URL . '/inventories';
    CONST GET_INVENTORY = self::BASE_URL . '/inventories/%s';

    const GET_PRODUCTS = self::BASE_URL . '/products';
    const POST_PRODUCTS = self::BASE_URL . '/products';
    const POST_PRODUCTS_STATUS = self::BASE_URL . '/products/status';

    const POST_LOW_STOCK = self::BASE_URL . '/custom-low-stock';

    /**
     * @var IzettleClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $organizationUuid;

    /**
     * @var InventoryBuilderInterface
     */
    private $inventoryBuilder;

    /**
     * @var LocationInventoryBuilderInterface
     */
    private $locationInventoryBuilder;

    /**
     * @var ProductBalanceBuilderInterface
     */
    private $productBalanceBuilder;

    /**
     * @var VariantChangeHistoryBuilderInterface
     */
    private $variantChangeHistoryBuilder;

    public function __construct(
        IzettleClientInterface $client,
        ?UuidInterface $organizationUuid = null,
        ?InventoryBuilderInterface $inventoryBuilder = null,
        ?LocationInventoryBuilderInterface $locationInventoryBuilder = null,
        ?ProductBalanceBuilderInterface $productBalanceBuilder = null,
        ?VariantChangeHistoryBuilderInterface $variantChangeHistoryBuilder = null
    )
    {
        $this->client = $client;
        $this->organizationUuid = $organizationUuid ? $organizationUuid->toString() : self::DEFAULT_ORGANIZATION_UUID;
        $this->inventoryBuilder = $inventoryBuilder ?? new InventoryBuilder();
        $this->locationInventoryBuilder = $locationInventoryBuilder ?? new LocationInventoryBuilder();
        $this->productBalanceBuilder = $productBalanceBuilder ?? new ProductBalanceBuilder();
        $this->variantChangeHistoryBuilder = $variantChangeHistoryBuilder ?? new VariantChangeHistoryBuilder();
    }

    public function setOrganizationUuid(UuidInterface $organizationUuid): void
    {
        $this->organizationUuid = $organizationUuid->toString();
    }

    public function resetOrganizationUuid(): void
    {
        $this->organizationUuid = self::DEFAULT_ORGANIZATION_UUID;
    }

    /**
     * @return Inventory[]
     */
    public function getAllInventories(): array
    {
        $url = sprintf(self::GET_ALL_INVENTORIES);
        $json = $this->client->getJson($this->client->get($url, null));

        return $this->inventoryBuilder->buildFromJsonArray($json);
    }

    /**
     * @return LocationInventory[]
     */
    public function getLocationInventories(): array
    {
        $url = sprintf(self::GET_ALL_INVENTORIES);
        $json = $this->client->getJson($this->client->get($url, null));

        return $this->locationInventoryBuilder->buildFromJsonArray($json);
    }

    public function getLocationInventory(UuidInterface $locationUuid): LocationInventory
    {
        $url = sprintf(self::GET_LOCATION, $this->organizationUuid, $locationUuid->toString());
        $json = $this->client->getJson($this->client->get($url, null));

        return $this->locationInventoryBuilder->buildFromJson($json);
    }

    public function getProductInventory(UuidInterface $locationUuid, UuidInterface $productUuid): ProductBalance
    {
        $url = sprintf(self::GET_PRODUCT_INVENTORY, $this->organizationUuid, $locationUuid->toString(), $productUuid->toString());
        $json = $this->client->getJson($this->client->get($url, null));

        return $this->productBalanceBuilder->buildFromJson($json);
    }

    /**
     * @throws UnprocessableEntityException
     */
    public function trackInventory(Product $product): ProductBalance
    {
        $url = sprintf(self::POST_INVENTORY, $this->organizationUuid);
        $json = $this->client->getJson($this->client->post($url, new StartTrackingRequest($product)));

        return $this->productBalanceBuilder->buildFromJson($json);
    }

    /**
     * @return VariantChangeHistory[]
     */
    public function getHistory(UuidInterface $locationUuid): array
    {
        $url = sprintf(self::GET_HISTORY, $this->organizationUuid, $locationUuid->toString());
        $json = $this->client->getJson($this->client->get($url, new HistoryFilter()));

        return $this->variantChangeHistoryBuilder->buildFromJson($json);
    }
}
