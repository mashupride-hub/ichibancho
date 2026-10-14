<?php
if (PHP_SAPI !== 'cli') { exit; }
function wc_get_page_id($key) { return $GLOBALS['ch_test_shop_id']; }
function wc_get_products($args) { $GLOBALS['ch_last_query'] = $args; return $GLOBALS['ch_test_products']; }
class FixtureProduct extends TestProduct {
    function get_permalink() { return 'https://example.test/product/fixture'; }
    function get_image_id() { return 0; }
    function get_name() { return '契約検証専用の商品'; }
    function get_price() { return '12345'; }
    function get_price_html() { return '¥12,345'; }
}
