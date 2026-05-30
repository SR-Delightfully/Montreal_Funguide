const mysql = require("mysql2/promise");

async function resetDatabase() {
  const db = await mysql.createConnection({
    host: "localhost",
    user: "root",
    password: "",
    database: "montreal_funguide_db",
  });

  console.log("Connected to DB");

  await db.query(`SET FOREIGN_KEY_CHECKS = 0;`);

  const [tables] = await db.query(`
    SELECT TABLE_NAME
    FROM information_schema.tables
    WHERE table_schema = 'montreal_funguide_db'
      AND TABLE_TYPE = 'BASE TABLE';
  `);

  console.log(`Dropping ${tables.length} tables...`);

  for (const row of tables) {
    const tableName = row.TABLE_NAME || row.table_name;

    if (!tableName) {
      console.log("Skipping invalid row:", row);
      continue;
    }

    console.log("Dropping:", tableName);
    await db.query(`DROP TABLE IF EXISTS \`${tableName}\``);
  }

  await db.query(`SET FOREIGN_KEY_CHECKS = 1;`);
  console.log("All tables dropped.");

  await db.end();
}

resetDatabase().catch((err) => {
  console.error("Reset failed:", err);
});
