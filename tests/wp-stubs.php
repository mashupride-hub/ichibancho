<?php
// CLI-only contract harness, never load this file in WordPress or deploy it.
if (PHP_SAPI !== 'cli') { exit; }
define('ABSPATH', dirname(__DIR__) . '/');
$GLOBALS['ch_test_options'] = array();
$GLOBALS['ch_test_filters'] = array();
$GLOBALS['ch_test_terms'] = array();
$GLOBALS['ch_test_pages'] = array();
$GLOBALS['ch_test_products'] = array();
function get_theme_file_path($path) { return dirname(__DIR__) . $path; }
function get_theme_file_uri($path) { return ltrim($path, '/'); }
function get_option($key, $default = false) { return isset($GLOBALS['ch_test_options'][$key]) ? $GLOBALS['ch_test_options'][$key] : $default; }
function apply_filters($name, $value) { return isset($GLOBALS['ch_test_filters'][$name]) ? call_user_func($GLOBALS['ch_test_filters'][$name], $value) : $value; }
function wp_parse_url($url) { return parse_url($url); }
function esc_url_raw($url, $protocols = array()) { return preg_match('/^https?:\/\//', $url) ? $url : ''; }
function esc_url($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function esc_html($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function esc_attr($value) { return esc_html($value); }
function wp_json_encode($value) { return json_encode($value); }
function wp_kses_post($value) { return $value; }
function wp_enqueue_style($id, $url, $dependencies, $version) { $GLOBALS['ch_styles'][$id] = $url; }
function wp_enqueue_script($id, $url, $dependencies, $version, $footer) { $GLOBALS['ch_scripts'][$id] = $url; }
function get_posts($args) { return $GLOBALS['ch_test_pages']; }
function get_post_status($id) { return $id > 0 ? 'publish' : 'draft'; }
function get_permalink($id) { return 'https://example.test/page/' . (is_object($id) ? $id->ID : $id); }
function home_url($path = '') { return 'https://mandai.ne.jp' . $path; }
function is_wp_error($value) { return $value instanceof Exception; }
function taxonomy_exists($name) { return isset($GLOBALS['ch_test_terms'][$name]); }
function get_the_terms($id, $taxonomy) { return isset($GLOBALS['ch_test_terms'][$taxonomy]) ? $GLOBALS['ch_test_terms'][$taxonomy] : false; }
function wp_list_pluck($items, $field) { return array_map(function($item) use ($field) { return $item->$field; }, $items); }
function wc_attribute_label($name) { return $name; }
class WP_Query { function __construct($args) {} function have_posts() { return false; } }
function wp_reset_postdata() {}
function get_header() {
    echo '<!doctype html><html lang="ja"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>万代 ブランド買取・販売 本文プレビュー</title>';
    foreach ($GLOBALS['ch_styles'] as $url) { echo '<link rel="stylesheet" href="' . esc_url($url) . '">'; }
    echo '<style>body{margin:0}.preview-notice{margin:0;padding:10px 18px;background:#ede8da;color:#4a463c;font:12px/1.6 sans-serif;text-align:center}</style></head><body><p class="preview-notice">本文デザインの確認用。共通ヘッダー・フッター、商品在庫、お知らせは実環境で確認してください。</p>';
}
function get_footer() {
    foreach ($GLOBALS['ch_scripts'] as $url) { echo '<script src="' . esc_url($url) . '"></script>'; }
    echo '</body></html>';
}
