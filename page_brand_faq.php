<?php
/** Template Name: ブランド買取 よくあるご質問 */
defined('ABSPATH') || exit;
require_once get_theme_file_path('/lp_ichibancho_assets/inc/luxury-page.php');
\MandaiLuxury\enqueue();
$ch_settings = \MandaiLuxury\settings();
$ch_links = \MandaiLuxury\links();
get_header();
?>
<script>document.body.setAttribute('data-loader', 'load');</script>
<main id="main_contents" class="ch-page ch-faq-page">
    <section class="ch-section ch-container" aria-labelledby="ch-faq-page-title"><div class="ch-section-heading"><h1 id="ch-faq-page-title">よくあるご質問</h1></div><div class="ch-faq-list"><?php \MandaiLuxury\faqs($ch_settings['faqs']); ?></div></section>
    <section class="ch-contact" aria-labelledby="ch-faq-contact-title"><div class="ch-container"><h2 id="ch-faq-contact-title" class="ch-unified-heading"><span class="ch-heading-text">ご不明な点は、お気軽にご相談ください</span></h2><div class="ch-actions"><?php \MandaiLuxury\button('LINE査定', $ch_links['line'], 'ch-button-gold'); ?><a class="ch-button ch-button-outline" href="tel:0227247219">電話で相談する<span aria-hidden="true">→</span></a></div></div></section>
</main>
<?php get_footer(); ?>
