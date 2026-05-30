<?php

declare(strict_types=1);

namespace App\Domain\Models\Fungi;

use App\Domain\Models\BaseModel;

class SpeciesModel extends BaseModel
{
    public function getAllSpecies(): array
    {
        return $this->fetchAll("SELECT * FROM species");
    }

    public function getSpeciesById(int $id): ?array
    {
        return $this->fetchSingle(
            "SELECT * FROM species WHERE species_id = :id LIMIT 1",
            ['id' => $id]
        ) ?: null;
    }

    public function getSpeciesByFungi(int $fungiId): array
    {
        return $this->fetchAll(
            "SELECT * FROM species WHERE fungi_id = :id",
            ['id' => $fungiId]
        );
    }
}
