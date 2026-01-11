const axios = require('axios');
const cheerio = require('cheerio');
const fs = require('fs');
const path = require('path');

const BASE_URL = 'https://www.myanmarhymn.com/hymn.php?id=';
const OUTPUT_DIR = path.join(__dirname, 'output');
const FILES_DIR = path.join(OUTPUT_DIR, 'files');

// Ensure output directories exist
if (!fs.existsSync(OUTPUT_DIR)) {
  fs.mkdirSync(OUTPUT_DIR, { recursive: true });
}
if (!fs.existsSync(FILES_DIR)) {
  fs.mkdirSync(FILES_DIR, { recursive: true });
}

/**
 * Download a file from URL
 * @param {string} url - The URL to download from
 * @param {string} filepath - The local filepath to save to
 * @returns {Promise<boolean>} - True if successful, false otherwise
 */
async function downloadFile(url, filepath) {
  try {
    const response = await axios({
      method: 'GET',
      url: url,
      responseType: 'stream',
      timeout: 30000
    });

    const writer = fs.createWriteStream(filepath);
    response.data.pipe(writer);

    return new Promise((resolve, reject) => {
      writer.on('finish', () => resolve(true));
      writer.on('error', (err) => {
        fs.unlink(filepath, () => {}); // Delete partial file
        reject(err);
      });
    });
  } catch (error) {
    console.error(`  ⚠️  Failed to download ${url}: ${error.message}`);
    return false;
  }
}

/**
 * Scrape a single hymn from myanmarhymn.com
 * @param {number} id - The hymn ID
 * @returns {Object|null} - Hymn data or null if failed
 */
async function scrapeHymn(id) {
  try {
    console.log(`📖 Scraping hymn ${id}...`);

    const response = await axios.get(`${BASE_URL}${id}`, {
      headers: {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Language': 'en-US,en;q=0.5',
        'Accept-Encoding': 'gzip, deflate, br',
        'Connection': 'keep-alive',
        'Upgrade-Insecure-Requests': '1'
      },
      timeout: 15000
    });

    const $ = cheerio.load(response.data);

    // Extract full page content
    const fullText = $('body').text();

    // Extract hymn number from URL
    const hymnNumber = id;

    // Extract title (Myanmar) - from <title> tag
    const titleMM = $('title').text().trim().replace(' - Myanmar Hymn', '') || '';

    // Extract title (English) - search for it in the content
    let titleEN = '';
    const titleENMatch = fullText.match(/Title\s*\(English\)[:\s]*([^\n]+)/i);
    if (titleENMatch) {
      titleEN = titleENMatch[1].trim();
    } else {
      // Try to find English title in page content (usually after Myanmar title)
      const lines = fullText.split('\n').map(line => line.trim()).filter(line => line.length > 0);
      for (let i = 0; i < lines.length; i++) {
        // Look for lines that are primarily English letters
        if (lines[i] && /^[A-Za-z\s]+$/.test(lines[i]) && lines[i].length > 3) {
          // Exclude common non-title text
          if (!lines[i].includes('Acknowledgement') &&
              !lines[i].includes('Composer') &&
              !lines[i].includes('Myanmar Hymn') &&
              !lines[i].includes('All rights reserved')) {
            titleEN = lines[i];
            break;
          }
        }
      }
    }

    // Extract composer
    let composer = '';
    const composerMatch = fullText.match(/Composer[:\s]*([^\n]+)/i);
    if (composerMatch) {
      composer = composerMatch[1].trim();
    } else {
      // Try to find composer pattern (Name with years like "Henry Smart (1813-1879)")
      const composerPatternMatch = fullText.match(/([A-Z][a-zA-Z\s]+)\s*\(\d{4}-\d{4}\)/);
      if (composerPatternMatch) {
        composer = composerPatternMatch[1].trim();
      }
    }

    // Extract lyrics (verses) - stop before metadata section
    const lyrics = [];

    // Find where the metadata section starts (look for "Hymn Number:", "<p>", "PPTX File", etc.)
    const metadataStartPatterns = [
      '<p>',
      'Hymn Number:',
      'PPTX File',
      'Download PPTX',
      'PDF File',
      'Download PDF'
    ];

    let lyricsText = fullText;
    for (const pattern of metadataStartPatterns) {
      const patternIndex = fullText.indexOf(pattern);
      if (patternIndex !== -1) {
        lyricsText = fullText.substring(0, patternIndex);
        break;
      }
    }

    // Split content into lines
    const lines = lyricsText.split('\n').map(line => line.trim()).filter(line => line.length > 0);

    let currentVerse = [];
    let foundFirstVerse = false;
    let verseNumber = null;

    for (const line of lines) {
      // Check if line starts with a Myanmar verse number (၁။, ၂။, ၃။, etc.)
      const verseMatch = line.match(/^([၁၂၃၄၅၆၇၈၉၀])\s*။/);

      if (verseMatch) {
        // Save previous verse if exists
        if (currentVerse.length > 0 && foundFirstVerse) {
          lyrics.push({
            verse_number: verseNumber,
            content: currentVerse.join('\n')
          });
        }

        // Start new verse
        verseNumber = myanmarDigitToArabic(verseMatch[1]);
        currentVerse = [line.replace(/^[၁၂၃၄၅၆၇၈၉၀]\s*။\s*/, '')];
        foundFirstVerse = true;
      } else if (foundFirstVerse) {
        // Check for special verse markers like "၄ (က)" or "တုန့်ပြန်ရန်ကျူး"
        const specialVerseMatch = line.match(/^(\d+)\s*\((က|ခ|ဂ)\)\s*(.+)$/);
        if (specialVerseMatch && currentVerse.length > 0) {
          // Save previous verse first
          lyrics.push({
            verse_number: verseNumber,
            content: currentVerse.join('\n')
          });

          verseNumber = parseInt(specialVerseMatch[1]);
          currentVerse = [specialVerseMatch[3]];
        } else if (!line.includes('Acknowledgement') &&
                   !line.includes('Title') &&
                   !line.includes('Composer') &&
                   !line.includes('All rights reserved') &&
                   !line.includes('Myanmar Hymn') &&
                   line.length > 0) {
          // Add line to current verse
          currentVerse.push(line);
        }
      }
    }

    // Add the last verse
    if (currentVerse.length > 0 && foundFirstVerse) {
      lyrics.push({
        verse_number: verseNumber,
        content: currentVerse.join('\n')
      });
    }

    // Extract metadata from the structured section at the bottom
    // Look for pattern: "Label:\nValue"
    let scriptures = '';
    let category = '';

    // Extract scriptures - look for "Scriptures:" followed by the value on next line(s)
    const scripturesIndex = fullText.indexOf('Scriptures:');
    if (scripturesIndex !== -1) {
      // Get text after "Scriptures:"
      const afterScriptures = fullText.substring(scripturesIndex + 11).trim();
      // Extract everything until the next label or end
      const scripturesLines = afterScriptures.split('\n').map(l => l.trim()).filter(l => l.length > 0);
      if (scripturesLines.length > 0) {
        // Take the first non-empty line as the scripture reference
        scriptures = scripturesLines[0];
        // If it looks like a label (ends with colon), skip it and take next line
        if (scriptures.endsWith(':')) {
          scriptures = scripturesLines.length > 1 ? scripturesLines[1] : '';
        }
      }
    }

    // Extract category - look for "Category:" followed by the value on next line(s)
    const categoryIndex = fullText.indexOf('Category:');
    if (categoryIndex !== -1) {
      // Get text after "Category:"
      const afterCategory = fullText.substring(categoryIndex + 9).trim();
      // Extract everything until the next label or end
      const categoryLines = afterCategory.split('\n').map(l => l.trim()).filter(l => l.length > 0);
      if (categoryLines.length > 0) {
        // Take the first non-empty line as the category
        category = categoryLines[0];
        // If it looks like a label (ends with colon), skip it and take next line
        if (category.endsWith(':')) {
          category = categoryLines.length > 1 ? categoryLines[1] : '';
        }
      }
    }

    // Extract and download PDF/PPTX files
    const files = {
      pdf: null,
      pptx: null
    };

    // Check for PPTX download link
    const pptxLink = $('a[href*="download=pptx"]').first();
    if (pptxLink.length > 0) {
      const pptxUrl = `https://www.myanmarhymn.com/${pptxLink.attr('href')}`;
      const pptxFilename = `hymn-${id}.pptx`;
      const pptxPath = path.join(FILES_DIR, pptxFilename);

      console.log(`  📥 Downloading PPTX...`);
      const pptxDownloaded = await downloadFile(pptxUrl, pptxPath);
      if (pptxDownloaded) {
        files.pptx = `files/${pptxFilename}`;
        console.log(`  ✓ PPTX downloaded`);
      }
    }

    // Check for PDF download link
    const pdfLink = $('a[href*="download=pdf"]').first();
    if (pdfLink.length > 0) {
      const pdfUrl = `https://www.myanmarhymn.com/${pdfLink.attr('href')}`;
      const pdfFilename = `hymn-${id}.pdf`;
      const pdfPath = path.join(FILES_DIR, pdfFilename);

      console.log(`  📥 Downloading PDF...`);
      const pdfDownloaded = await downloadFile(pdfUrl, pdfPath);
      if (pdfDownloaded) {
        files.pdf = `files/${pdfFilename}`;
        console.log(`  ✓ PDF downloaded`);
      }
    }

    const hymn = {
      hymn_number: hymnNumber,
      title_mm: titleMM,
      title_en: titleEN,
      composer: composer,
      scriptures: scriptures,
      category: category,
      lyrics: lyrics,
      files: files,
      url: `${BASE_URL}${id}`,
      scraped_at: new Date().toISOString()
    };

    // Validate that we got meaningful content
    if (titleMM.length < 2) {
      console.log(`⚠️  Skipped hymn ${id} - No title found`);
      return null;
    }

    console.log(`✓ Hymn ${id}: ${titleMM}${titleEN ? ' / ' + titleEN : ''}`);
    return hymn;

  } catch (error) {
    if (error.response?.status === 404) {
      console.log(`⚠️  Hymn ${id} not found (404)`);
    } else {
      console.error(`✗ Error scraping hymn ${id}:`, error.message);
    }
    return null;
  }
}

/**
 * Convert Myanmar digit to Arabic number
 * @param {string} myanmarDigit - Myanmar digit character
 * @returns {number} - Arabic number
 */
function myanmarDigitToArabic(myanmarDigit) {
  const digits = {
    '၀': 0, '၁': 1, '၂': 2, '၃': 3, '၄': 4,
    '၅': 5, '၆': 6, '၇': 7, '၈': 8, '၉': 9
  };
  return digits[myanmarDigit] || 1;
}

/**
 * Scrape multiple hymns within a range
 * @param {number} startId - Starting hymn ID
 * @param {number} endId - Ending hymn ID
 * @param {number} delay - Delay between requests in ms (default: 2000)
 * @returns {Array} - Array of hymn objects
 */
async function scrapeMultipleHymns(startId, endId, delay = 2000) {
  console.log(`\n🚀 Starting scrape from hymn ${startId} to ${endId}`);
  console.log(`⏱️  Delay: ${delay}ms between requests\n`);

  const results = [];
  const skipped = [];

  for (let id = startId; id <= endId; id++) {
    const hymn = await scrapeHymn(id);

    if (hymn) {
      results.push(hymn);
    } else {
      skipped.push(id);
    }

    // Rate limiting - delay between requests
    if (id < endId) {
      await new Promise(resolve => setTimeout(resolve, delay));
    }
  }

  // Save results to JSON file
  const timestamp = new Date().toISOString().replace(/[:.]/g, '-').substring(0, 19);
  const filename = `myanmar-hymns-${startId}-${endId}-${timestamp}.json`;
  const outputPath = path.join(OUTPUT_DIR, filename);

  fs.writeFileSync(outputPath, JSON.stringify({
    meta: {
      scraped_at: new Date().toISOString(),
      total_hymns: results.length,
      range: `${startId}-${endId}`,
      skipped_ids: skipped
    },
    hymns: results
  }, null, 2));

  // Print summary
  console.log('\n' + '='.repeat(50));
  console.log('📊 SCRAPING SUMMARY');
  console.log('='.repeat(50));
  console.log(`✅ Successfully scraped: ${results.length} hymns`);
  console.log(`⚠️  Skipped/Not found: ${skipped.length} hymns`);
  if (skipped.length > 0) {
    console.log(`   Skipped IDs: ${skipped.join(', ')}`);
  }
  console.log(`💾 Saved to: output/${filename}`);
  console.log('='.repeat(50) + '\n');

  return results;
}

/**
 * Scrape hymns and output as Laravel-ready format
 */
async function scrapeForLaravel(startId, endId) {
  const hymns = await scrapeMultipleHymns(startId, endId);

  // Transform to Laravel-friendly format
  const laravelFormat = {
    hymn_book_id: 1, // Default hymn book
    title_mm: '',
    title_en: '',
    composer: '',
    category: '',
    scriptures: '',
    hymns: hymns.map(hymn => ({
      hymn_number: hymn.hymn_number,
      title_mm: hymn.title_mm,
      title_en: hymn.title_en,
      composer: hymn.composer,
      category: hymn.category,
      scriptures: hymn.scriptures,
      pdf_file: hymn.files?.pdf || null,
      pptx_file: hymn.files?.pptx || null,
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString(),
      details: hymn.lyrics.map(verse => ({
        verse_number: verse.verse_number,
        content_mm: verse.content,
        sort_order: verse.verse_number,
        created_at: new Date().toISOString(),
        updated_at: new Date().toISOString()
      }))
    }))
  };

  const laravelPath = path.join(OUTPUT_DIR, `laravel-hymns-${startId}-${endId}.json`);
  fs.writeFileSync(laravelPath, JSON.stringify(laravelFormat, null, 2));
  console.log(`💾 Laravel-ready format saved to: output/${path.basename(laravelPath)}`);

  return laravelFormat;
}

/**
 * Create CSV export for easy viewing
 */
async function scrapeToCSV(startId, endId) {
  const hymns = await scrapeMultipleHymns(startId, endId);

  // Create CSV header
  let csv = 'Hymn Number,Title (Myanmar),Title (English),Composer,Category,Scriptures,PDF,PPTX,Verse Count\n';

  // Add each hymn as a row
  hymns.forEach(hymn => {
    // Escape commas and quotes in CSV fields
    const escapeCsv = (text) => {
      if (!text) return '';
      return '"' + text.replace(/"/g, '""') + '"';
    };

    csv += [
      hymn.hymn_number,
      escapeCsv(hymn.title_mm),
      escapeCsv(hymn.title_en),
      escapeCsv(hymn.composer),
      escapeCsv(hymn.category),
      escapeCsv(hymn.scriptures),
      escapeCsv(hymn.files?.pdf || ''),
      escapeCsv(hymn.files?.pptx || ''),
      hymn.lyrics.length
    ].join(',') + '\n';
  });

  const csvPath = path.join(OUTPUT_DIR, `myanmar-hymns-${startId}-${endId}.csv`);
  fs.writeFileSync(csvPath, csv, 'utf8');
  console.log(`💾 CSV export saved to: output/${path.basename(csvPath)}`);

  return hymns;
}

// CLI interface
async function main() {
  const args = process.argv.slice(2);

  if (args.length === 0) {
    console.log(`
🎵 Myanmar Hymn Scraper
=======================

Usage:
  node index.js <start_id> <end_id> [options]

Examples:
  node index.js 12 12              # Scrape single hymn (ID 12)
  node index.js 1 50               # Scrape hymns 1-50
  node index.js 1 100 --laravel    # Scrape and output Laravel format
  node index.js 1 50 --csv         # Scrape and export to CSV
  node index.js 1 10 --fast        # Scrape with 500ms delay (default: 2000ms)

Options:
  --laravel    Output in Laravel-ready format
  --csv        Export to CSV for easy viewing
  --fast       Use 500ms delay instead of 2000ms
  --slow       Use 5000ms delay

Data Fields Extracted:
  • Hymn Number
  • Title (Myanmar)
  • Title (English)
  • Composer
  • Category
  • Scriptures
  • Lyrics (with verse numbers)
  • PDF & PPTX files (downloaded to output/files/)

Output:
  • JSON: output/myanmar-hymns-{range}-{timestamp}.json
  • CSV:  output/myanmar-hymns-{range}.csv
  • Laravel: output/laravel-hymns-{range}.json
  • Files: output/files/hymn-{id}.pdf & .pptx

Example: node index.js 12 12 --csv
`);
    process.exit(0);
  }

  const startId = parseInt(args[0]);
  const endId = parseInt(args[1]);
  const isLaravel = args.includes('--laravel');
  const isCSV = args.includes('--csv');
  const isFast = args.includes('--fast');
  const isSlow = args.includes('--slow');

  const delay = isFast ? 500 : (isSlow ? 5000 : 2000);

  if (isNaN(startId) || isNaN(endId)) {
    console.error('❌ Error: start_id and end_id must be valid numbers');
    process.exit(1);
  }

  if (startId > endId) {
    console.error('❌ Error: start_id must be less than or equal to end_id');
    process.exit(1);
  }

  try {
    if (isLaravel) {
      await scrapeForLaravel(startId, endId);
    } else if (isCSV) {
      await scrapeToCSV(startId, endId);
    } else {
      await scrapeMultipleHymns(startId, endId, delay);
    }
    console.log('✅ Scraping complete!\n');
  } catch (error) {
    console.error('❌ Fatal error:', error.message);
    process.exit(1);
  }
}

// Export functions for testing or module usage
module.exports = {
  scrapeHymn,
  scrapeMultipleHymns,
  scrapeForLaravel,
  scrapeToCSV
};

// Run if executed directly
if (require.main === module) {
  main();
}
