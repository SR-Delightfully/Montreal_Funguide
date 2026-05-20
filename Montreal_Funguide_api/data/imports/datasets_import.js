const { execSync } = require("child_process");

const fs = require("fs");

const path = require("path");

const scripts = [
  "gbif_import.js",
  "mealdb_import.js",
  "geocode_import.js",
  "nutrition_import.js",
];

const jsonDir = path.join(__dirname, "json");

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
  return JSON.parse(fs.readFileSync(path.join(jsonDir, file), "utf8"));
}

const fungiData = {
  species: load("species.json"),

  fungi: [],

  habitat: [],

  species_habitat: [],

  fungi_location: [],
};

const recipesData = {
  recipe: load("recipes.json"),

  ingredient: load("ingredients.json"),

  recipe_ingredient: [],

  calories: load("calories.json"),
};

const mapData = {
  map: [
    {
      map_name: "Montreal Funguide Map",

      map_region: "Montreal",
    },
  ],

  location: load("locations.json"),

  trip: [],

  trip_location: [],
};

const usersData = {
  user: [],

  device: [],

  user_device: [],
};

fs.writeFileSync(
  path.join(jsonDir, "fungi.json"),
  JSON.stringify(fungiData, null, 2),
);

fs.writeFileSync(
  path.join(jsonDir, "recipes.json"),
  JSON.stringify(recipesData, null, 2),
);

fs.writeFileSync(
  path.join(jsonDir, "map.json"),
  JSON.stringify(mapData, null, 2),
);

fs.writeFileSync(
  path.join(jsonDir, "users.json"),
  JSON.stringify(usersData, null, 2),
);

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

console.log("fungi.json created");
console.log("recipes.json created");
console.log("map.json created");
console.log("users.json created");
console.log("combined_data.json created");

console.log("\nALL IMPORTS COMPLETE");
