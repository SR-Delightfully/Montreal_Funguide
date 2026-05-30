<?php

declare(strict_types=1);

namespace App\Domain\Models\Fungi;

use App\Domain\Models\BaseModel;

class FungiModel extends BaseModel
{
    public function getAllFungi(): array
    {
        $sql = "
            SELECT *
            FROM fungi
            ORDER BY fungi_id DESC
        ";

        return $this->fetchAll($sql);
    }

    public function getFungiById(int $id): ?array
    {
        $sql = "
            SELECT *
            FROM fungi
            WHERE fungi_id = :id
            LIMIT 1
        ";

        return $this->fetchSingle($sql, ['id' => $id]) ?: null;
    }

    public function createFungi(array $data): string
    {
        return $this->insert('fungi', $data);
    }

    public function updateFungi(int $id, array $data): int
    {
        return $this->update('fungi', $data, [
            'fungi_id' => $id
        ]);
    }

    public function deleteFungi(int $id): int
    {
        return $this->delete('fungi', [
            'fungi_id' => $id
        ]);
    }
}
