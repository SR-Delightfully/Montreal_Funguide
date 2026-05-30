<?php

declare(strict_types=1);

namespace App\Domain\Models\Map;

use App\Domain\Models\BaseModel;

class TripModel extends BaseModel
{
    public function getAll(): array
    {
        return $this->fetchAll("SELECT * FROM trips");
    }

    public function getById(int $id): array|false
    {
        return $this->fetchSingle(
            "SELECT * FROM trips WHERE id = :id",
            ["id" => $id]
        );
    }

    public function create(array $data): string
    {
        return $this->insert("trips", $data);
    }

    public function updateById(int $id, array $data): int
    {
        return $this->update("trips", $data, ["id" => $id]);
    }

    public function deleteById(int $id): int
    {
        return $this->delete("trips", ["id" => $id]);
    }
}
