/**
 * Scrape specific hymn IDs and merge with existing CSV
 *
 * Usage: node scrape-merge-ids.js <csv_file> <id1,id2,id3,...>
 *
 * Example: node scrape-merge-ids.js myanmar-hymns-12-429.csv 16,87,102,141,158,181,234,327
 */

const { scrapeHymn } = require('./myanmar-hymns.js');
const fs = require('fs');
const path = require('path');

const OUTPUT_DIR = path.join(__dirname, 'output');

function escapeCsv(text) {
  if (!text) return '';
  return '"' + text.replace(/"/g, '""') + '"';
}

function unescapeCsv(text) {
  if (!text || text === '') return '';
  // Remove surrounding quotes
  let cleaned = text;
  if (cleaned.startsWith('"') && cleaned.endsWith('"')) {
    cleaned = cleaned.slice(1, -1);
  }
  // Unescape double quotes
  cleaned = cleaned.replace(/""/g, '"');
  return cleaned;
}

function parseCSV(csvContent) {
  const lines = csvContent.split('\n').filter(line => line.trim());
  const header = lines[0];
  const hymns = [];

  for (let i = 1; i < lines.length; i++) {
    const line = lines[i];
    if (!line.trim()) continue;

    // Parse CSV (handling quoted fields with commas)
    const matches = [];
    let current = '';
    let inQuotes = false;

    for (let j = 0; j < line.length; j++) {
      const char = line[j];
      const nextChar = line[j + 1];

      if (char === '"') {
        if (inQuotes && nextChar === '"') {
          current += '"';
          j++;
        } else {
          inQuotes = !inQuotes;
        }
      } else if (char === ',' && !inQuotes) {
        matches.push(current);
        current = '';
      } else {
        current += char;
      }
    }
    matches.push(current);

    if (matches.length >= 11) {
      hymns.push({
        reference_id: parseInt(matches[0]),
        hymn_number: parseInt(matches[1]),
        title_mm: unescapeCsv(matches[2]),
        title_en: unescapeCsv(matches[3]),
        composer: unescapeCsv(matches[4]),
        category: unescapeCsv(matches[5]),
        scriptures: unescapeCsv(matches[6]),
        pdf: unescapeCsv(matches[7]),
        pptx: unescapeCsv(matches[8]),
        verse_count: parseInt(matches[9]),
        lyrics: unescapeCsv(matches[10])
      });
    }
  }

  return { header, hymns };
}

function hymnToCSVRow(hymn) {
  // Handle both scraped hymns (lyrics as array) and parsed CSV hymns (lyrics as string)
  let lyricsText;
  let verseCount;

  if (Array.isArray(hymn.lyrics)) {
    // Newly scraped hymn - lyrics is an array
    verseCount = hymn.lyrics.length;
    lyricsText = hymn.lyrics
      .map(verse => {
        if (verse.verse_type === 'chorus') {
          return `[Chorus]\n${verse.content}`;
        }
        return `[Verse ${verse.verse_number}]\n${verse.content}`;
      })
      .join('\n\n');
  } else {
    // Parsed from CSV - lyrics is already a string
    lyricsText = hymn.lyrics || '';
    verseCount = hymn.verse_count || 0;
  }

  return [
    hymn.reference_id,
    hymn.hymn_number,
    escapeCsv(hymn.title_mm),
    escapeCsv(hymn.title_en),
    escapeCsv(hymn.composer),
    escapeCsv(hymn.category),
    escapeCsv(hymn.scriptures),
    escapeCsv(hymn.files?.pdf || hymn.pdf || ''),
    escapeCsv(hymn.files?.pptx || hymn.pptx || ''),
    verseCount,
    escapeCsv(lyricsText)
  ].join(',');
}

function saveMergedCSV(header, hymns, outputPath) {
  let csv = header + '\n';

  // Sort by reference_id
  hymns.sort((a, b) => a.reference_id - b.reference_id);

  hymns.forEach(hymn => {
    csv += hymnToCSVRow(hymn) + '\n';
  });

  fs.writeFileSync(outputPath, csv, 'utf8');
  return outputPath;
}

async function main() {
  const args = process.argv.slice(2);

  if (args.length < 2) {
    console.log(`
Usage: node scrape-merge-ids.js <csv_file> <id1,id2,id3,...>

Example:
  node scrape-merge-ids.js myanmar-hymns-12-429.csv 16,87,102,141,158,181,234,327

This will:
  1. Read the existing CSV file
  2. Scrape only the missing IDs
  3. Merge the new hymns into the existing data
  4. Save a new merged CSV file
`);
    process.exit(1);
  }

  const csvFilename = args[0];
  const idsArg = args[1];
  const csvPath = path.join(OUTPUT_DIR, csvFilename);

  if (!fs.existsSync(csvPath)) {
    console.error(`❌ Error: CSV file not found: ${csvFilename}`);
    console.error(`   Looking for: ${csvPath}`);
    process.exit(1);
  }

  const ids = idsArg.split(',').map(id => parseInt(id.trim()));
  const delay = 2000; // 2 second delay

  // Read existing CSV
  console.log(`📂 Reading existing CSV: ${csvFilename}`);
  const csvContent = fs.readFileSync(csvPath, 'utf8');
  const { header, hymns: existingHymns } = parseCSV(csvContent);

  console.log(`   Found ${existingHymns.length} existing hymns`);

  // Check which IDs are actually missing
  const existingIds = new Set(existingHymns.map(h => h.reference_id));
  const actuallyMissing = ids.filter(id => !existingIds.has(id));

  if (actuallyMissing.length === 0) {
    console.log(`\n✅ All specified IDs already exist in the CSV file!`);
    console.log(`   No scraping needed.\n`);
    process.exit(0);
  }

  console.log(`\n🚀 Scraping ${actuallyMissing.length} missing hymn IDs: ${actuallyMissing.join(', ')}`);
  console.log(`⏱️  Delay: ${delay}ms between requests\n`);

  const scrapedHymns = [];
  const skipped = [];

  for (let i = 0; i < actuallyMissing.length; i++) {
    const id = actuallyMissing[i];
    console.log(`[${i + 1}/${actuallyMissing.length}] Scraping hymn ${id}...`);

    const hymn = await scrapeHymn(id);

    if (hymn) {
      scrapedHymns.push(hymn);
    } else {
      skipped.push(id);
    }

    // Rate limiting
    if (i < actuallyMissing.length - 1) {
      await new Promise(resolve => setTimeout(resolve, delay));
    }
  }

  // Merge with existing hymns
  const allHymns = [...existingHymns, ...scrapedHymns];

  // Save merged CSV
  const timestamp = new Date().toISOString().replace(/[:.]/g, '-').substring(0, 19);
  const mergedFilename = csvFilename.replace('.csv', `-merged-${timestamp}.csv`);
  const mergedPath = path.join(OUTPUT_DIR, mergedFilename);

  saveMergedCSV(header, allHymns, mergedPath);

  console.log('\n' + '='.repeat(50));
  console.log('📊 MERGE SUMMARY');
  console.log('='.repeat(50));
  console.log(`📂 Original CSV: ${csvFilename} (${existingHymns.length} hymns)`);
  console.log(`✅ Scraped: ${scrapedHymns.length} hymns`);
  console.log(`⚠️  Skipped/Not found: ${skipped.length} hymns`);
  if (skipped.length > 0) {
    console.log(`   Skipped IDs: ${skipped.join(', ')}`);
  }
  console.log(`📝 Merged CSV: ${mergedFilename} (${allHymns.length} total hymns)`);
  console.log('='.repeat(50) + '\n');

  if (skipped.length > 0) {
    console.log(`💡 To retry skipped IDs, run:`);
    console.log(`   node scrape-merge-ids.js ${csvFilename} ${skipped.join(',')}\n`);
  }
}

main().catch(console.error);
