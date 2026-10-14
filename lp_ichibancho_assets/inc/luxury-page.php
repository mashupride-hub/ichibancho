<?php
namespace MandaiLuxury;
defined('ABSPATH') || exit;

function content() {
    static $data;
    if ($data === null) {
        $path = get_theme_file_path('/lp_ichibancho_assets/data/content.json');
        $decoded = is_readable($path) ? json_decode(file_get_contents($path), true) : array();
        $data = is_array($decoded) ? $decoded : array();
    }
    return apply_filters('mandai_luxury_content', $data);
}

function settings() {
    return apply_filters('mandai_luxury_settings', array_replace_recursive(content(), (array) get_option('mandai_luxury_lp_settings', array())));
}

// Only HTTP(S) URLs or a deliberately provided local fragment can be used.
function safe_url($value, $fragment = false) {
    if (!is_string($value)) { return ''; }
    $value = trim($value);
    if ($fragment && preg_match('/^#[a-zA-Z][a-zA-Z0-9_-]*$/', $value)) { return $value; }
    $parts = wp_parse_url($value);
    if (!$parts || empty($parts['host']) || empty($parts['scheme']) || !in_array(strtolower($parts['scheme']), array('http', 'https'), true)) { return ''; }
    return esc_url_raw($value, array('http', 'https'));
}

function links() {
    $settings = settings();
    $links = isset($settings['links']) ? $settings['links'] : array();
    $out = array();
    foreach (array('line', 'shop', 'purchase', 'faq', 'delivery') as $key) {
        $out[$key] = safe_url(isset($links[$key]) ? $links[$key] : '');
    }
    $out['line'] = safe_url(apply_filters('mandai_chrome_hearts_line_url', $out['line']));
    $out['shop'] = safe_url(apply_filters('mandai_chrome_hearts_shop_url', $out['shop']));
    // Resolve WooCommerce's actual published Shop page instead of inventing /shop/.
    if (!$out['shop'] && function_exists('wc_get_page_id')) {
        $id = (int) wc_get_page_id('shop');
        if ($id > 0 && get_post_status($id) === 'publish') { $out['shop'] = safe_url(get_permalink($id)); }
    }
    if (!$out['faq']) {
        $pages = get_posts(array('post_type' => 'page', 'post_status' => 'publish', 'numberposts' => 1, 'meta_key' => '_wp_page_template', 'meta_value' => 'page_brand_faq.php'));
        if ($pages) { $out['faq'] = safe_url(get_permalink($pages[0])); }
    }
    return apply_filters('mandai_luxury_links', $out);
}

function button($label, $url, $class = 'ch-button-dark', $fragment = false) {
    $url = safe_url($url, $fragment);
    if ($url) {
        echo '<a class="ch-button ' . esc_attr($class) . '" href="' . esc_url($url) . '">' . esc_html($label) . '<span aria-hidden="true">→</span></a>';
    } else {
        echo '<button class="ch-button ch-button-unavailable" type="button" disabled>' . esc_html($label) . '（準備中）</button>';
    }
}

function asset($file) {
    if (!is_string($file) || !$file || strpos($file, '..') !== false) { return ''; }
    if (preg_match('~^https?://~', $file)) { return safe_url($file); }
    $path = '/lp_ichibancho_assets/images/' . ltrim($file, '/');
    return is_file(get_theme_file_path($path)) ? get_theme_file_uri($path) : '';
}

function photo($file, $alt, $class = 'ch-store-photo-frame') {
    $url = asset($file);
    echo '<div class="' . esc_attr($class) . ($url ? '' : ' ch-photo-empty') . '">';
    if ($url) {
        echo '<img src="' . esc_url($url) . '" alt="' . esc_attr($alt) . '" loading="lazy" width="600" height="450">';
    }
    echo '<span class="ch-photo-fallback"' . ($url ? ' hidden' : '') . '>' . esc_html($alt) . '<small>写真は準備中です</small></span></div>';
}

function faqs($items) {
    foreach ($items as $item) {
        echo '<details><summary>' . esc_html($item['question']) . '</summary><div class="ch-faq-answer">' . wp_kses_post($item['answer']) . '</div></details>';
    }
}

function product_brand($product) {
    $settings = settings();
    foreach ((array) $settings['brand_taxonomies'] as $taxonomy) {
        if (!taxonomy_exists($taxonomy)) { continue; }
        $terms = get_the_terms($product->get_id(), $taxonomy);
        if (!is_wp_error($terms) && $terms) { return implode(' / ', wp_list_pluck($terms, 'name')); }
    }
    foreach ($product->get_attributes() as $attribute) {
        $name = $attribute->get_name();
        $label = function_exists('wc_attribute_label') ? wc_attribute_label($name) : $name;
        if (in_array(strtolower($name), $settings['brand_attributes'], true) || in_array(strtolower($label), $settings['brand_attributes'], true)) {
            $value = $product->get_attribute($name);
            if ($value !== '') { return $value; }
        }
    }
    // Category fallback is restricted to verified brand names, not generic bag/watch categories.
    $terms = get_the_terms($product->get_id(), 'product_cat');
    if (!is_wp_error($terms) && $terms) {
        $known = array('Chrome Hearts', 'CHROME HEARTS', 'クロムハーツ');
        foreach ($settings['brands'] as $brand) { $known = array_merge($known, $brand['aliases']); }
        $known = array_map('strtolower', $known);
        foreach ($terms as $term) { if (in_array(strtolower($term->name), $known, true)) { return $term->name; } }
    }
    return '';
}

function products() {
    if (!function_exists('wc_get_products')) { return array(); }
    $args = apply_filters('mandai_luxury_product_query', array('status' => 'publish', 'limit' => 8, 'stock_status' => 'instock', 'orderby' => 'date', 'order' => 'DESC', 'visibility' => 'catalog'));
    return wc_get_products($args);
}

function result_link($category) {
    $settings = settings();
    $map = isset($settings['links']['result_categories']) ? $settings['links']['result_categories'] : array();
    if (!empty($map[$category])) { return safe_url($map[$category]); }
    foreach ($settings['results'] as $item) {
        if (!empty($item['chrome_hearts']) && $item['category'] === $category) { return '#ch-results-' . preg_replace('/[^a-z0-9_-]/', '', $category); }
    }
    return '';
}

function result_cards($items) {
    foreach ($items as $item) {
        echo '<article class="ch-result" data-category="' . esc_attr($item['category']) . '">';
        photo($item['image'], $item['brand'] . ' ' . $item['name'], 'ch-result-image');
        echo '<div class="ch-product-info"><h3>' . esc_html($item['brand']) . '</h3><p>' . esc_html($item['name']) . '</p>';
        $url = !empty($item['url']) ? safe_url($item['url']) : '';
        if ($url) { echo '<a class="ch-text-link" href="' . esc_url($url) . '">実績の詳細 <span aria-hidden="true">→</span></a>'; }
        echo '</div></article>';
    }
}

function prioritized_results($items) {
    // Preserve order within each brand group; never create missing Chrome Hearts records.
    return array_merge(array_values(array_filter($items, function($item) { return !empty($item['chrome_hearts']); })), array_values(array_filter($items, function($item) { return empty($item['chrome_hearts']); })));
}

function category_cards($items) {
    foreach ($items as $category) {
        echo '<article class="ch-explore-card ch-category-card" tabindex="0" aria-label="' . esc_attr($category['name'] . 'の取扱商材') . '"><div class="ch-explore-image"><img src="' . esc_url(asset($category['image'])) . '" alt="' . esc_attr($category['name']) . '" width="500" height="380" loading="lazy"></div><details class="ch-category-brands"><summary class="ch-explore-info"><span>' . esc_html($category['name']) . '</span><span class="ch-map-sr">取扱ブランド・商材を表示</span></summary><div class="ch-category-brand-panel"><ul>';
        foreach ($category['brands'] as $name) { echo '<li>' . esc_html($name) . '</li>'; }
        echo '</ul><p>' . ($category['brands'] ? '掲載以外のお品物もお気軽にご相談ください。' : 'お取り扱いについては店舗へお気軽にお問い合わせください。') . '</p></div></details></article>';
    }
}

function product_facets($product, $brand_name) {
    $tags = array();
    $names = array_map('trim', explode(' / ', $brand_name));
    foreach (settings()['brands'] as $brand) {
        foreach ($brand['aliases'] as $alias) {
            foreach ($names as $name) { if (strtolower($alias) === strtolower($name)) { $tags[] = $brand['id']; } }
        }
    }
    if (!$tags) { $tags[] = 'other'; }
    $terms = get_the_terms($product->get_id(), 'product_cat');
    if ($terms && !is_wp_error($terms)) {
        $types = array('bag' => array('bag','bags','バッグ'), 'watch' => array('watch','watches','clock','時計','腕時計'), 'jewelry' => array('jewelry','jewellery','ジュエリー','アクセサリー'), 'apparel' => array('apparel','clothing','アパレル','衣料'));
        foreach ($terms as $term) {
            foreach ($types as $key => $aliases) {
                if (in_array(strtolower($term->name), $aliases, true) || (isset($term->slug) && in_array(strtolower($term->slug), $aliases, true))) { $tags[] = $key; }
            }
        }
    }
    return apply_filters('mandai_luxury_product_facets', array_values(array_unique($tags)), $product);
}

function sales_controls() {
    $filters = array('all' => 'すべて', 'chrome-hearts' => 'クロムハーツ', 'louis-vuitton' => 'ルイ・ヴィトン', 'hermes' => 'エルメス', 'rolex' => 'ロレックス', 'chanel' => 'シャネル', 'bag' => 'バッグ', 'watch' => '時計', 'jewelry' => 'ジュエリー', 'apparel' => 'アパレル', 'other' => 'その他');
    echo '<div class="ch-sales-controls" hidden><div class="ch-sales-filters" role="group" aria-label="表示中の販売商品の絞り込み">';
    foreach ($filters as $key => $label) { echo '<button type="button" data-sales-filter="' . esc_attr($key) . '" aria-pressed="' . ($key === 'all' ? 'true' : 'false') . '">' . esc_html($label) . '</button>'; }
    echo '</div><label class="ch-sales-sort"><span class="ch-map-sr">商品を並び替え</span><select aria-label="商品を並び替え"><option value="newest">新着順</option><option value="price-asc">価格の安い順</option><option value="price-desc">価格の高い順</option></select></label></div>';
}

function favorite_button($id, $name) {
    echo '<button type="button" class="ch-favorite" data-favorite-id="' . esc_attr($id) . '" aria-label="' . esc_attr($name . 'をお気に入りに追加') . '" aria-pressed="false"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21S2 15 2 8a5 5 0 0110-2A5 5 0 0122 8c0 7-10 13-10 13z"/></svg></button>';
}

function enqueue() {
    foreach (array('css' => 'chrome-hearts.css', 'js' => 'luxury-page.js') as $type => $file) {
        $relative = '/lp_ichibancho_assets/' . $type . '/' . $file;
        $version = is_file(get_theme_file_path($relative)) ? (string) filemtime(get_theme_file_path($relative)) : null;
        if ($type === 'css') { wp_enqueue_style('mandai-chrome-hearts', get_theme_file_uri($relative), array(), $version); }
        else { wp_enqueue_script('mandai-luxury-page', get_theme_file_uri($relative), array(), $version, true); }
    }
}
