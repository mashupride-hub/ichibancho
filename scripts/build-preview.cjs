// Execute the real PHP templates with CLI-only WordPress contract stubs.
// Local preview includes explicitly labeled demo products requested for design review.
const fs = require('node:fs');
const path = require('node:path');
const { execFileSync } = require('node:child_process');
const root = path.resolve(__dirname, '..');
const localPhp = path.join(root, '.local-tools/php/php.exe');
const php = process.env.MANDAI_PHP || (fs.existsSync(localPhp) ? localPhp : 'php');
const data = JSON.parse(fs.readFileSync(path.join(root, 'lp_ichibancho_assets/data/content.json'), 'utf8'));
const before = fs.readFileSync(path.join(root, 'tests/fixtures/page-before-ux.php.txt'), 'utf8');
const beforeFaqs = [...before.matchAll(/<details><summary>(.*?)<\/summary><div class="ch-faq-answer">([\s\S]*?)<\/div><\/details>/g)];
if (data.faqs.length !== 15 || data.faqs.filter(x => x.featured).length !== 6) throw Error('FAQ counts');
data.faqs.forEach((item, index) => { if (item.question !== beforeFaqs[index][1] || item.answer !== beforeFaqs[index][2]) throw Error('FAQ content changed'); });
if (data.results.length !== 4) throw Error('Purchase records lost');
for (const mode of ['main', 'faq']) {
  const html = execFileSync(php, ['-n', path.join(root, 'tests/render.php'), mode], { cwd: root, encoding: 'utf8' });
  if (!html.startsWith('<!doctype html>') || /Warning:|Fatal error:|Deprecated:/.test(html)) throw Error('PHP render error');
  const ids = [...html.matchAll(/\bid="([^"]+)"/g)].map(m => m[1]);
  if (ids.length !== new Set(ids).size) throw Error('Duplicate IDs');
  for (const m of html.matchAll(/href="#([^"]+)"/g)) if (!ids.includes(m[1])) throw Error('Broken anchor: ' + m[1]);
  for (const m of html.matchAll(/(?:src|href)="(lp_ichibancho_assets\/[^"?]+)/g)) if (!fs.existsSync(path.join(root, m[1]))) throw Error('Missing asset: ' + m[1]);
  if (mode === 'main') {
    if (html.includes('ch-product-placeholder')) throw Error('Dummy product cards must not be rendered');
    if ((html.match(/class="ch-product ch-demo-product"/g) || []).length !== 8 || !html.includes('実際の販売在庫ではありません')) throw Error('Preview demo cards must be clearly identified');
    if (html.includes('ch-store-photo-frame ch-photo-empty')) throw Error('Unconfigured store photos must not render');
    if (html.includes('class="ch-faq-more"')) throw Error('Additional FAQs should not expand on the homepage');
    if (/href="(?:https?:\/\/[^" ]+)?\/(?:shop|faq|apply)\//.test(html)) throw Error('Invented route');
    if (/LUXURY BRAND BUY|OUR STRENGTH|THE VALUE|SCROLL|PICK UP|FIND YOUR/.test(html)) throw Error('English decoration returned');
  }
  fs.writeFileSync(path.join(root, mode === 'main' ? 'preview.html' : 'preview-faq.html'), html);
}
const cssPath = path.join(root, 'lp_ichibancho_assets/css/chrome-hearts.css');
const css = fs.readFileSync(cssPath, 'utf8');
for (const m of css.matchAll(/url\('([^']+)'\)/g)) if (!fs.existsSync(path.resolve(path.dirname(cssPath), m[1]))) throw Error('Missing CSS asset');
console.log('PASS: local preview with 8 labeled sample products, unchanged 15 FAQs, featured 6, preserved 4 results, no empty store frames, assets and internal links.');
