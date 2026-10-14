<?php
/** Template Name: 買取専門店一番町店 */
defined('ABSPATH') || exit;
require_once get_theme_file_path('/lp_ichibancho_assets/inc/luxury-page.php');
\MandaiLuxury\enqueue();
$ch_assets = get_theme_file_uri('/lp_ichibancho_assets/');
$ch_settings = \MandaiLuxury\settings();
$ch_links = \MandaiLuxury\links();
$ch_line = \MandaiLuxury\safe_url($ch_links['line']);
$ch_purchase = \MandaiLuxury\safe_url($ch_links['purchase']);
$ch_shop = \MandaiLuxury\safe_url($ch_links['shop']);
$ch_faq_url = \MandaiLuxury\safe_url($ch_links['faq']);
$ch_products = \MandaiLuxury\products();
$ch_categories = $ch_settings['categories'];
get_header();
?>
<script>document.body.setAttribute('data-loader', 'load');</script>
<main id="main_contents" class="ch-page" data-fixed-cta="<?php echo !empty($ch_settings['fixed_cta']) ? 'on' : 'off'; ?>" data-cta-exclude="<?php echo esc_attr(wp_json_encode($ch_settings['fixed_cta_exclude_selectors'])); ?>">
    <section class="ch-hero" aria-labelledby="ch-title">
        <img class="ch-hero-image" src="<?php echo esc_url($ch_assets . 'images/ichibanchotop.png'); ?>" alt="一番町店に展示されたクロムハーツのブレスレットとクロスペンダント" width="1620" height="1080" fetchpriority="high" loading="eager">
        <div class="ch-container ch-hero-inner">
            <h1 id="ch-title">CHROME<br>HEARTS</h1>
            <p class="ch-hero-lead">クロムハーツの買取・販売</p>
            <p class="ch-hero-copy">クロムハーツをはじめ、ルイ・ヴィトン、エルメス、<br>ロレックス、シャネルなど高級ブランドを幅広くお取り扱い。<br>大切な一品の買取も、次の一品との出会いも、万代へ。</p>
            <div class="ch-actions"><?php \MandaiLuxury\button('LINE査定', $ch_line, 'ch-button-gold'); \MandaiLuxury\button('買取申込', $ch_purchase ?: '#ch-request', 'ch-button-outline', true); \MandaiLuxury\button('販売商品を見る', '#ch-pickup', 'ch-button-outline', true); ?></div>
        </div>
    </section>

<nav class="ch-page-nav ch-container" aria-label="ページ内メニュー"><a href="#ch-category">取扱ブランド・カテゴリー</a><a href="#ch-chrome">クロムハーツの買取</a><a href="#ch-results">買取実績</a><a href="#ch-flow">買取の流れ</a><a href="#ch-pickup">販売商品</a><a href="#ch-faq">よくあるご質問</a><a href="#ch-store">店舗・アクセス</a></nav>
    <section class="ch-section ch-container ch-buy-intro" id="ch-sell" aria-labelledby="ch-intro-title"><div class="ch-intro-copy"><h2 id="ch-intro-title">ブランド品の価値を、丁寧に。</h2><p>クロムハーツ、ルイ・ヴィトン、エルメス、ロレックス、シャネルなど。<br>万代は、高級ブランド全般の買取と販売を行う総合店です。</p></div><div class="ch-intro-points"><article><svg viewBox="0 0 40 40" aria-hidden="true"><path d="M5 13l7-7h16l7 7-15 23zM5 13h30M12 6l8 30L28 6M12 6l8 7 8-7"/></svg><h3>幅広いブランドをお取り扱い</h3><p>バッグ・時計・ジュエリーから、<br>レザー・アパレルまで。</p></article><article><svg viewBox="0 0 40 40" aria-hidden="true"><circle cx="17" cy="17" r="11"/><path d="M25 25l10 10"/></svg><h3>一点から、無料で査定</h3><p>査定のみのご相談も歓迎。<br>ご納得いただいてからお手続きへ。</p></article><article><svg viewBox="0 0 40 40" aria-hidden="true"><path d="M9 15L20 7l11 8v21H9z"/></svg><h3>ご都合に合わせた買取方法</h3><p>LINE査定・店頭買取・宅配買取。<br>お品物の価値を一点ずつ確認します。</p></article></div></section>

    <section class="ch-section ch-container ch-explore" id="ch-category" aria-labelledby="ch-category-title">
        <div class="ch-section-heading"><div><h2 id="ch-category-title" class="ch-unified-heading"><span class="ch-heading-text">取扱ブランド・カテゴリー</span></h2><p class="ch-section-intro">ブランド名から、またはお品物の種類から、お取り扱いをご確認ください。</p></div></div>
        <div class="ch-discovery" data-discovery>
            <div class="ch-discovery-tabs" role="tablist" aria-label="取扱ブランド・商品の切替" hidden><button id="ch-discovery-brand-tab" role="tab" type="button" aria-controls="ch-discovery-brands" aria-selected="true">ブランドから探す</button><button id="ch-discovery-product-tab" role="tab" type="button" aria-controls="ch-discovery-products" aria-selected="false" tabindex="-1">商品から探す</button></div>
            <div class="ch-discovery-panels">
                <div id="ch-discovery-brands" class="ch-discovery-panel"><h3 class="ch-discovery-fallback-title">ブランドから探す</h3><div class="ch-explore-grid"><?php foreach ($ch_settings['brands'] as $ch_brand) : ?><div class="ch-explore-card ch-discovery-brand"><div class="ch-brand-photo" role="img" aria-label="<?php echo esc_attr($ch_brand['name'] . 'の取扱イメージ'); ?>"><span class="ch-brand-photo-image" style="background-position:<?php echo esc_attr($ch_brand['photo_position']); ?>;"></span></div><div class="ch-explore-info"><?php if ($ch_brand['id'] !== 'other') : ?><small><?php echo esc_html($ch_brand['display_name']); ?></small><?php endif; ?><span><?php echo esc_html($ch_brand['name']); ?></span></div></div><?php endforeach; ?></div></div>
                <div id="ch-discovery-products" class="ch-discovery-panel" hidden><h3 class="ch-discovery-fallback-title">商品から探す</h3><div class="ch-explore-grid"><?php \MandaiLuxury\category_cards(array_slice($ch_categories, 0, 6)); ?></div><details class="ch-more-categories" id="ch-extra-categories"><summary>すべての取扱商材を見る <span>（<?php echo count($ch_categories); ?>カテゴリー）</span></summary><div class="ch-explore-grid"><?php \MandaiLuxury\category_cards(array_slice($ch_categories, 6)); ?></div></details></div>
            </div>
            <button type="button" class="ch-show-all-categories" data-show-all aria-controls="ch-extra-categories" aria-expanded="false" hidden>すべての取扱ブランド・カテゴリーを見る（18カテゴリー） <span aria-hidden="true">→</span></button>
        </div>
    </section>

    <section class="ch-feature ch-chrome-purchase" id="ch-chrome" aria-labelledby="ch-feature-title"><div class="ch-container">
        <div class="ch-chrome-heading"><h2 id="ch-feature-title" class="ch-unified-heading"><span class="ch-heading-text">クロムハーツのお買取</span></h2></div>
        <p class="ch-chrome-intro">シルバーアクセサリーから財布・レザー、アパレルまで幅広くお取り扱い。<br>専門バイヤーが一点ずつ丁寧に査定いたします。お品物の状態や付属品を確認し、査定内容をご案内します。</p>
        <div class="ch-chrome-items">
        <?php foreach (array('ring' => array('リング', '0% 0%'), 'necklace' => array('ネックレス', '50% 0%'), 'bracelet' => array('ブレスレット', '100% 0%'), 'leather' => array('財布・レザー', '0% 100%'), 'apparel' => array('アパレル', '50% 100%')) as $ch_key => $ch_item) : $ch_result_url = \MandaiLuxury\result_link($ch_key); ?>
            <article class="ch-chrome-item"><div class="ch-chrome-item-visual" role="img" aria-label="<?php echo esc_attr($ch_item[0] . 'のイメージ'); ?>" style="background-position:<?php echo esc_attr($ch_item[1]); ?>;"></div><div class="ch-chrome-item-caption"><h3><?php echo esc_html($ch_item[0]); ?></h3><span>買取対象</span><?php if ($ch_result_url) : ?><a class="ch-text-link ch-light-link" href="<?php echo esc_url($ch_result_url); ?>">買取実績を見る <span aria-hidden="true">→</span></a><?php endif; ?></div></article>
        <?php endforeach; ?>
        </div>
        <p class="ch-chrome-image-note">写真は買取対象を紹介するイメージです。</p>
        <div class="ch-chrome-apply"><?php \MandaiLuxury\button('LINEでクロムハーツを査定する', $ch_line, 'ch-button-gold'); ?><a class="ch-text-link ch-light-link" href="#ch-results">買取実績を見る <span aria-hidden="true">→</span></a></div>
    </div></section>

    <section class="ch-section ch-container ch-results" id="ch-results" aria-labelledby="ch-results-title"><div class="ch-section-heading"><div><h2 id="ch-results-title" class="ch-unified-heading"><span class="ch-heading-text">買取実績</span></h2></div><span class="ch-subtitle">一番町店でお買取したお品物をご紹介</span></div><div class="ch-results-grid">
    <?php \MandaiLuxury\result_cards(\MandaiLuxury\prioritized_results($ch_settings['results'])); ?>
    </div>
    <?php foreach (array('ring' => 'リング', 'necklace' => 'ネックレス', 'bracelet' => 'ブレスレット', 'apparel' => 'アパレル', 'leather' => '財布・レザー') as $ch_key => $ch_label) : $ch_group = array_filter($ch_settings['results'], function($item) use ($ch_key) { return !empty($item['chrome_hearts']) && $item['category'] === $ch_key; }); if (!$ch_group) { continue; } ?>
        <details class="ch-result-group" id="ch-results-<?php echo esc_attr($ch_key); ?>"><summary><?php echo esc_html($ch_label); ?>のクロムハーツ買取実績</summary><div class="ch-results-grid"><?php \MandaiLuxury\result_cards($ch_group); ?></div></details>
    <?php endforeach; ?>
    </section>

    <section class="ch-buy-guide ch-guide-unified" id="ch-buy-guide" aria-labelledby="ch-guide-title">
    <div class="ch-guide-banner"><div class="ch-guide-banner-image" role="img" aria-label="シルバーリングとレザー小物のイメージ"></div><div class="ch-container ch-guide-heading"><span class="ch-guide-eyebrow">BUYBACK SERVICE</span><h2 id="ch-guide-title" class="ch-unified-heading"><span class="ch-heading-text">買取のご案内</span></h2><p>一点から、査定・相談は無料。お見積もりだけでもお気軽にご相談ください。</p><p class="ch-guide-assurance">シルバーからバッグ・時計まで、幅広いブランドをお取り扱い。お品物の状態や付属品を確認し、一点ずつ丁寧に査定内容をご案内します。</p></div></div>
    <?php include __DIR__ . '/lp_ichibancho_assets/inc/purchase-guide.php'; ?>

    </section>

    <section class="ch-section ch-container ch-sales" id="ch-pickup" data-sales aria-labelledby="ch-pickup-title"><div class="ch-section-heading"><div><h2 id="ch-pickup-title" class="ch-unified-heading"><span class="ch-heading-text">販売商品・最新入荷</span></h2><p class="ch-section-intro">次の一品との出会いも、万代で。公開中の販売在庫をご案内します。</p></div></div>
    <div class="ch-sales-gallery" aria-label="一番町店の売場写真">
        <figure><img src="<?php echo esc_url($ch_assets . 'images/ichibancho2.jpg'); ?>" alt="クロムハーツのシルバーやアパレルが並ぶ一番町店の売場" width="5712" height="4284" loading="lazy"><figcaption>一番町店の売場</figcaption></figure>
        <figure><img src="<?php echo esc_url($ch_assets . 'images/ichibancho1.jpg'); ?>" alt="ブランドバッグや時計を展示したショーケース" width="1620" height="1080" loading="lazy"><figcaption>バッグ・時計のショーケース</figcaption></figure>
        <figure><img src="<?php echo esc_url($ch_assets . 'images/ichibancho3.jpg'); ?>" alt="クロムハーツの財布やレザー小物の展示" width="1620" height="1080" loading="lazy"><figcaption>クロムハーツのレザーアイテム</figcaption></figure>
    </div>
    <?php \MandaiLuxury\sales_controls(); ?>
    <?php if ($ch_products) : ?><div class="ch-products">
    <?php foreach ($ch_products as $ch_product) : $ch_product_brand = \MandaiLuxury\product_brand($ch_product); ?>
        <?php $ch_facets = \MandaiLuxury\product_facets($ch_product, $ch_product_brand); $ch_created = method_exists($ch_product, 'get_date_created') ? $ch_product->get_date_created() : null; $ch_sort_price = $ch_product->get_price() === '' ? -1 : (function_exists('wc_get_price_to_display') ? wc_get_price_to_display($ch_product) : (float) $ch_product->get_price()); ?>
        <article class="ch-product" data-sales-tags="<?php echo esc_attr(wp_json_encode($ch_facets)); ?>" data-sales-price="<?php echo esc_attr($ch_sort_price); ?>" data-sales-date="<?php echo esc_attr($ch_created ? $ch_created->getTimestamp() : 0); ?>"><a class="ch-product-detail" href="<?php echo esc_url($ch_product->get_permalink()); ?>"><div class="ch-product-image"><?php if ($ch_product->get_image_id()) : echo wp_kses_post($ch_product->get_image('woocommerce_thumbnail', array('loading' => 'lazy'))); else : ?><span class="ch-photo-fallback">商品画像<small>写真は準備中です</small></span><?php endif; ?></div><div class="ch-product-info"><?php if ($ch_product_brand) : ?><p class="ch-product-brand"><?php echo esc_html($ch_product_brand); ?></p><?php endif; ?><h3><?php echo esc_html($ch_product->get_name()); ?></h3><div class="ch-price"><?php echo $ch_product->get_price() !== '' ? wp_kses_post($ch_product->get_price_html()) : '販売価格はお問い合わせください'; ?></div></div></a><?php \MandaiLuxury\favorite_button('product-' . $ch_product->get_id(), $ch_product->get_name()); ?></article>
    <?php endforeach; ?></div>
    <?php else : ?><div class="ch-stock-note"><div><h3>お探しの一品を、ご相談ください。</h3><p>商品情報が未掲載の場合も、入荷状況はLINEまたは店舗へお問い合わせください。</p></div><?php \MandaiLuxury\button('LINEで在庫を問い合わせる', $ch_line); ?></div><?php endif; ?>
    <p class="ch-sales-empty" role="status" hidden>この条件に合う表示商品はありません。</p><div class="ch-sales-footer"><?php \MandaiLuxury\button('すべての販売商品を見る', $ch_shop, 'ch-button-sales'); ?><p>表示中の商品を絞り込んでいます。最新の在庫・販売価格は店舗または商品詳細でご確認ください。</p></div>
    </section>

    <section class="ch-section ch-container ch-faq" id="ch-faq" aria-labelledby="ch-faq-title"><div class="ch-section-heading"><div><h2 id="ch-faq-title" class="ch-unified-heading"><span class="ch-heading-text">よくあるご質問</span></h2></div></div>
        <div class="ch-faq-list"><?php $ch_featured = array_slice(array_values(array_filter($ch_settings['faqs'], function($item) { return !empty($item['featured']); })), 0, 6); \MandaiLuxury\faqs($ch_featured); ?></div>
        <div class="ch-faq-actions"><?php \MandaiLuxury\button('よくあるご質問一覧を見る', $ch_faq_url); ?></div>
    </section>

    <section class="ch-store" id="ch-store" aria-labelledby="ch-store-title">
        <div class="ch-container ch-store-heading"><h2 id="ch-store-title" class="ch-unified-heading"><span class="ch-heading-text">店舗案内</span></h2><p>買取のご相談も、販売商品のお問い合わせも。お気軽にお立ち寄りください。</p></div>
        <div class="ch-container ch-store-hero">
            <div class="ch-store-information"><h3>万代 一番町店</h3><dl class="ch-store-facts"><div><dt>営業時間</dt><dd>10:00〜21:00</dd></div><div><dt>定休日</dt><dd>年中無休<span>（年末年始を除く）</span></dd></div><div><dt>所在地</dt><dd><address>〒980-0811<br>宮城県仙台市青葉区一番町4丁目5-16</address></dd></div></dl><div class="ch-store-call"><p>買取・販売のお問い合わせ</p><a class="ch-store-phone" href="tel:0227247219">022-724-7219</a></div><div class="ch-actions"><a class="ch-button ch-button-dark" href="#ch-map">地図・アクセス<span aria-hidden="true">↓</span></a></div><a class="ch-text-link ch-store-detail-link" href="<?php echo esc_url(home_url('/access/#ichibanmachi')); ?>">店舗の詳細を見る <span aria-hidden="true">→</span></a></div>
            <figure class="ch-store-hero-photo"><?php foreach ($ch_settings['photos'] as $ch_photo) : if ($ch_photo['id'] !== 'exterior' || !\MandaiLuxury\asset($ch_photo['image'])) { continue; } \MandaiLuxury\photo($ch_photo['image'], $ch_photo['label']); ?><figcaption><?php echo esc_html($ch_photo['caption']); ?></figcaption><?php endforeach; ?></figure>
        </div>
        <div class="ch-store-map" id="ch-map">
            <div class="ch-store-map-heading"><h3>アクセスマップ</h3><a class="ch-text-link" href="https://www.google.com/maps/search/?api=1&amp;query=%E5%AE%AE%E5%9F%8E%E7%9C%8C%E4%BB%99%E5%8F%B0%E5%B8%82%E9%9D%92%E8%91%89%E5%8C%BA%E4%B8%80%E7%95%AA%E7%94%BA4%E4%B8%81%E7%9B%AE5-16" target="_blank" rel="noopener noreferrer">Googleマップで開く <span aria-hidden="true">↗</span><span class="ch-map-sr">（新しいタブ）</span></a></div>
            <iframe title="万代 一番町店のアクセスマップ" src="https://maps.google.com/maps?q=%E5%AE%AE%E5%9F%8E%E7%9C%8C%E4%BB%99%E5%8F%B0%E5%B8%82%E9%9D%92%E8%91%89%E5%8C%BA%E4%B8%80%E7%95%AA%E7%94%BA4%E4%B8%81%E7%9B%AE5-16&amp;hl=ja&amp;z=17&amp;output=embed" width="1200" height="400" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
        </div>
        <div class="ch-container ch-store-block ch-store-social-block"><div class="ch-store-block-heading"><h3>公式SNS・LINE</h3><p>入荷情報や店舗のお知らせをチェック。ご相談はLINEでも。</p></div>
        <div class="ch-socials ch-social-links" aria-label="一番町店の公式SNS">
            <?php foreach ($ch_settings['socials'] as $ch_social) : $ch_social_url = \MandaiLuxury\safe_url($ch_social['id'] === 'line' ? $ch_line : $ch_social['url']); ?>
            <div class="ch-social-card"><div class="ch-social-content"><h4><?php echo esc_html($ch_social['name']); ?></h4><?php if ($ch_social_url) : ?><a href="<?php echo esc_url($ch_social_url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($ch_social['label']); ?><span class="ch-map-sr">（新しいタブ）</span></a><?php else : ?><span class="ch-social-disabled">準備中</span><?php endif; ?></div></div>
            <?php endforeach; ?>
        </div>
        <details class="ch-store-supplement"><summary>QRコードでフォローする</summary><div class="ch-social-qr-grid"><?php foreach ($ch_settings['socials'] as $ch_social) : ?><figure><img src="<?php echo esc_url($ch_assets . 'images/' . $ch_social['image']); ?>" alt="<?php echo esc_attr($ch_social['name'] . 'のQRコード'); ?>" width="200" height="200" loading="lazy"><figcaption><?php echo esc_html($ch_social['name']); ?></figcaption></figure><?php endforeach; ?></div></details>
        </div>
        <?php foreach (array('inside' => '店内の様子') as $ch_group => $ch_group_title) : $ch_group_photos = array_filter($ch_settings['photos'], function($photo) use ($ch_group) { $inside = in_array($photo['id'], array('interior', 'showcase', 'counter'), true); return $photo['id'] !== 'exterior' && ($ch_group === 'inside') === $inside && \MandaiLuxury\asset($photo['image']); }); if (!$ch_group_photos) { continue; } ?>
        <details class="ch-container ch-store-block ch-store-supplement"><summary><?php echo esc_html($ch_group_title); ?>の写真を見る</summary><div class="ch-store-gallery" aria-label="<?php echo esc_attr($ch_group_title); ?>"><?php foreach ($ch_group_photos as $ch_photo) : ?><figure class="ch-store-photo"><?php \MandaiLuxury\photo($ch_photo['image'], $ch_photo['label']); ?><figcaption><?php echo esc_html($ch_photo['label']); ?><small><?php echo esc_html($ch_photo['caption']); ?></small></figcaption></figure><?php endforeach; ?></div></details>
        <?php endforeach; ?>
    </section>


    <?php $ch_news = new WP_Query(array('post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 3, 'ignore_sticky_posts' => true, 'no_found_rows' => true)); ?>
    <?php if ($ch_news->have_posts()) : ?><section class="ch-news ch-container" aria-labelledby="ch-news-title"><div><h2 id="ch-news-title" class="ch-unified-heading"><span class="ch-heading-text">お知らせ</span></h2></div><div class="ch-news-list"><?php while ($ch_news->have_posts()) : $ch_news->the_post(); ?><a href="<?php the_permalink(); ?>"><time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('Y.m.d')); ?></time><span><?php echo esc_html(get_the_title()); ?></span><b aria-hidden="true">→</b></a><?php endwhile; ?></div></section><?php endif; wp_reset_postdata(); ?>


    <section class="ch-contact" aria-labelledby="ch-contact-title"><div class="ch-container"><h2 id="ch-contact-title" class="ch-unified-heading"><span class="ch-heading-text">まずはお気軽にご相談ください</span></h2><p>売りたい方はLINE査定・買取申込へ。<br class="ch-mobile-break">お探しの方は販売商品をご覧ください。</p><div class="ch-actions"><?php \MandaiLuxury\button('LINE査定', $ch_line, 'ch-button-gold'); \MandaiLuxury\button('買取申込', $ch_purchase ?: '#ch-request', 'ch-button-outline', true); \MandaiLuxury\button('販売商品を見る', '#ch-pickup', 'ch-button-outline', true); ?></div><a class="ch-contact-phone" href="tel:0227247219">電話で相談：022-724-7219</a></div></section>
    <nav class="ch-fixed-cta" aria-label="買取・販売のご案内"><?php \MandaiLuxury\button('LINE査定', $ch_line, 'ch-button-gold'); \MandaiLuxury\button('買取申込', $ch_purchase ?: '#ch-request', 'ch-button-dark', true); \MandaiLuxury\button('販売商品を見る', '#ch-pickup', 'ch-button-dark', true); ?></nav>
</main>
<?php get_footer(); ?>
