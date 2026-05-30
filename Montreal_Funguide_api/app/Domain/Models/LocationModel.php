<?php

declare(strict_types=1);

namespace App\Domain\Models\Map;

use App\Domain\Models\BaseModel;

class LocationModel extends BaseModel
{
    public function getAll(): array
    {
        return $this->fetchAll("SELECT * FROM locations");
    }

    public function getById(int $id): array|false
    {
        return $this->fetchSingle(
            "SELECT * FROM locations WHERE id = :id",
            ["id" => $id]
        );
    }

    public function create(array $data): string
    {
        return $this->insert("locations", $data);
    }

    public function updateById(int $id, array $data): int
    {
        return $this->update("locations", $data, ["id" => $id]);
    }

    public function deleteById(int $id): int
    {
        return $this->delete("locations", ["id" => $id]);
    }
}
