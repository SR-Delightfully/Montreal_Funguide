<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class DatabaseModel
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollback(): void
    {
        $this->pdo->rollBack();
    }

    public function insertSpecies(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO species (
                species_name,
                species_family,
                species_genus,
                species_edibility,
                species_toxicity,
                species_discovery,
                species_gbif_id,
                species_image_url
            ) VALUES (
                :species_name,
                :species_family,
                :species_genus,
                :species_edibility,
                :species_toxicity,
                :species_discovery,
                :species_gbif_id,
                :species_image_url
            )
        ");

        $stmt->execute($data);
        return (int)$this->pdo->lastInsertId();
    }

    public function insertHabitat(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO habitat (
                habitat_type,
                habitat_climate,
                habitat_soil,
                habitat_humindex,
                habitat_desc
            ) VALUES (
                :habitat_type,
                :habitat_climate,
                :habitat_soil,
                :habitat_humindex,
                :habitat_desc
            )
        ");

        $stmt->execute($data);
        return (int)$this->pdo->lastInsertId();
    }

    public function linkSpeciesHabitat(int $speciesId, int $habitatId): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO species_habitat (species_id, habitat_id)
            VALUES (:species_id, :habitat_id)
        ");

        $stmt->execute([
            'species_id' => $speciesId,
            'habitat_id' => $habitatId
        ]);
    }

    public function insertFungi(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO fungi (
                species_id,
                fungi_observation_source,
                fungi_observation_date,
                fungi_notes
            ) VALUES (
                :species_id,
                :fungi_observation_source,
                :fungi_observation_date,
                :fungi_notes
            )
        ");

        $stmt->execute($data);
        return (int)$this->pdo->lastInsertId();
    }

    public function insertFungiLocation(array $data): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO fungi_location (
                fungi_id,
                fungi_location_lat,
                fungi_location_long,
                fungi_location_borough
            ) VALUES (
                :fungi_id,
                :fungi_location_lat,
                :fungi_location_long,
                :fungi_location_borough
            )
        ");

        $stmt->execute($data);
    }

    public function insertRecipe(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO recipe (
                recipe_external_id,
                recipe_name,
                recipe_source,
                recipe_category,
                recipe_area,
                recipe_instructions,
                recipe_thumbnail,
                recipe_tags,
                recipe_difficulty,
                recipe_image_url
            ) VALUES (
                :recipe_external_id,
                :recipe_name,
                :recipe_source,
                :recipe_category,
                :recipe_area,
                :recipe_instructions,
                :recipe_thumbnail,
                :recipe_tags,
                :recipe_difficulty,
                :recipe_image_url
            )
        ");

        $stmt->execute($data);
        return (int)$this->pdo->lastInsertId();
    }

    public function linkRecipeIngredient(array $data): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO recipe_ingredient (
                recipe_id,
                ingredient_id,
                quantity
            ) VALUES (
                :recipe_id,
                :ingredient_id,
                :quantity
            )
        ");

        $stmt->execute($data);
    }

    public function insertIngredient(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO ingredient (
                ingredient_name,
                ingredient_unit
            ) VALUES (
                :ingredient_name,
                :ingredient_unit
            )
        ");

        $stmt->execute($data);
        return (int)$this->pdo->lastInsertId();
    }

    public function insertCalories(array $data): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO calories (
                ingredient_id,
                calories,
                protein,
                fat,
                carbohydrates
            ) VALUES (
                :ingredient_id,
                :calories,
                :protein,
                :fat,
                :carbohydrates
            )
        ");

        $stmt->execute($data);
    }

    public function insertLocation(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO location (
                location_name,
                location_lat,
                location_long,
                location_borough,
                location_addr,
                location_type,
                location_image_url,
                location_accessibility
            ) VALUES (
                :location_name,
                :location_lat,
                :location_long,
                :location_borough,
                :location_addr,
                :location_type,
                :location_image_url,
                :location_accessibility
            )
        ");

        $stmt->execute($data);
        return (int)$this->pdo->lastInsertId();
    }
}
