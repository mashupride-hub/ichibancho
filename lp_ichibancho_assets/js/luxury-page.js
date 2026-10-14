(() => {
  const page = document.querySelector('.ch-page');
  if (!page) return;
  page.querySelectorAll('[data-sales]').forEach(section => {
    const grid = section.querySelector('.ch-products');
    if (!grid) return;
    const cards = [...grid.querySelectorAll('.ch-product')];
    const filters = [...section.querySelectorAll('[data-sales-filter]')];
    const sort = section.querySelector('.ch-sales-sort select');
    const empty = section.querySelector('.ch-sales-empty');
    let activeFilter = 'all';
    let favorites = [];
    try { favorites = JSON.parse(localStorage.getItem('mandai-favorites') || '[]'); if (!Array.isArray(favorites)) favorites = []; } catch (_) { favorites = []; }
    const tags = card => { try { return JSON.parse(card.dataset.salesTags || '[]'); } catch (_) { return []; } };
    const refresh = () => {
      let count = 0;
      const ordered = [...cards].sort((a, b) => {
        if (sort.value === 'newest') return Number(b.dataset.salesDate || 0) - Number(a.dataset.salesDate || 0);
        const left = Number(a.dataset.salesPrice), right = Number(b.dataset.salesPrice);
        if (left < 0 || right < 0) return left < 0 ? (right < 0 ? 0 : 1) : -1;
        return sort.value === 'price-asc' ? left - right : right - left;
      });
      ordered.forEach(card => {
        card.hidden = activeFilter !== 'all' && !tags(card).includes(activeFilter);
        if (!card.hidden) count++;
        grid.append(card);
      });
      empty.hidden = count !== 0;
    };
    filters.forEach(button => button.addEventListener('click', () => {
      activeFilter = button.dataset.salesFilter;
      filters.forEach(other => other.setAttribute('aria-pressed', String(other === button)));
      refresh();
    }));
    sort.addEventListener('change', refresh);
    section.querySelector('.ch-sales-controls').hidden = false;
    section.querySelectorAll('[data-favorite-id]').forEach(button => {
      const name = (button.getAttribute('aria-label') || '').replace(/をお気に入りに追加$/, '');
      const updateFavorite = () => {
        const selected = favorites.includes(button.dataset.favoriteId);
        button.setAttribute('aria-pressed', String(selected));
        button.setAttribute('aria-label', name + (selected ? 'をお気に入りから削除' : 'をお気に入りに追加'));
      };
      button.addEventListener('click', () => {
        const id = button.dataset.favoriteId;
        favorites = favorites.includes(id) ? favorites.filter(value => value !== id) : [...favorites, id];
        try { localStorage.setItem('mandai-favorites', JSON.stringify(favorites)); } catch (_) { /* Keep in-page state if storage is unavailable. */ }
        updateFavorite();
      });
      updateFavorite();
    });
    refresh();
  });
  page.querySelectorAll('[data-discovery]').forEach(widget => {
    const tabList = widget.querySelector('[role="tablist"]');
    const tabs = [...tabList.querySelectorAll('[role="tab"]')];
    const panels = tabs.map(tab => document.getElementById(tab.getAttribute('aria-controls')));
    const panelBox = widget.querySelector('.ch-discovery-panels');
    const showAll = widget.querySelector('[data-show-all]');
    const more = widget.querySelector('.ch-more-categories');
    const updateAll = () => {
      const expanded = activeIndex === 1 && more.open;
      showAll.setAttribute('aria-expanded', String(expanded));
      showAll.textContent = expanded ? '取扱商材を少なく表示する −' : 'すべての取扱ブランド・カテゴリーを見る（18カテゴリー） →';
    };
    let activeIndex = 0;
    const activate = (index, focus = false) => {
      activeIndex = index;
      tabs.forEach((tab, i) => {
        tab.setAttribute('aria-selected', String(i === index));
        tab.tabIndex = i === index ? 0 : -1;
        panels[i].hidden = i !== index;
        panels[i].inert = i !== index;
      });
      if (focus) tabs[index].focus();
      updateAll();
    };
    const measure = () => {
      const more = widget.querySelector('.ch-more-categories');
      const wasOpen = more.open;
      more.open = false;
      panelBox.style.minHeight = '0';
      let height = 0;
      panels.forEach(panel => { panel.hidden = false; height = Math.max(height, panel.getBoundingClientRect().height); panel.hidden = true; });
      panelBox.style.minHeight = Math.ceil(height) + 'px';
      more.open = wasOpen;
      activate(activeIndex);
    };
    panels.forEach((panel, i) => { panel.setAttribute('role', 'tabpanel'); panel.setAttribute('aria-labelledby', tabs[i].id); });
    widget.classList.add('ch-discovery-ready');
    tabList.hidden = false;
    showAll.hidden = false;
    showAll.addEventListener('click', () => {
      const expand = activeIndex !== 1 || !more.open;
      activate(1);
      more.open = expand;
      updateAll();
    });
    more.addEventListener('toggle', updateAll);
    activate(0);
    tabs.forEach((tab, index) => {
      tab.addEventListener('click', () => activate(index));
      tab.addEventListener('keydown', event => {
        let next;
        if (event.key === 'ArrowRight') next = (index + 1) % tabs.length;
        if (event.key === 'ArrowLeft') next = (index + tabs.length - 1) % tabs.length;
        if (event.key === 'Home') next = 0;
        if (event.key === 'End') next = tabs.length - 1;
        if (next !== undefined) { event.preventDefault(); activate(next, true); }
      });
    });
    window.addEventListener('resize', measure);
    widget.querySelectorAll('img').forEach(img => img.addEventListener('load', measure, { once: true }));
    document.fonts?.ready.then(measure);
    measure();
  });
  page.querySelectorAll('[data-purchase-methods]').forEach(widget => {
    const inputs = [...widget.querySelectorAll('input[type="radio"]')];
    const panels = [...widget.querySelectorAll('.ch-method-panel')];
    const updateMethod = () => {
      const value = inputs.find(input => input.checked)?.value || 'store';
      panels.forEach(panel => {
        const active = panel.id === 'ch-method-' + value;
        panel.classList.toggle('is-inactive', !active);
        panel.setAttribute('aria-hidden', String(!active));
        panel.inert = !active;
      });
    };
    widget.classList.add('ch-methods-ready');
    inputs.forEach(input => input.addEventListener('change', updateMethod));
    updateMethod();
  });
  page.querySelectorAll('.ch-category-brands').forEach(details => {
    const card = details.closest('.ch-category-card');
    card.addEventListener('click', event => { if (!event.target.closest('summary') && !event.target.closest('.ch-category-brand-panel')) details.open = !details.open; });
    card.addEventListener('keydown', event => { if (event.target === card && (event.key === 'Enter' || event.key === ' ')) { event.preventDefault(); details.open = !details.open; } });
    card.addEventListener('keydown', event => { if (event.key === 'Escape') details.open = false; });
    details.addEventListener('toggle', () => {
      if (details.open) page.querySelectorAll('.ch-category-brands').forEach(other => { if (other !== details) other.open = false; });
    });
    details.addEventListener('keydown', event => {
      if (event.key === 'Escape') { details.open = false; details.querySelector('summary').focus(); }
    });
  });
  document.addEventListener('click', event => {
    if (!event.target.closest('.ch-category-card')) page.querySelectorAll('.ch-category-brands').forEach(details => { details.open = false; });
  });
  // Category links open the actual matching group before the browser scrolls to it.
  const openResultGroup = () => {
    const id = window.location.hash.slice(1);
    if (!id.startsWith('ch-results-')) return;
    const group = document.getElementById(id);
    if (group && group.matches('details.ch-result-group')) group.open = true;
  };
  window.addEventListener('hashchange', openResultGroup);
  openResultGroup();
  // A failed image retains its slot without exposing broken-image icons.
  page.querySelectorAll('.ch-store-photo-frame img, .ch-result-image img').forEach(img => {
    const fallback = () => {
      img.hidden = true;
      img.parentElement.classList.add('ch-photo-empty');
      const label = img.parentElement.querySelector('.ch-photo-fallback');
      if (label) label.hidden = false;
    };
    img.addEventListener('error', fallback);
    if (img.complete && img.naturalWidth === 0) fallback();
  });
  const bar = page.querySelector('.ch-fixed-cta');
  if (!bar || page.dataset.fixedCta !== 'on') return;
  const mobile = window.matchMedia('(max-width: 640px)');
  const touchKeyboard = () => {
    const element = document.activeElement;
    return element?.tagName === 'TEXTAREA' || element?.tagName === 'SELECT' || (element?.tagName === 'INPUT' && !['radio','checkbox','button','submit'].includes(element.type));
  };
  let configured = [];
  try { configured = JSON.parse(page.dataset.ctaExclude || '[]'); } catch (_) { /* Use conservative defaults. */ }
  const selectors = ['[data-mandai-fixed-cta]', '#footer-fixed', '#fixed-footer', '.footer-fixed', '.fixed-footer', ...configured];
  let pending = false;
  function update() {
    pending = false;
    let sharedBar = false;
    // Also detect unknown shared fixed controls near the bottom of the viewport.
    let controls = [...document.querySelectorAll('footer, [role="contentinfo"], body > nav, body > aside, a[href^="tel:"], a[href*="line.me"]')];
    selectors.forEach(selector => { try { controls.push(...document.querySelectorAll(selector)); } catch (_) { /* Invalid admin selector is ignored. */ } });
    for (const control of controls) {
      if (page.contains(control)) continue;
      let element = control;
      while (element && element !== document.body) {
        const style = getComputedStyle(element);
        const rect = element.getBoundingClientRect();
        if (style.position === 'fixed' && style.display !== 'none' && style.visibility !== 'hidden' && rect.height > 0 && rect.top < window.innerHeight && rect.bottom >= window.innerHeight - 140) { sharedBar = true; break; }
        element = element.parentElement;
      }
      if (sharedBar) break;
    }
    const footer = document.querySelector('footer, [role="contentinfo"]');
    const footerVisible = footer && footer.getBoundingClientRect().top < window.innerHeight;
    const hero = page.querySelector('.ch-hero');
    const heroVisible = hero && hero.getBoundingClientRect().bottom > 100;
    const active = mobile.matches && !sharedBar && !footerVisible && !heroVisible && !touchKeyboard();
    bar.classList.toggle('is-visible', active);
    page.classList.toggle('ch-has-fixed-cta', active);
    bar.setAttribute('aria-hidden', String(!active));
    // inert prevents keyboard focus when the visually hidden bar is inactive.
    bar.inert = !active;
  }
  const schedule = () => { if (!pending) { pending = true; requestAnimationFrame(update); } };
  window.addEventListener('scroll', schedule, { passive: true });
  window.addEventListener('resize', schedule);
  document.addEventListener('focusin', schedule);
  document.addEventListener('focusout', schedule);
  window.visualViewport?.addEventListener('resize', schedule);
  mobile.addEventListener('change', schedule);
  new MutationObserver(schedule).observe(document.body, { childList: true, subtree: true });
  update();
})();
