<?php

declare(strict_types=1);

namespace App\Domain\Models\Fungi;

use App\Domain\Models\BaseModel;

class HabitatModel extends BaseModel
{
    public function getAllHabitats(): array
    {
        return $this->fetchAll("SELECT * FROM habitats");
    }

    public function getHabitatById(int $id): ?array
    {
        return $this->fetchSingle(
            "SELECT * FROM habitats WHERE habitat_id = :id LIMIT 1",
            ['id' => $id]
        ) ?: null;
    }

    public function getHabitatsByFungi(int $fungiId): array
    {
        return $this->fetchAll(
            "SELECT * FROM fungi_habitats WHERE fungi_id = :id",
            ['id' => $fungiId]
        );
    }
}
