const fs = require("fs");
const path = require("path");

async function run() {
  const response = await fetch(
    "https://www.themealdb.com/api/json/v1/1/search.php?s=",
  );

  const data = await response.json();

  const meals = data.meals || [];
  const recipes = [];
  const ingredients = [];
  const recipeIngredients = [];

  meals.forEach((meal) => {
    recipes.push({
      recipe_external_id: meal.idMeal,
      recipe_name: meal.strMeal,
      recipe_source: "MealDB",
      recipe_category: meal.strCategory,
      recipe_area: meal.strArea,
      recipe_instructions: meal.strInstructions,
      recipe_thumbnail: meal.strMealThumb,
      recipe_tags: meal.strTags || null,
    });

    for (let i = 1; i <= 20; i++) {
      const ingredient = meal[`strIngredient${i}`];
      const measure = meal[`strMeasure${i}`];

      if (ingredient && ingredient.trim() !== "") {
        ingredients.push({
          ingredient_name: ingredient.trim(),
          ingredient_source: "MealDB",
        });
        recipeIngredients.push({
          recipe_id: meal.idMeal,
          ingredient_name: ingredient.trim(),
          quantity: measure || null,
        });
      }
    }
  });

  fs.writeFileSync(
    path.join(__dirname, "../../data/json/recipes.json"),
    JSON.stringify(recipes, null, 2),
  );

  fs.writeFileSync(
    path.join(__dirname, "../../data/json/ingredients.json"),
    JSON.stringify(ingredients, null, 2),
  );

  fs.writeFileSync(
    path.join(__dirname, "../../data/json/recipe_ingredients.json"),
    JSON.stringify(recipeIngredients, null, 2),
  );

  console.log("recipes.json created");
  console.log("ingredients.json created");
  console.log("recipe_ingredients.json created");
}

run();
