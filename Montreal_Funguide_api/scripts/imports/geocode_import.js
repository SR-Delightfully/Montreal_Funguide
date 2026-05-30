const fs = require("fs");
const path = require("path");

const locations = [
  "Mount Royal Montreal",
  "Old Port Montreal",
  "Montreal Botanical Garden",
  "La Fontaine Park Montreal",
  "Bois-de-Liesse Nature Park Montreal",
];

async function run() {
  const output = [];

  for (const location of locations) {
    const url = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(location)}&format=json&limit=1`;

    const response = await fetch(url, {
      headers: {
        "User-Agent": "Montreal-Funguide",
      },
    });

    const data = await response.json();

    if (!data[0]) continue;

    const item = data[0];

    output.push({
      location_name: location,
      location_lat: parseFloat(item.lat),
      location_long: parseFloat(item.lon),
      location_type: "park",
      location_borough: "Montreal",
    });
  }

  fs.writeFileSync(
    path.join(__dirname, "../../data/json/locations.json"),
    JSON.stringify(output, null, 2),
  );

  console.log("locations.json created");
}

run();
