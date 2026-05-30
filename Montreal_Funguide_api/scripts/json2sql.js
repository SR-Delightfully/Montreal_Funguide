const fs = require("fs");
const path = require("path");
const mysql = require("mysql2/promise");
const jsonPath = path.join(__dirname, "../data/json/combined_data.json");

function normalizeBorough(name) {
  if (!name) return null;
  return name.replace(/’/g, "'").replace(/–/g, "-").trim();
}

function clean(v) {
  return v === undefined ? null : v;
}

function parseQuantity(q) {
  if (q === undefined || q === null) return null;
  const num = parseFloat(String(q).replace(/[^0-9.]/g, ""));
  return isNaN(num) ? null : num;
}

async function ensureBorough(db, boroughCache, name) {
  if (!name) return;
  const normalized = normalizeBorough(name);
  if (boroughCache.has(normalized)) return;
  await db.execute(
    `INSERT IGNORE INTO montreal_borough (borough_name) VALUES (?)`,
    [normalized],
  );
  boroughCache.add(normalized);
}

async function run() {
  const data = JSON.parse(fs.readFileSync(jsonPath, "utf8"));
  const db = await mysql.createConnection({
    host: "localhost",
    user: "root",
    password: "",
    database: "montreal_funguide_db",
  });
  console.log("Connected to DB");

  const speciesMap = new Map();
  const ingredientMap = new Map();
  const recipeMap = new Map();
  const fungiMap = new Map();
  const locationMap = new Map();
  const tripMap = new Map();
  const userMap = new Map();
  const deviceMap = new Map();

  const boroughCache = new Set();

  for (const s of data.fungi?.species || []) {
    const [res] = await db.execute(
      `INSERT INTO species (species_name, species_family, species_genus, species_gbif_id)
       VALUES (?, ?, ?, ?)`,
      [
        clean(s.species_name),
        clean(s.species_family),
        clean(s.species_genus),
        clean(s.species_gbif_id),
      ],
    );

    speciesMap.set(s.species_gbif_id, res.insertId);
  }

  for (const h of data.fungi?.habitat || []) {
    const [res] = await db.execute(
      `INSERT INTO habitat (habitat_type, habitat_climate, habitat_soil, habitat_humindex, habitat_desc)
       VALUES (?, ?, ?, ?, ?)`,
      [
        clean(h.habitat_type),
        clean(h.habitat_climate),
        clean(h.habitat_soil),
        clean(h.habitat_humindex),
        clean(h.habitat_desc),
      ],
    );

    h._id = res.insertId;
  }

  for (const sh of data.fungi?.species_habitat || []) {
    const speciesId = speciesMap.get(sh.species_gbif_id || sh.species_id);

    if (!speciesId || !sh.habitat_id) continue;

    await db.execute(
      `INSERT INTO species_habitat (species_id, habitat_id)
       VALUES (?, ?)`,
      [speciesId, sh.habitat_id],
    );
  }

  let fungiIndex = 0;

  for (const f of data.fungi?.fungi || []) {
    const speciesId = speciesMap.get(f.species_gbif_id);
    if (!speciesId) continue;

    const [res] = await db.execute(
      `INSERT INTO fungi (species_id, fungi_observation_source, fungi_observation_date, fungi_notes)
       VALUES (?, ?, ?, ?)`,
      [
        speciesId,
        clean(f.fungi_observation_source),
        clean(f.fungi_observation_date),
        clean(f.fungi_notes),
      ],
    );

    fungiMap.set(fungiIndex++, res.insertId);
  }

  let locIndex = 0;

  for (const l of data.fungi?.fungi_location || []) {
    const fungiId = fungiMap.get(locIndex++);
    if (!fungiId) continue;

    const borough = normalizeBorough(l.location_borough);

    await ensureBorough(db, boroughCache, borough);

    await db.execute(
      `INSERT INTO fungi_location (fungi_id, fungi_location_lat, fungi_location_long, fungi_location_borough)
       VALUES (?, ?, ?, ?)`,
      [fungiId, clean(l.location_lat), clean(l.location_long), borough],
    );
  }

  for (const r of data.recipes?.recipe || []) {
    const [res] = await db.execute(
      `INSERT INTO recipe (recipe_external_id, recipe_name, recipe_source, recipe_category, recipe_area, recipe_instructions, recipe_thumbnail, recipe_tags)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
      [
        clean(r.recipe_external_id),
        clean(r.recipe_name),
        clean(r.recipe_source),
        clean(r.recipe_category),
        clean(r.recipe_area),
        clean(r.recipe_instructions),
        clean(r.recipe_thumbnail),
        clean(r.recipe_tags),
      ],
    );

    recipeMap.set(r.recipe_external_id, res.insertId);
  }

  for (const i of data.recipes?.ingredient || []) {
    const [res] = await db.execute(
      `INSERT INTO ingredient (ingredient_name, ingredient_source)
       VALUES (?, ?)`,
      [clean(i.ingredient_name), clean(i.ingredient_source)],
    );

    ingredientMap.set(i.ingredient_name, res.insertId);
  }

  const pairSet = new Set();

  for (const ri of data.recipes?.recipe_ingredient || []) {
    const recipeId = recipeMap.get(ri.recipe_id);
    const ingredientId = ingredientMap.get(ri.ingredient_name);

    if (!recipeId || !ingredientId) continue;

    const key = `${recipeId}-${ingredientId}`;
    if (pairSet.has(key)) continue;
    pairSet.add(key);

    await db.execute(
      `INSERT INTO recipe_ingredient (recipe_id, ingredient_id, quantity)
       VALUES (?, ?, ?)`,
      [recipeId, ingredientId, parseQuantity(ri.quantity)],
    );
  }

  for (const m of data.map?.map || []) {
    await db.execute(
      `INSERT INTO map (map_name, map_region)
       VALUES (?, ?)`,
      [clean(m.map_name), clean(m.map_region)],
    );
  }

  for (const l of data.map?.location || []) {
    const borough = normalizeBorough(l.location_borough);

    await ensureBorough(db, boroughCache, borough);

    const [res] = await db.execute(
      `INSERT INTO location (location_name, location_lat, location_long, location_borough, location_type)
       VALUES (?, ?, ?, ?, ?)`,
      [
        clean(l.location_name),
        clean(l.location_lat),
        clean(l.location_long),
        borough,
        clean(l.location_type),
      ],
    );

    locationMap.set(locationMap.size, res.insertId);
  }

  for (const t of data.map?.trip || []) {
    const [res] = await db.execute(
      `INSERT INTO trip (trip_desc, trip_distance, trip_travel_mode)
       VALUES (?, ?, ?)`,
      [clean(t.trip_desc), clean(t.trip_distance), clean(t.trip_travel_mode)],
    );

    tripMap.set(tripMap.size, res.insertId);
  }

  for (const tl of data.map?.trip_location || []) {
    const tripId = tripMap.get(tl.trip_index);
    const locationId = locationMap.get(tl.location_index);

    if (!tripId || !locationId) continue;

    await db.execute(
      `INSERT INTO trip_location (trip_id, location_id, trip_location_visit_order, trip_location_notes)
       VALUES (?, ?, ?, ?)`,
      [
        tripId,
        locationId,
        clean(tl.trip_location_visit_order),
        clean(tl.trip_location_notes),
      ],
    );
  }

  for (const u of data.users?.user || []) {
    const [res] = await db.execute(
      `INSERT INTO user (user_handle, user_verified, user_fname, user_lname, user_email, user_password, user_role)
       VALUES (?, ?, ?, ?, ?, ?, ?)`,
      [
        clean(u.user_handle),
        clean(u.user_verified),
        clean(u.user_fname),
        clean(u.user_lname),
        clean(u.user_email),
        clean(u.user_password),
        clean(u.user_role),
      ],
    );

    userMap.set(u.user_email, res.insertId);
  }

  for (const d of data.users?.device || []) {
    const [res] = await db.execute(
      `INSERT INTO device (device_name, device_type, device_os, device_ip)
       VALUES (?, ?, ?, ?)`,
      [
        clean(d.device_name),
        clean(d.device_type),
        clean(d.device_os),
        clean(d.device_ip),
      ],
    );

    deviceMap.set(d.device_ip, res.insertId);
  }

  for (const ud of data.users?.user_device || []) {
    const userId = userMap.get(ud.user_email);
    const deviceId = deviceMap.get(ud.device_ip);

    if (!userId || !deviceId) continue;

    await db.execute(
      `INSERT INTO user_device (user_id, device_id, last_ip, last_user_agent, login_count)
       VALUES (?, ?, ?, ?, ?)`,
      [
        userId,
        deviceId,
        clean(ud.last_ip),
        clean(ud.last_user_agent),
        clean(ud.login_count),
      ],
    );
  }

  console.log("IMPORT COMPLETE");
  await db.end();
}

run().catch(console.error);
