/**
 * Scrape specific hymn IDs
 *
 * Usage: node scrape-ids.js 16,87,102,141,158,181,234,327 --csv
 */

const { scrapeHymn } = require('./myanmar-hymns.js');
const fs = require('fs');
const path = require('path');

const OUTPUT_DIR = path.join(__dirname, 'output');

function escapeCsv(text) {
  if (!text) return '';
  return '"' + text.replace(/"/g, '""') + '"';
}

function saveToCSV(hymns) {
  let csv = 'Reference ID,Hymn Number,Title (Myanmar),Title (English),Composer,Category,Scriptures,PDF,PPTX,Verse Count,Lyrics\n';

  hymns.forEach(hymn => {
    const lyricsText = hymn.lyrics
      .map(verse => {
        if (verse.verse_type === 'chorus') {
          return `[Chorus]\n${verse.content}`;
        }
        return `[Verse ${verse.verse_number}]\n${verse.content}`;
      })
      .join('\n\n');

    csv += [
      hymn.reference_id,
      hymn.hymn_number,
      escapeCsv(hymn.title_mm),
      escapeCsv(hymn.title_en),
      escapeCsv(hymn.composer),
      escapeCsv(hymn.category),
      escapeCsv(hymn.scriptures),
      escapeCsv(hymn.files?.pdf || ''),
      escapeCsv(hymn.files?.pptx || ''),
      hymn.lyrics.length,
      escapeCsv(lyricsText)
    ].join(',') + '\n';
  });

  const timestamp = new Date().toISOString().replace(/[:.]/g, '-').substring(0, 19);
  const csvPath = path.join(OUTPUT_DIR, `myanmar-hymns-custom-${timestamp}.csv`);
  fs.writeFileSync(csvPath, csv, 'utf8');
  return csvPath;
}

async function main() {
  const args = process.argv.slice(2);
  const idsArg = args[0];

  if (!idsArg || idsArg.startsWith('--')) {
    console.log(`
Usage: node scrape-ids.js <id1,id2,id3,...> [--csv]

Example:
  node scrape-ids.js 16,87,102,141,158,181,234,327 --csv
`);
    process.exit(1);
  }

  const ids = idsArg.split(',').map(id => parseInt(id.trim()));
  const delay = 2000; // 2 second delay

  console.log(`\n🚀 Scraping ${ids.length} specific hymn IDs: ${ids.join(', ')}`);
  console.log(`⏱️  Delay: ${delay}ms between requests\n`);

  const results = [];
  const skipped = [];

  for (let i = 0; i < ids.length; i++) {
    const id = ids[i];
    console.log(`[${i + 1}/${ids.length}] Scraping hymn ${id}...`);

    const hymn = await scrapeHymn(id);

    if (hymn) {
      results.push(hymn);
    } else {
      skipped.push(id);
    }

    // Rate limiting
    if (i < ids.length - 1) {
      await new Promise(resolve => setTimeout(resolve, delay));
    }
  }

  // Save results
  if (results.length > 0) {
    const csvPath = saveToCSV(results);
    console.log('\n' + '='.repeat(50));
    console.log('📊 SCRAPING SUMMARY');
    console.log('='.repeat(50));
    console.log(`✅ Successfully scraped: ${results.length} hymns`);
    console.log(`⚠️  Skipped/Not found: ${skipped.length} hymns`);
    if (skipped.length > 0) {
      console.log(`   Skipped IDs: ${skipped.join(', ')}`);
    }
    console.log(`💾 Saved to: ${path.basename(csvPath)}`);
    console.log('='.repeat(50) + '\n');
  } else {
    console.log('\n❌ No hymns were successfully scraped.\n');
  }
}

main().catch(console.error);
