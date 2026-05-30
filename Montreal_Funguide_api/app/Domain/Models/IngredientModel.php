<?php

declare(strict_types=1);

namespace App\Domain\Models\Recipe;

use App\Domain\Models\BaseModel;

class IngredientModel extends BaseModel
{
    public function getAll(): array
    {
        return $this->fetchAll("SELECT * FROM ingredients");
    }

    public function getById(int $id): array|false
    {
        return $this->fetchSingle(
            "SELECT * FROM ingredients WHERE id = :id",
            ["id" => $id]
        );
    }

    public function create(array $data): string
    {
        return $this->insert("ingredients", $data);
    }

    public function updateById(int $id, array $data): int
    {
        return $this->update("ingredients", $data, ["id" => $id]);
    }

    public function deleteById(int $id): int
    {
        return $this->delete("ingredients", ["id" => $id]);
    }
}
