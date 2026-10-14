<?php
if (PHP_SAPI !== 'cli') { exit; }
require __DIR__ . '/wp-stubs.php';
// Only the local design preview receives explicitly labeled demo cards.
$faq = isset($argv[1]) && $argv[1] === 'faq';
ob_start();
require dirname(__DIR__) . ($faq ? '/page_brand_faq.php' : '/page_ichibancho.php');
$html = ob_get_clean();
if (!$faq) {
    require __DIR__ . '/demo-products.php';
    $sales_start = strpos($html, 'id="ch-pickup"');
    $sales_end = strpos($html, '</section>', $sales_start);
    $note_start = strpos($html, '<div class="ch-stock-note">', $sales_start);
    if ($note_start !== false && $note_start < $sales_end) {
        $note_end = strpos($html, '<p class="ch-sales-empty"', $note_start);
        $html = substr($html, 0, $note_start) . mandai_demo_product_cards() . substr($html, $note_end !== false && $note_end < $sales_end ? $note_end : $sales_end);
    }
}
echo $html;
