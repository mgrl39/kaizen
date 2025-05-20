const fs = require("fs");
const path = require("path");

// Obtener la ruta absoluta al directorio raíz del proyecto
const rootDir = path.resolve(__dirname, "../..");

// Cargar archivos de traducción usando rutas absolutas
const esPath = path.join(
  rootDir,
  "srcs/front/src/lib/i18n/languages/es/index.ts"
);
const enPath = path.join(
  rootDir,
  "srcs/front/src/lib/i18n/languages/en/index.ts"
);

// Leer y parsear los archivos manualmente ya que son archivos TypeScript
const esContent = fs.readFileSync(esPath, "utf8");
const enContent = fs.readFileSync(enPath, "utf8");

// Extraer las claves de traducción mediante expresiones regulares
function extractKeys(content) {
  // Buscar el objeto de traducciones
  const match = content.match(
    /const \w+: TranslationDictionary = {([\s\S]*?)};/
  );
  if (!match) return [];

  const dictContent = match[1];
  // Extraer todas las claves definidas
  const keyRegex = /\s+(\w+):/g;
  const keys = [];
  let keyMatch;

  while ((keyMatch = keyRegex.exec(dictContent)) !== null) {
    keys.push(keyMatch[1]);
  }

  return keys;
}

const esKeys = extractKeys(esContent);
const enKeys = extractKeys(enContent);

console.log(`Español: ${esKeys.length} claves`);
console.log(`Inglés: ${enKeys.length} claves`);

const missingInEn = esKeys.filter((key) => !enKeys.includes(key));
const missingInEs = enKeys.filter((key) => !esKeys.includes(key));

if (missingInEn.length > 0) {
  console.error("Claves faltantes en inglés:", missingInEn);
  process.exit(1);
}

if (missingInEs.length > 0) {
  console.error("Claves faltantes en español:", missingInEs);
  process.exit(1);
}

console.log("✅ Todas las traducciones están completas");
process.exit(0);
