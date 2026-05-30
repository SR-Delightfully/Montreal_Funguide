<?php

declare(strict_types=1);

namespace App\Domain\Models\Map;

use App\Domain\Models\BaseModel;

class MapModel extends BaseModel
{
    public function getMapOverview(): array
    {
        return [
            "locations_count" => $this->count("SELECT * FROM locations"),
            "trips_count" => $this->count("SELECT * FROM trips")
        ];
    }
}
