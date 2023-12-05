<?php

namespace LauLamanApps\IzettleApi\Client\Inventory;

use LauLamanApps\IzettleApi\API\Inventory\Inventory;

interface InventoryBuilderInterface
{
    /**
     * @return Inventory[]
     */
    public function buildFromJsonArray(string $json): array;

    public function buildFromJson(string $json): Inventory;
}
