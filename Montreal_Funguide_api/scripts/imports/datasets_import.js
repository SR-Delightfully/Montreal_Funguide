const { execSync } = require("child_process");
const fs = require("fs");
const path = require("path");

const scripts = [
  "gbif_import.js",
  "mealdb_import.js",
  "geocode_import.js",
  "nutrition_import.js",
];

const jsonDir = path.join(__dirname, "../../data/json");

if (!fs.existsSync(jsonDir)) {
  fs.mkdirSync(jsonDir, { recursive: true });
}

for (const script of scripts) {
  console.log(`\nRunning ${script}...\n`);

  try {
    execSync(`node "${path.join(__dirname, script)}"`, {
      stdio: "inherit",
    });

    console.log(`SUCCESS: ${script}`);
  } catch (err) {
    console.error(`FAILED: ${script}`);
    process.exit(1);
  }
}

console.log("\nBuilding grouped datasets...\n");

function load(file) {
  const filePath = path.join(jsonDir, file);
  if (!fs.existsSync(filePath)) return [];
  try {
    return JSON.parse(fs.readFileSync(filePath, "utf8"));
  } catch {
    return [];
  }
}

/* -------------------------
   LOAD RAW DATA
--------------------------*/
const species = load("species.json");
const fungi = load("fungi.json");
const locations = load("locations.json");
const recipes = load("recipes.json");
const ingredients = load("ingredients.json");
const recipe_ingredients = load("recipe_ingredients.json");
const calories = load("calories.json");

const habitatMap = {
  Polyporaceae: "dead_wood",
  Psathyrellaceae: "forest_floor",
  Amanitaceae: "forest_soil",
  Parmeliaceae: "tree_bark",
  Xylariaceae: "decaying_wood",
  Agaricaceae: "grassland_or_forest",
};

const habitatSet = new Set();
const species_habitat = [];

for (const sp of species) {
  const habitat = habitatMap[sp.species_family] || "mixed_forest";

  habitatSet.add(habitat);

  species_habitat.push({
    species_gbif_id: sp.species_gbif_id,
    habitat_name: habitat,
  });
}

const habitat = [...habitatSet].map((h) => ({
  habitat_name: h,
}));

const trip = [];
const trip_location = [];

for (let i = 0; i < locations.length; i += 3) {
  const group = locations.slice(i, i + 3);

  const tripId = i / 3 + 1;

  trip.push({
    trip_id: tripId,
    trip_name: `Fungi Route ${tripId}`,
  });

  for (const loc of group) {
    trip_location.push({
      trip_id: tripId,
      location_name: loc.location_name,
    });
  }
}

const fungiData = {
  species,
  fungi,
  habitat,
  species_habitat,
  fungi_location: locations,
};

const recipesData = {
  recipe: recipes,
  ingredient: ingredients,
  recipe_ingredient: recipe_ingredients,
  calories,
};

const mapData = {
  map: [
    {
      map_name: "Montreal Funguide Map",
      map_region: "Montreal",
    },
  ],
  location: locations,
  trip,
  trip_location,
};

const usersData = {
  user: [],
  device: [],
  user_device: [],
};

/* -------------------------
   COMBINED OUTPUT
--------------------------*/
const combinedData = {
  fungi: fungiData,
  recipes: recipesData,
  map: mapData,
  users: usersData,
  metadata: {
    generated_at: new Date().toISOString(),
    project: "Montreal Funguide",
    version: "1.0.0",
  },
};

fs.writeFileSync(
  path.join(jsonDir, "combined_data.json"),
  JSON.stringify(combinedData, null, 2),
);

console.log("combined_data.json created");
console.log("\nALL IMPORTS COMPLETE");
