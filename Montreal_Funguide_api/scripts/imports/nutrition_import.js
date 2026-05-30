const fs = require("fs");
const path = require("path");

const ingredientsPath = path.join(
  __dirname,
  "../../data/json/ingredients.json",
);
const outputPath = path.join(__dirname, "../json/calories.json");
const API_KEY = fs
  .readFileSync(path.join(__dirname, "../../config/env.php"), "utf-8")
  .match(/ninjas_key'\]\s*=\s*'(.+?)'/)[1];
async function run() {
  if (!fs.existsSync(ingredientsPath)) {
    console.log("ingredients.json missing");
    return;
  }

  const ingredients = JSON.parse(fs.readFileSync(ingredientsPath, "utf-8"));
  const output = [];

  for (const ingredient of ingredients) {
    const url =
      `https://api.api-ninjas.com/v1/nutrition?query=` +
      encodeURIComponent(ingredient.ingredient_name);

    try {
      const response = await fetch(url, {
        headers: {
          "X-Api-Key": API_KEY,
        },
      });

      const data = await response.json();

      if (!data[0]) continue;

      const nutr = data[0];

      output.push({
        ingredient_name: ingredient.ingredient_name,
        calories: nutr.calories ?? null,
        protein: nutr.protein_g ?? null,
        fat: nutr.fat_total_g ?? null,
        carbohydrates: nutr.carbohydrates_total_g ?? null,
      });

      console.log(`Nutrition added for ${ingredient.ingredient_name}`);
    } catch (err) {
      console.log(`Failed nutrition for ${ingredient.ingredient_name}`);
    }
  }

  fs.writeFileSync(
    path.join(__dirname, "../../data/json/calories.json"),
    JSON.stringify(output, null, 2),
  );

  console.log("calories.json created");
}

run();
