<?php
if (PHP_SAPI !== 'cli') { exit; }
require __DIR__ . '/wp-stubs.php';
require dirname(__DIR__) . '/lp_ichibancho_assets/inc/luxury-page.php';
$checks = 0;
function check($condition, $message) {
    global $checks;
    if (!$condition) { throw new Exception($message); }
    $checks++;
}
$data = \MandaiLuxury\content();
check(count($data['faqs']) === 15, 'FAQ data lost');
check(count(array_filter($data['faqs'], function($faq) { return $faq['featured']; })) === 6, 'Featured FAQ count');
check(count($data['results']) === 4, 'Existing purchase records lost');
check(\MandaiLuxury\asset('../secret') === '', 'Asset traversal');
check(\MandaiLuxury\safe_url('javascript:alert(1)') === '', 'Unsafe URL');
check(\MandaiLuxury\safe_url('/invented-shop') === '', 'Unconfirmed relative route');
check(\MandaiLuxury\safe_url('#ch-store') === '', 'Shop must not point to unrelated section');
check(\MandaiLuxury\safe_url('#ch-request', true) === '#ch-request', 'Local request route');
check(\MandaiLuxury\links()['shop'] === '', 'Invented shop URL');
check(\MandaiLuxury\links()['purchase'] === '', 'Invented application URL');
check(\MandaiLuxury\links()['faq'] === '', 'Invented FAQ URL');
check(\MandaiLuxury\result_link('ring') === '', 'Invented Chrome Hearts result');
check(\MandaiLuxury\products() === array(), 'WooCommerce absent branch');
ob_start(); \MandaiLuxury\button('商品一覧', ''); $disabled = ob_get_clean();
check(strpos($disabled, 'disabled') !== false && strpos($disabled, 'href=') === false, 'Unset links must be disabled');
$GLOBALS['ch_test_pages'] = array((object)array('ID' => 55));
check(\MandaiLuxury\links()['faq'] === 'https://example.test/page/55', 'Published FAQ template resolution');
$GLOBALS['ch_test_pages'] = array();
$GLOBALS['ch_test_options']['mandai_luxury_lp_settings'] = array('links' => array('purchase' => 'https://example.test/apply', 'result_categories' => array('ring' => 'https://example.test/results/ring')));
check(\MandaiLuxury\links()['purchase'] === 'https://example.test/apply', 'Settings override');
check(\MandaiLuxury\result_link('ring') === 'https://example.test/results/ring', 'Configured result route');
$GLOBALS['ch_test_options'] = array();
class TestAttribute { function get_name() { return 'ブランド'; } }
class TestProduct {
    public $attributes = array();
    function get_id() { return 1; }
    function get_attributes() { return $this->attributes; }
    function get_attribute($key) { return $key === 'ブランド' ? '属性から取得したブランド' : ''; }
}
$product = new TestProduct();
$GLOBALS['ch_test_terms']['product_brand'] = array((object)array('name'=>'登録済みブランド'));
check(\MandaiLuxury\product_brand($product) === '登録済みブランド', 'Brand taxonomy');
$GLOBALS['ch_test_terms'] = array(); $product->attributes = array(new TestAttribute());
check(\MandaiLuxury\product_brand($product) === '属性から取得したブランド', 'Local brand attribute');
$product->attributes = array();
$GLOBALS['ch_test_terms']['product_cat'] = array((object)array('name'=>'バッグ'));
check(\MandaiLuxury\product_brand($product) === '', 'Generic category must not become brand');
$GLOBALS['ch_test_terms']['product_cat'] = array((object)array('name'=>'ロレックス'));
check(\MandaiLuxury\product_brand($product) === 'ロレックス', 'Verified brand category fallback');
$GLOBALS['ch_test_terms'] = array();
// Load optional WooCommerce fixtures only after testing its absence.
require __DIR__ . '/woo-fixtures.php';
$GLOBALS['ch_test_shop_id'] = -1;
check(\MandaiLuxury\links()['shop'] === '', 'Unset Woo Shop');
$GLOBALS['ch_test_shop_id'] = 66;
check(\MandaiLuxury\links()['shop'] === 'https://example.test/page/66', 'Woo Shop resolution');
$GLOBALS['ch_test_products'] = array(new FixtureProduct());
check(count(\MandaiLuxury\products()) === 1, 'Woo product source');
check($GLOBALS['ch_last_query']['visibility'] === 'catalog' && $GLOBALS['ch_last_query']['limit'] === 8, 'Catalog query and limit');
ob_start(); require dirname(__DIR__) . '/page_ichibancho.php'; $html = ob_get_clean();
check(strpos($html, '契約検証専用の商品') !== false, 'Product rendering');
check(strpos($html, '¥12,345') !== false, 'Price rendering');
check(strpos($html, '販売商品・最新入荷') !== false && strpos($html, '取扱ブランド・カテゴリー') !== false && strpos($html, 'OTHER LUXURY BRANDS') === false, 'Independent sales section without duplicate brand section');
check(strpos($html, 'LUXURY BRAND BUY') === false, 'Unrequested English decoration');
$GLOBALS['ch_test_filters']['mandai_luxury_settings'] = function($settings) {
    $settings['results'][] = array('id' => 'contract-only', 'brand' => '検証用ブランド', 'name' => 'カテゴリー動作検証専用', 'image' => '', 'category' => 'ring', 'chrome_hearts' => true, 'url' => '');
    return $settings;
};
check(\MandaiLuxury\result_link('ring') === '#ch-results-ring', 'Local category destination');
ob_start(); require dirname(__DIR__) . '/page_ichibancho.php'; $category_html = ob_get_clean();
check(strpos($category_html, 'id="ch-results-ring"') !== false, 'Real matching category group is rendered');
check(strpos($category_html, 'href="#ch-results-ring"') !== false, 'Category card links to matching group');
check(count($data['categories']) === 18, 'All handling categories preserved');
check(strpos($html, 'ch-product-placeholder') === false, 'No fake product cards');
check(strpos($html, 'ch-paths') === false, 'Duplicate entry cards removed');
$previous_source = file_get_contents(__DIR__ . '/fixtures/page-before-information-design.php.txt');
$current_source = file_get_contents(dirname(__DIR__) . '/page_ichibancho.php');
preg_match('~<section class="ch-hero"[\s\S]*?</section>~', $previous_source, $previous_hero);
preg_match('~<section class="ch-hero"[\s\S]*?</section>~', $current_source, $current_hero);
check(preg_replace('~^.*class="ch-hero-image".*$~m', '', $previous_hero[0]) === preg_replace('~^.*class="ch-hero-image".*$~m', '', $current_hero[0]), 'First view copy and CTAs preserved');
check(strpos($current_hero[0], 'images/ichibanchotop.png') !== false, 'Requested first view photo configured');
check(substr_count($current_source, 'get_header();') === 1 && substr_count($current_source, 'get_footer();') === 1, 'Shared WordPress chrome preserved');
check(strpos($current_source, 'application/ld+json') === false && strpos($current_source, '<meta') === false, 'No competing SEO output added');
$ordered = array('id="ch-sell"', 'id="ch-category"', 'id="ch-chrome"', 'id="ch-results"', 'id="ch-buy-guide"', 'id="ch-pickup"', 'id="ch-faq"', 'id="ch-store"');
$offset = -1;
foreach ($ordered as $id) { $position = strpos($html, $id); check($position !== false && $position > $offset, 'Section order: ' . $id); $offset = $position; }
$prioritized = \MandaiLuxury\prioritized_results(array_merge($data['results'], array(array('id' => 'fixture-priority', 'chrome_hearts' => true))));
check($prioritized[0]['id'] === 'fixture-priority' && $prioritized[1]['id'] === $data['results'][0]['id'], 'Stable Chrome Hearts priority');
check(strpos($html, '写真は準備中です') === false || strpos($html, 'ch-store-photo-frame ch-photo-empty') === false, 'No empty store photo frames');
echo "PASS: $checks contract checks. Synthetic product fixtures were used only in this test, never in previews.\n";
