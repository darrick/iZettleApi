<?php

declare(strict_types=1);

namespace LauLamanApps\IzettleApi\Client\Inventory;

use LauLamanApps\IzettleApi\API\Inventory\Inventory;
use LauLamanApps\IzettleApi\API\Inventory\Location\TypeEnum;

use Ramsey\Uuid\Uuid;

final class InventoryBuilder implements InventoryBuilderInterface
{
    /**
     * @return Inventory[]
     */
    public function buildFromJsonArray(string $json): array
    {
        $data = json_decode($json, true);

        $Inventories = [];

        foreach ($data as $InventoryData){
            $Inventories[] = $this->build($InventoryData);
        }

        return $Inventories;
    }

    public function buildFromJson(string $json): Inventory
    {
        return $this->build(json_decode($json, true));
    }

    private function build($data): Inventory
    {
        $Balances = [];
        /*
        foreach ($data['variants'] as $BalanceData) {
            $categories[] = $this->BalanceBuilder->buildFromArray($BalanceData);
        }
        */

        return new Inventory(Uuid::fromString($data['inventoryUuid']), $data['name'], $data['description'], TypeEnum::get($data['inventoryType']), $data['defaultInventory']);
    }
}
