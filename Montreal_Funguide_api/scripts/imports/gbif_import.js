const fs = require("fs");
const path = require("path");

// Hard coding borough boundaries, since we don't need all of this information directly inside the database,
// only to add the correct borough to the record depending on its lat/long location
const boroughs = [
  {
    name: "Ahuntsic-Cartierville",
    minLat: 45.52,
    maxLat: 45.57,
    minLng: -73.73,
    maxLng: -73.66,
  },
  {
    name: "Anjou",
    minLat: 45.6,
    maxLat: 45.63,
    minLng: -73.57,
    maxLng: -73.52,
  },
  {
    name: "Côte-des-Neiges–Notre-Dame-de-Grâce",
    minLat: 45.47,
    maxLat: 45.51,
    minLng: -73.65,
    maxLng: -73.6,
  },
  {
    name: "Lachine",
    minLat: 45.43,
    maxLat: 45.46,
    minLng: -73.73,
    maxLng: -73.68,
  },
  {
    name: "LaSalle",
    minLat: 45.41,
    maxLat: 45.45,
    minLng: -73.66,
    maxLng: -73.6,
  },
  {
    name: "Le Plateau-Mont-Royal",
    minLat: 45.52,
    maxLat: 45.54,
    minLng: -73.59,
    maxLng: -73.56,
  },
  {
    name: "Le Sud-Ouest",
    minLat: 45.45,
    maxLat: 45.49,
    minLng: -73.6,
    maxLng: -73.55,
  },
  {
    name: "L’Île-Bizard–Sainte-Geneviève",
    minLat: 45.48,
    maxLat: 45.53,
    minLng: -73.95,
    maxLng: -73.85,
  },
  {
    name: "Mercier–Hochelaga-Maisonneuve",
    minLat: 45.54,
    maxLat: 45.58,
    minLng: -73.55,
    maxLng: -73.48,
  },
  {
    name: "Montréal-Nord",
    minLat: 45.6,
    maxLat: 45.64,
    minLng: -73.65,
    maxLng: -73.6,
  },
  {
    name: "Outremont",
    minLat: 45.51,
    maxLat: 45.53,
    minLng: -73.62,
    maxLng: -73.6,
  },
  {
    name: "Pierrefonds-Roxboro",
    minLat: 45.48,
    maxLat: 45.53,
    minLng: -73.92,
    maxLng: -73.83,
  },
  {
    name: "Rivière-des-Prairies–Pointe-aux-Trembles",
    minLat: 45.65,
    maxLat: 45.71,
    minLng: -73.55,
    maxLng: -73.44,
  },
  {
    name: "Rosemont–La Petite-Patrie",
    minLat: 45.54,
    maxLat: 45.57,
    minLng: -73.61,
    maxLng: -73.56,
  },
  {
    name: "Saint-Laurent",
    minLat: 45.48,
    maxLat: 45.52,
    minLng: -73.76,
    maxLng: -73.66,
  },
  {
    name: "Saint-Léonard",
    minLat: 45.57,
    maxLat: 45.6,
    minLng: -73.6,
    maxLng: -73.54,
  },
  {
    name: "Verdun",
    minLat: 45.43,
    maxLat: 45.47,
    minLng: -73.58,
    maxLng: -73.53,
  },
  {
    name: "Ville-Marie",
    minLat: 45.49,
    maxLat: 45.52,
    minLng: -73.58,
    maxLng: -73.54,
  },
  {
    name: "Villeray–Saint-Michel–Parc-Extension",
    minLat: 45.54,
    maxLat: 45.58,
    minLng: -73.64,
    maxLng: -73.59,
  },
  {
    name: "Dollard-des-Ormeaux",
    minLat: 45.48,
    maxLat: 45.5,
    minLng: -73.84,
    maxLng: -73.8,
  },
  {
    name: "Pointe-Claire",
    minLat: 45.44,
    maxLat: 45.46,
    minLng: -73.83,
    maxLng: -73.8,
  },
  {
    name: "Kirkland",
    minLat: 45.44,
    maxLat: 45.47,
    minLng: -73.89,
    maxLng: -73.85,
  },
  {
    name: "Beaconsfield",
    minLat: 45.42,
    maxLat: 45.45,
    minLng: -73.9,
    maxLng: -73.87,
  },
  {
    name: "Baie-D'Urfé",
    minLat: 45.41,
    maxLat: 45.43,
    minLng: -73.92,
    maxLng: -73.89,
  },
  {
    name: "Senneville",
    minLat: 45.4,
    maxLat: 45.42,
    minLng: -73.95,
    maxLng: -73.9,
  },
  {
    name: "Dorval",
    minLat: 45.43,
    maxLat: 45.46,
    minLng: -73.75,
    maxLng: -73.7,
  },
  {
    name: "Montréal-Ouest",
    minLat: 45.45,
    maxLat: 45.46,
    minLng: -73.64,
    maxLng: -73.62,
  },
  {
    name: "Hampstead",
    minLat: 45.47,
    maxLat: 45.48,
    minLng: -73.65,
    maxLng: -73.63,
  },
  {
    name: "Côte-Saint-Luc",
    minLat: 45.46,
    maxLat: 45.48,
    minLng: -73.67,
    maxLng: -73.64,
  },
  {
    name: "Mont-Royal (Town of Mount Royal)",
    minLat: 45.5,
    maxLat: 45.52,
    minLng: -73.66,
    maxLng: -73.64,
  },
  {
    name: "L'Île-Dorval",
    minLat: 45.42,
    maxLat: 45.43,
    minLng: -73.75,
    maxLng: -73.74,
  },
  {
    name: "Laval",
    minLat: 45.55,
    maxLat: 45.65,
    minLng: -73.85,
    maxLng: -73.65,
  },
  {
    name: "South Shore",
    minLat: 45.4,
    maxLat: 45.55,
    minLng: -73.5,
    maxLng: -73.3,
  },
];

function getBorough(lat, lng) {
  for (const b of boroughs) {
    if (
      lat >= b.minLat &&
      lat <= b.maxLat &&
      lng >= b.minLng &&
      lng <= b.maxLng
    ) {
      return b.name;
    }
  }
  return null;
}

async function run() {
  const response = await fetch(
    // "https://api.gbif.org/v1/occurrence/search?taxon_key=5&limit=50",
    "https://api.gbif.org/v1/occurrence/search?kingdomKey=5&limit=300&decimalLatitude=45.35,45.75&decimalLongitude=-74.15,-73.45&hasCoordinate=true", //limiting the search to only Montreal-based locations
  );

  const data = await response.json();

  const speciesMap = new Map();
  const fungi = [];
  const locations = [];

  for (const item of data.results) {
    const lat = item.decimalLatitude;
    const lng = item.decimalLongitude;

    if (lat == null || lng == null) continue;

    const borough = getBorough(lat, lng);
    if (!borough) continue;

    const speciesKey = item.speciesKey;

    if (speciesKey && !speciesMap.has(speciesKey)) {
      speciesMap.set(speciesKey, {
        species_name: item.species || item.scientificName || "Unknown",
        species_family: item.family || null,
        species_genus: item.genus || null,
        species_gbif_id: speciesKey,
      });
    }

    fungi.push({
      species_gbif_id: speciesKey,
      fungi_observation_source: "GBIF",
      fungi_observation_date: item.eventDate || null,
      fungi_notes: item.remarks || null,
    });

    locations.push({
      location_lat: lat,
      location_long: lng,
      location_borough: borough,
      location_type: "fungi_spotting",
    });
  }

  fs.writeFileSync(
    path.join(__dirname, "../../data/json/species.json"),
    JSON.stringify([...speciesMap.values()], null, 2),
  );

  fs.writeFileSync(
    path.join(__dirname, "../../data/json/fungi.json"),
    JSON.stringify(fungi, null, 2),
  );

  fs.writeFileSync(
    path.join(__dirname, "../../data/json/locations.json"),
    JSON.stringify(locations, null, 2),
  );

  console.log("species.json created");
  console.log("fungi.json created");
  console.log("locations.json created");
}

run();
