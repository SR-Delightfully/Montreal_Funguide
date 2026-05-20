<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DatabaseModel;

class DatabaseService
{
    private array $data = [];

    public function __construct(
        private DatabaseModel $model
    ) {}

    public function run(): void
    {
        $this->loadJson();

        $this->model->beginTransaction();

        try {
            $this->importSpecies();
            $this->importHabitat();
            $this->importFungi();
            $this->importLocations();
            $this->importIngredients();
            $this->importRecipes();
            $this->importNutrition();

            $this->model->commit();
        } catch (\Throwable $e) {
            $this->model->rollback();
            throw $e;
        }
    }

    private function loadJson(): void
    {
        $path = __DIR__ . "/../../data/json/combined_data.json";

        if (!file_exists($path)) {
            throw new \RuntimeException("combined_data.json not found");
        }

        $json = file_get_contents($path);
        $this->data = json_decode($json, true) ?? [];

        if (!is_array($this->data)) {
            throw new \RuntimeException("Invalid JSON structure");
        }
    }

    private function importSpecies(): void
    {
        foreach (($this->data['fungi']['species'] ?? []) as $s) {

            $speciesId = $this->model->insertSpecies([
                'species_name' => trim($s['species_name'] ?? ''),
                'species_family' => $s['species_family'] ?? null,
                'species_genus' => $s['species_genus'] ?? null,
                'species_edibility' => (int)($s['species_edibility'] ?? 0),
                'species_toxicity' => $s['species_toxicity'] ?? 'safe',
                'species_discovery' => $s['species_discovery'] ?? null,
                'species_gbif_id' => $s['species_gbif_id'] ?? null,
                'species_image_url' => $s['species_image_url'] ?? null,
            ]);

            foreach (($s['habitats'] ?? []) as $habitatId) {
                $this->model->linkSpeciesHabitat($speciesId, (int)$habitatId);
            }
        }
    }

    private function importHabitat(): void
    {
        foreach (($this->data['fungi']['habitat'] ?? []) as $h) {
            $this->model->insertHabitat([
                'habitat_type' => $h['type'] ?? null,
                'habitat_climate' => $h['climate'] ?? null,
                'habitat_soil' => $h['soil'] ?? null,
                'habitat_humindex' => $h['humidity'] ?? null,
                'habitat_desc' => $h['description'] ?? null,
            ]);
        }
    }

    private function importFungi(): void
    {
        foreach (($this->data['fungi']['fungi'] ?? []) as $f) {

            $fungiId = $this->model->insertFungi([
                'species_id' => $f['species_id'] ?? null,
                'fungi_observation_source' => $f['source'] ?? 'GBIF',
                'fungi_observation_date' => $f['date'] ?? null,
                'fungi_notes' => $f['notes'] ?? null,
            ]);

            if (!empty($f['location'])) {
                $this->model->insertFungiLocation([
                    'fungi_id' => $fungiId,
                    'fungi_location_lat' => $f['location']['lat'] ?? null,
                    'fungi_location_long' => $f['location']['long'] ?? null,
                    'fungi_location_borough' => $f['location']['borough'] ?? null,
                ]);
            }
        }
    }

    private function importLocations(): void
    {
        foreach (($this->data['map']['location'] ?? []) as $l) {
            $this->model->insertLocation([
                'location_name' => trim($l['location_name'] ?? ''),
                'location_lat' => (float)($l['location_lat'] ?? 0),
                'location_long' => (float)($l['location_long'] ?? 0),
                'location_borough' => $l['location_borough'] ?? 'Montreal',
                'location_type' => $l['location_type'] ?? 'park',
                'location_addr' => $l['location_addr'] ?? null,
                'location_image_url' => $l['location_image_url'] ?? null,
                'location_accessibility' => $l['location_accessibility'] ?? 'moderate',
            ]);
        }
    }

    private function importIngredients(): void
    {
        foreach (($this->data['recipes']['ingredient'] ?? []) as $i) {
            $this->model->insertIngredient([
                'ingredient_name' => trim($i['ingredient_name'] ?? ''),
                'ingredient_unit' => $i['ingredient_unit'] ?? null,
            ]);
        }
    }

    private function importRecipes(): void
    {
        foreach (($this->data['recipes']['recipe'] ?? []) as $r) {

            $recipeId = $this->model->insertRecipe([
                'recipe_external_id' => $r['recipe_external_id'] ?? null,
                'recipe_name' => trim($r['recipe_name'] ?? ''),
                'recipe_source' => $r['recipe_source'] ?? 'MealDB',
                'recipe_category' => $r['recipe_category'] ?? null,
                'recipe_area' => $r['recipe_area'] ?? null,
                'recipe_instructions' => $r['recipe_instructions'] ?? null,
                'recipe_thumbnail' => $r['recipe_thumbnail'] ?? null,
                'recipe_tags' => $r['recipe_tags'] ?? null,
                'recipe_difficulty' => $r['recipe_difficulty'] ?? 'medium',
                'recipe_image_url' => $r['recipe_image_url'] ?? null,
            ]);

            foreach (($r['ingredients'] ?? []) as $ingredient) {
                $this->model->linkRecipeIngredient([
                    'recipe_id' => $recipeId,
                    'ingredient_id' => $ingredient['ingredient_id'],
                    'quantity' => $ingredient['quantity'] ?? null,
                ]);
            }
        }
    }

    private function importNutrition(): void
    {
        foreach (($this->data['recipes']['calories'] ?? []) as $c) {
            $this->model->insertCalories([
                'ingredient_id' => $c['ingredient_id'],
                'calories' => $c['calories'] ?? null,
                'protein' => $c['protein'] ?? null,
                'fat' => $c['fat'] ?? null,
                'carbohydrates' => $c['carbohydrates'] ?? null,
            ]);
        }
    }
}
