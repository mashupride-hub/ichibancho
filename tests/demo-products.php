<?php
// CLI-only, explicitly marked design samples; never loaded in WordPress.
if (PHP_SAPI !== 'cli') { exit; }
function mandai_demo_product_cards() {
    $items = array(
        array('CHROME HEARTS', 'シルバーリング', 198000, 'categories.png', '0% 0%', array('chrome-hearts','jewelry')),
        array('LOUIS VUITTON', 'モノグラムバッグ', 148000, 'brands.png', '50% 0%', array('louis-vuitton','bag')),
        array('HERMÈS', 'レザーハンドバッグ', 2780000, 'brands.png', '100% 0%', array('hermes','bag')),
        array('ROLEX', 'ステンレスウォッチ', 1280000, 'brands.png', '0% 100%', array('rolex','watch')),
        array('CHANEL', 'キルティングバッグ', 698000, 'brands.png', '50% 100%', array('chanel','bag')),
        array('CHROME HEARTS', 'クロスペンダント', 158000, 'categories.png', '50% 0%', array('chrome-hearts','jewelry')),
        array('CHROME HEARTS', 'シルバーブレスレット', 138000, 'categories.png', '100% 0%', array('chrome-hearts','jewelry')),
        array('CHROME HEARTS', 'レザーウォレット', 108000, 'categories.png', '0% 100%', array('chrome-hearts','bag')),
    );
    $html = '<p class="ch-demo-notice">デザイン確認用のサンプルです。画像・商品名・価格はダミーで、実際の販売在庫ではありません。</p><div class="ch-products">';
    foreach ($items as $index => $item) {
        $html .= '<article class="ch-product ch-demo-product" data-sales-tags="' . esc_attr(wp_json_encode($item[5])) . '" data-sales-price="' . $item[2] . '" data-sales-date="' . (8 - $index) . '"><div class="ch-demo-visual" role="img" aria-label="' . esc_attr($item[1] . 'のサンプル画像') . '" style="background-image:url(\'lp_ichibancho_assets/images/chrome-hearts/' . $item[3] . '\');background-position:' . $item[4] . ';"><span>サンプル</span></div><div class="ch-product-info"><p class="ch-product-brand">' . esc_html($item[0]) . '</p><h3>' . esc_html($item[1]) . '</h3><div class="ch-price">¥' . number_format($item[2]) . '<small>表示サンプル価格</small></div></div>';
        ob_start(); \MandaiLuxury\favorite_button('demo-' . $index, $item[1]); $html .= ob_get_clean();
        $html .= '</article>';
    }
    return $html . '</div>';
}
