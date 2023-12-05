<?php

declare(strict_types=1);

namespace LauLamanApps\IzettleApi\API\Inventory;

use LauLamanApps\IzettleApi\API\Inventory\Location\TypeEnum;
use Ramsey\Uuid\UuidInterface;

final class Inventory
{
    private $name;

    private $description;
    /**
     * @var UuidInterface
     */
    private $uuid;

    private $inventoryType;

    private $defaultInventory;



    public function __construct(UuidInterface $uuid, string $name, string $description, TypeEnum $inventoryType, bool $defaultInventory)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->description = $description;
        $this->inventoryType = $inventoryType;
        $this->defaultInventory = $defaultInventory;
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getInventoryType(): TypeEnum
    {
        return $this->inventoryType;
    }

    public function getDefaultInventory(): bool
    {
        return $this->defaultInventory;
    }
}
