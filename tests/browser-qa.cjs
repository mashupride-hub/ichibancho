const fs = require('node:fs');
const path = require('node:path');
const { spawn } = require('node:child_process');
const { pathToFileURL } = require('node:url');
const root = path.resolve(__dirname, '..');
const port = 9238;
(async () => {
const qaTimeout = setTimeout(() => { console.error('Browser QA timed out'); process.exit(1); }, 55000);
const child = spawn('C:/Program Files/Google/Chrome/Application/chrome.exe', ['--headless','--disable-gpu','--no-first-run','--allow-file-access-from-files',`--remote-debugging-port=${port}`,`--user-data-dir=${path.join(root,'.local-tools/chrome-cdp')}`,'about:blank'], { stdio:'ignore',windowsHide:true });
const delay = ms => new Promise(r => setTimeout(r,ms));
let ws;
const results = [];
try {
  let targets;
  for(let i=0;i<40;i++) { try { targets = await (await fetch(`http://127.0.0.1:${port}/json/list`)).json(); break; } catch (_) { await delay(250); } }
  if(!targets) throw Error('Chrome debugging unavailable');
  ws = new WebSocket(targets.find(x=>x.type==='page').webSocketDebuggerUrl);
  await new Promise((resolve,reject)=>{ws.addEventListener('open',resolve,{once:true});ws.addEventListener('error',reject,{once:true});});
  let sequence=0; const pending=new Map();
  ws.addEventListener('message',e=>{const m=JSON.parse(e.data);if(pending.has(m.id)){const p=pending.get(m.id);pending.delete(m.id);m.error?p.reject(Error(JSON.stringify(m.error))):p.resolve(m.result);}});
  const send=(method,params={})=>new Promise((resolve,reject)=>{const id=++sequence;pending.set(id,{resolve,reject});ws.send(JSON.stringify({id,method,params}));});
  const evaluate=async expression=>{const r=await send('Runtime.evaluate',{expression,returnByValue:true,awaitPromise:true});if(r.exceptionDetails)throw Error(JSON.stringify(r.exceptionDetails));return r.result.value;};
  const check=(value,label)=>{results.push({label,pass:!!value});if(!value)throw Error(label);};
  await send('Page.enable');
  await send('Page.navigate',{url:pathToFileURL(path.join(root,'preview.html')).href});
  await delay(1500);
  for(const width of [1440,768,390,320]) {
    await send('Emulation.setDeviceMetricsOverride',{width,height:900,deviceScaleFactor:1,mobile:width<=640});
    await delay(200);
    check(await evaluate('document.documentElement.scrollWidth <= innerWidth'),`No horizontal overflow at ${width}px`);
    const discoveryHeight = await evaluate("document.querySelector('.ch-discovery-panels').getBoundingClientRect().height");
    const brandRatio = await evaluate("(()=>{const r=document.querySelector('.ch-brand-photo').getBoundingClientRect();return r.width/r.height})()");
    check(await evaluate("!document.querySelector('#ch-discovery-brands').hidden && document.querySelector('#ch-discovery-products').hidden"),`Only selected brand panel visible at ${width}px`);
    await evaluate("document.querySelector('#ch-discovery-product-tab').click()");
    check(await evaluate("(()=>{const r=document.querySelector('.ch-explore-image').getBoundingClientRect();return Math.abs(r.width/r.height-"+brandRatio+")<0.01})()"),`Brand/product image ratios match at ${width}px`);
    check(await evaluate("document.querySelector('#ch-discovery-brands').hidden && !document.querySelector('#ch-discovery-products').hidden"),`Product tab switches one panel at ${width}px`);
    check(await evaluate("Math.abs(document.querySelector('.ch-discovery-panels').getBoundingClientRect().height-"+discoveryHeight+")<1"),`Compact tab height stable at ${width}px`);
    await evaluate("document.querySelector('.ch-more-categories > summary').click()");
    if (width === 1440) check(await evaluate("document.querySelector('input[name=chrome-purchase-method][value=store]').checked"),'Store purchase selected by default');
    for (const method of ['store','delivery','line']) {
      await evaluate("document.querySelector('input[name=chrome-purchase-method][value="+method+"]').click()");
      check(await evaluate("!document.querySelector('#ch-method-"+method+"').inert && document.querySelector('#ch-method-"+method+"').getAttribute('aria-hidden')==='false'"),`Purchase method ${method} selects at ${width}px`);
      check(await evaluate("[...document.querySelectorAll('.ch-method-panel')].filter(p=>getComputedStyle(p).display!=='none').length===1 && document.querySelector('#ch-method-"+method+"').querySelectorAll('.ch-method-flow li').length===3"),`Only selected three-step method visible at ${width}px`);
    }
    check(await evaluate("[...document.querySelectorAll('.ch-explore-image')].every(frame=>{const image=frame.querySelector('img');const f=frame.getBoundingClientRect(),i=image.getBoundingClientRect();return Math.abs(f.top-i.top)<1 && Math.abs(f.height-i.height)<1})"),`No image-frame white gaps at ${width}px`);
    for (let card=0;card<18;card++) {
      await evaluate("document.querySelectorAll('.ch-category-card')["+card+"].dispatchEvent(new PointerEvent('pointerenter',{pointerType:'mouse'}))");
      check(await evaluate("!document.querySelectorAll('.ch-category-brands')["+card+"].open"),`Hover does not open category ${card+1} at ${width}px`);
      await evaluate("document.querySelectorAll('.ch-category-card')["+card+"].click()");
      await delay(40);
      check(await evaluate("document.querySelectorAll('.ch-category-brands')["+card+"].open"),`Click opens category ${card+1} at ${width}px`);
      check(await evaluate("(()=>{const r=document.querySelectorAll('.ch-category-brand-panel')["+card+"].getBoundingClientRect();return r.left>=-1 && r.right<=innerWidth+1})()"),`Brand popup ${card+1} fits at ${width}px`);
      await evaluate("document.querySelectorAll('.ch-category-card')["+card+"].click()");
      check(await evaluate("!document.querySelectorAll('.ch-category-brands')["+card+"].open"),`Click closes category ${card+1} at ${width}px`);
    }
    await evaluate("document.querySelector('.ch-more-categories > summary').click();document.querySelector('#ch-discovery-brand-tab').click()");
  }
  check(await evaluate("document.querySelectorAll('#ch-category .ch-category-card').length===18 && document.querySelectorAll('.ch-discovery-brand').length===6 && document.querySelector('[role=tablist]')"),'All categories and brands retained with tabs');
  check(await evaluate("document.querySelector('#ch-category').nextElementSibling.id==='ch-chrome'"),'Categories precede Chrome Hearts');
  check(await evaluate("document.querySelectorAll('.ch-social-content > a').length===3"),'SNS links retained');
  check(await evaluate("document.querySelectorAll('#ch-buy-guide h2').length===1"),'Unified purchase guidance has one main heading');
  check(await evaluate("Boolean(document.querySelector('#ch-buy-guide #ch-flow')) && Boolean(document.querySelector('#ch-buy-guide #ch-request'))"),'Flow and method selection share one guidance section');
  check(await evaluate("Boolean(document.querySelector('#ch-map').compareDocumentPosition(document.querySelector('.ch-store-social-block')) & Node.DOCUMENT_POSITION_FOLLOWING)"),'Visit map precedes social information');
  check(await evaluate("Array.from(document.querySelectorAll('.ch-store-supplement')).every(e=>!e.open) && document.querySelectorAll('.ch-social-qr-grid img').length===3"),'Photos and preserved QR codes are initially collapsed');
  check(await evaluate("document.querySelectorAll('#ch-pickup .ch-button').length===1"),'Sales list CTA appears once');
  await evaluate("document.querySelector('.ch-store-social-block .ch-store-supplement summary').click()");
  check(await evaluate("document.querySelector('.ch-store-social-block .ch-store-supplement').open && document.querySelector('.ch-social-qr-grid').getBoundingClientRect().height>0"),'QR disclosure opens by click');
  await evaluate("document.querySelector('.ch-store-social-block .ch-store-supplement summary').click()");

  await evaluate("document.querySelector('[data-show-all]').click()");
  check(await evaluate("!document.querySelector('#ch-discovery-products').hidden && document.querySelector('.ch-more-categories').open && document.querySelector('[data-show-all]').getAttribute('aria-expanded')==='true'"),'View-all button selects products and reveals all 18 categories');
  await evaluate("document.querySelector('[data-show-all]').click()");
  check(await evaluate("!document.querySelector('.ch-more-categories').open && document.querySelector('[data-show-all]').getAttribute('aria-expanded')==='false'"),'View-all button collapses extra categories');
  await evaluate("document.querySelector('#ch-discovery-product-tab').click();document.querySelector('.ch-category-brands > summary').click()");
  check(await evaluate("document.querySelector('.ch-category-brands').open"),'Tap/click opens brands');
  await evaluate("document.querySelector('#ch-category-title').click()");
  check(await evaluate("!document.querySelector('.ch-category-brands').open"),'Clicking outside closes category detail');
  await evaluate("document.querySelector('#ch-discovery-brand-tab').click()");
  check(await evaluate("document.querySelector('.ch-faq-list').children.length === 6"),'Six top FAQs');
  check(await evaluate("!document.querySelector('.ch-faq-more')"),'Homepage does not expand additional FAQs');
  await evaluate("document.querySelector('.ch-faq-list > details > summary').click()");
  check(await evaluate("document.querySelector('.ch-faq-list > details').open"),'Featured FAQ expands');
  await evaluate("document.querySelector('.ch-faq-list > details > summary').click()");
  check(await evaluate("document.querySelectorAll('.ch-product-placeholder').length === 0"),'No dummy product cards');
  check(await evaluate("document.querySelectorAll('.ch-demo-product').length===8 && document.querySelector('.ch-demo-notice').textContent.includes('実際の販売在庫ではありません')"),'Eight clearly marked design samples in local preview');
  await evaluate("document.querySelector('[data-sales-filter=chrome-hearts]').click()");
  check(await evaluate("[...document.querySelectorAll('.ch-demo-product')].filter(card=>!card.hidden).length===4"),'Chrome Hearts product filter');
  await evaluate("document.querySelector('[data-sales-filter=apparel]').click()");
  check(await evaluate("!document.querySelector('.ch-sales-empty').hidden"),'Empty filter result displays honest message');
  await evaluate("document.querySelector('[data-sales-filter=all]').click();const select=document.querySelector('.ch-sales-sort select');select.value='price-asc';select.dispatchEvent(new Event('change'))");
  check(await evaluate("Number(document.querySelector('.ch-sales .ch-products').firstElementChild.dataset.salesPrice)===108000"),'Price ascending sort');
  await evaluate("document.querySelector('.ch-sales-sort select').value='newest';document.querySelector('.ch-sales-sort select').dispatchEvent(new Event('change'))");
  const favoriteBefore=await evaluate("document.querySelector('.ch-sales .ch-favorite').getAttribute('aria-pressed')");
  await evaluate("document.querySelector('.ch-sales .ch-favorite').click()");
  check(await evaluate("document.querySelector('.ch-sales .ch-favorite').getAttribute('aria-pressed')!=="+JSON.stringify(favoriteBefore)), 'Favorite toggle');
  await evaluate("document.querySelector('.ch-sales .ch-favorite').click()");
  check(await evaluate("!document.querySelector('.ch-store .ch-photo-empty')"),'No empty store photo frames');
  check(await evaluate("document.querySelector('#ch-pickup .ch-sales-footer button').disabled"),'Unconfigured product list is disabled');
  check(await evaluate("[...document.querySelectorAll('img')].every(i=>!i.complete || i.naturalWidth>0)"),'No broken local images');
  await send('Emulation.setDeviceMetricsOverride',{width:390,height:844,deviceScaleFactor:1,mobile:true});
  await evaluate("window.scrollTo(0,0)"); await delay(250);
  check(await evaluate("!document.querySelector('.ch-fixed-cta').classList.contains('is-visible')"),'Fixed CTA hidden over hero');
  let image=await send('Page.captureScreenshot',{format:'png'});
  fs.writeFileSync(path.join(__dirname,'mobile-hero.png'),Buffer.from(image.data,'base64'));
  await evaluate("document.querySelector('#ch-chrome').scrollIntoView()"); await delay(350);
  check(await evaluate("document.querySelector('.ch-fixed-cta').classList.contains('is-visible')"),'Mobile fixed CTA visible after hero');
  image=await send('Page.captureScreenshot',{format:'png'});
  fs.writeFileSync(path.join(__dirname,'mobile-cta.png'),Buffer.from(image.data,'base64'));
  await evaluate("const shared=document.createElement('aside');shared.id='qa-shared';shared.style.cssText='position:fixed;bottom:0;left:0;right:0;height:60px;background:black';shared.innerHTML='<a href=\"tel:123\">QA</a>';document.body.append(shared)"); await delay(350);
  check(await evaluate("!document.querySelector('.ch-fixed-cta').classList.contains('is-visible')"),'Existing shared fixed CTA suppresses page CTA');
  await evaluate("document.querySelector('#qa-shared').remove()"); await delay(250);
  await evaluate("const input=document.createElement('input');input.id='qa-input';document.body.append(input);input.focus()"); await delay(250);
  check(await evaluate("!document.querySelector('.ch-fixed-cta').classList.contains('is-visible')"),'Fixed CTA hidden while input focused');
  await evaluate("document.querySelector('#qa-input').remove()");
  await evaluate("const footer=document.createElement('footer');footer.id='qa-footer';footer.style.cssText='height:120px;background:#111';document.body.append(footer);footer.scrollIntoView()"); await delay(250);
  check(await evaluate("!document.querySelector('.ch-fixed-cta').classList.contains('is-visible')"),'Page CTA does not overlap visible footer');
  await evaluate("document.querySelector('#qa-footer').remove()");
  await send('Emulation.setDeviceMetricsOverride',{width:1440,height:1000,deviceScaleFactor:1,mobile:false});
  await evaluate("document.querySelector('#ch-chrome').scrollIntoView()"); await delay(300);
  image=await send('Page.captureScreenshot',{format:'png'});
  fs.writeFileSync(path.join(__dirname,'desktop-chrome.png'),Buffer.from(image.data,'base64'));
  await evaluate("document.querySelector('#ch-category').scrollIntoView()"); await delay(300);
  image=await send('Page.captureScreenshot',{format:'png'});
  fs.writeFileSync(path.join(__dirname,'desktop-brands.png'),Buffer.from(image.data,'base64'));
  await evaluate("document.querySelector('#ch-store').scrollIntoView()"); await delay(300);
  image=await send('Page.captureScreenshot',{format:'png'});
  fs.writeFileSync(path.join(__dirname,'desktop-store.png'),Buffer.from(image.data,'base64'));
  check(await evaluate("getComputedStyle(document.querySelector('.ch-store-hero-photo img')).objectFit==='cover'"),'Store exterior fills its frame');
  check(await evaluate("[...document.querySelectorAll('.ch-sales-gallery img')].length===3 && [...document.querySelectorAll('.ch-sales-gallery img')].every(i=>i.complete && i.naturalWidth>0)"),'Three sales floor photos load');
  for (const width of [1440,390]) {
    await send('Emulation.setDeviceMetricsOverride',{width,height:width===390?844:1000,deviceScaleFactor:1,mobile:width===390});
    await evaluate("document.querySelector('#ch-discovery-brand-tab').click();document.querySelector('.ch-more-categories').open=false;window.scrollTo(0,0)");
    await delay(350);
    await evaluate("document.querySelector('input[name=chrome-purchase-method][value=store]').click();document.querySelector('#ch-buy-guide').scrollIntoView()"); await delay(300);
    check(await evaluate("document.querySelector('#ch-method-store').querySelectorAll('.ch-method-flow li').length===3"), 'Store guide has three steps at '+width+'px');
    check(await evaluate("document.querySelector('#ch-method-store .ch-identity-details').open=false;document.querySelector('#ch-method-store .ch-identity-details summary').click();document.querySelector('#ch-method-store .ch-identity-details').open"), 'Identity requirements open at '+width+'px');
    const guideTop=await evaluate("scrollY+document.querySelector(\".ch-guide-banner\").getBoundingClientRect().top");
    const guideHeight=await evaluate("document.querySelector('.ch-guide-banner').offsetHeight+document.querySelector('#ch-flow').offsetHeight");
    image=await send('Page.captureScreenshot',{format:'png',captureBeyondViewport:true,clip:{x:0,y:guideTop,width,height:guideHeight,scale:1}});
    fs.writeFileSync(path.join(__dirname,width===390?'mobile-guide.png':'desktop-guide.png'),Buffer.from(image.data,'base64'));
    const height = await evaluate('document.documentElement.scrollHeight');
    image=await send('Page.captureScreenshot',{format:'png',captureBeyondViewport:true,clip:{x:0,y:0,width,height,scale:1}});
    fs.writeFileSync(path.join(__dirname,width===390?'mobile-full.png':'desktop-full.png'),Buffer.from(image.data,'base64'));
    const order = await evaluate("['ch-sell','ch-category','ch-chrome','ch-results','ch-buy-guide','ch-pickup','ch-faq','ch-store'].map(id=>document.getElementById(id).getBoundingClientRect().top)");
    check(order.every((top,i)=>i===0||top>order[i-1]),`Purchase-led visual section order at ${width}px`);
    await evaluate("document.querySelector('#ch-discovery-product-tab').click();document.querySelector('#ch-category').scrollIntoView()"); await delay(200);
    image=await send('Page.captureScreenshot',{format:'png'});
    fs.writeFileSync(path.join(__dirname,width===390?'mobile-products.png':'desktop-products.png'),Buffer.from(image.data,'base64'));
    await evaluate("document.querySelector('#ch-pickup').scrollIntoView()"); await delay(200);
    image=await send('Page.captureScreenshot',{format:'png'});
    fs.writeFileSync(path.join(__dirname,width===390?'mobile-sales.png':'desktop-sales.png'),Buffer.from(image.data,'base64'));
  }
  await send('Page.navigate',{url:pathToFileURL(path.join(root,'preview-faq.html')).href}); await delay(700);
  check(await evaluate("document.querySelectorAll('.ch-faq-list > details').length === 15"),'FAQ page lists all 15');
  console.log(`PASS: ${results.length} browser checks. Desktop/mobile screenshots saved in tests/.`);
} finally {
  clearTimeout(qaTimeout);
  fs.writeFileSync(path.join(__dirname,'browser-results.json'),JSON.stringify(results,null,2));
  if(ws)ws.close();
  child.kill();
}
})().catch(error => { console.error(error.message); process.exitCode = 1; });


