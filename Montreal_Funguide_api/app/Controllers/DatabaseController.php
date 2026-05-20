<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\DatabaseModel;

class DatabaseController
{
    public function __construct(
        private DatabaseModel $model
    ) {}

    public function populate(array $data): void
    {
        echo "Importing species...\n";
        foreach ($data['fungi']['species'] as $species) {
            $this->model->insertSpecies(
                $this->sanitizeSpecies($species)
            );
        }

        echo "Importing recipes...\n";
        foreach ($data['recipes']['recipe'] as $recipe) {
            $this->model->insertRecipe(
                $this->sanitizeRecipe($recipe)
            );
        }

        echo "Importing ingredients...\n";
        foreach ($data['recipes']['ingredient'] as $ingredient) {
            $this->model->insertIngredient(
                $this->sanitizeIngredient($ingredient)
            );
        }

        echo "Importing nutrition...\n";
        foreach ($data['recipes']['calories'] as $calorie) {
            $this->model->insertCalories(
                $this->sanitizeCalories($calorie)
            );
        }

        echo "Importing locations...\n";
        foreach ($data['map']['location'] as $location) {
            $this->model->insertLocation(
                $this->sanitizeLocation($location)
            );
        }

        echo "Database population complete.\n";
    }

    private function sanitizeSpecies(array $data): array
    {
        return [
            'species_name' => trim($data['species_name'] ?? ''),
            'species_family' => trim($data['species_family'] ?? ''),
            'species_genus' => trim($data['species_genus'] ?? ''),
            'species_edibility' => (int)($data['species_edibility'] ?? 0),
            'species_toxicity' => $data['species_toxicity'] ?? 'safe',
            'species_discovery' => $data['species_discovery'] ?? null,
            'species_gbif_id' => $data['species_gbif_id'] ?? null,
            'species_image_url' => $data['species_image_url'] ?? null,
        ];
    }

    private function sanitizeRecipe(array $data): array
    {
        return [
            'recipe_external_id' => $data['recipe_external_id'] ?? null,
            'recipe_name' => trim($data['recipe_name'] ?? ''),
            'recipe_source' => $data['recipe_source'] ?? 'MealDB',
            'recipe_category' => $data['recipe_category'] ?? null,
            'recipe_area' => $data['recipe_area'] ?? null,
            'recipe_instructions' => $data['recipe_instructions'] ?? null,
            'recipe_thumbnail' => $data['recipe_thumbnail'] ?? null,
            'recipe_tags' => $data['recipe_tags'] ?? null,
            'recipe_difficulty' => $data['recipe_difficulty'] ?? 'medium',
            'recipe_image_url' => $data['recipe_image_url'] ?? null,
        ];
    }

    private function sanitizeIngredient(array $data): array
    {
        return [
            'ingredient_name' => trim($data['ingredient_name'] ?? ''),
            'ingredient_unit' => $data['ingredient_unit'] ?? null,
        ];
    }

    private function sanitizeCalories(array $data): array
    {
        return [
            'ingredient_id' => (int)($data['ingredient_id'] ?? 0),
            'calories' => $data['calories'] ?? null,
            'protein' => $data['protein'] ?? null,
            'fat' => $data['fat'] ?? null,
            'carbohydrates' => $data['carbohydrates'] ?? null,
        ];
    }

    private function sanitizeLocation(array $data): array
    {
        return [
            'location_name' => trim($data['location_name'] ?? ''),
            'location_lat' => (float)($data['location_lat'] ?? 0),
            'location_long' => (float)($data['location_long'] ?? 0),
            'location_borough' => trim($data['location_borough'] ?? 'Montreal'),
            'location_addr' => $data['location_addr'] ?? null,
            'location_type' => $data['location_type'] ?? 'park',
            'location_image_url' => $data['location_image_url'] ?? null,
            'location_accessibility' => $data['location_accessibility'] ?? 'moderate',
        ];
    }
}
