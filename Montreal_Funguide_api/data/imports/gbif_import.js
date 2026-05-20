const fs = require("fs");
const path = require("path");

async function run() {
  const response = await fetch(
    "https://api.gbif.org/v1/occurrence/search?taxon_key=5&limit=50",
  );

  const data = await response.json();

  const species = data.results.map((item) => ({
    species_name: item.species || item.scientificName || "Unknown",

    species_family: item.family || null,

    species_genus: item.genus || null,

    species_gbif_id: item.taxonKey || null,

    species_observation_date: item.eventDate || null,
  }));

  const outputPath = path.join(__dirname, "../json/species.json");

  fs.writeFileSync(outputPath, JSON.stringify(species, null, 2));

  console.log("species.json created");
}

run();
