<?php
$ch_method_guides = array(
    'store' => array('店頭買取', '予約不要、その場で査定をいたします。', '身分証明書があれば即現金買取をいたしますので、お手軽な買い取り方法となります。専門バイヤーが親切丁寧に対応いたしますのでご安心ください！', array(
        array('ご来店', '「お品物」と「身分証明書」を持ってお越しください。中古品はもちろん、未使用品も大歓迎です。'),
        array('無料査定', '専門バイヤーがお客様の目の前で、お品物を一つ一つ丁寧に無料査定し、金額をご提示します。ご納得いただけない場合はキャンセルできます。査定・キャンセル料は一切かかりません。'),
        array('お支払い', '金額にご納得いただけましたら、必要書類へのご記入と身分証明書のコピーをいただき、その場で全額現金にてお支払いします。非常に高額の場合はお振込みとなることがあります。')
    )),
    'line' => array('LINE査定', '写真を送って、まずは気軽にご相談。', 'ご来店前に、お品物の写真をLINEでお送りください。実際の買取金額は、お品物を拝見したうえでご案内します。', array(
        array('友だち追加', '公式LINEを友だち追加し、査定したいお品物についてメッセージをお送りください。'),
        array('写真・情報を送信', '全体・刻印・気になる傷などがわかる写真と、ブランド名、品名、付属品の有無をお知らせください。'),
        array('査定目安をご案内', 'スタッフからのご案内をご確認ください。買取をご希望の場合は店頭へのお持ち込み、または宅配買取の利用方法をご相談ください。')
    )),
    'delivery' => array('宅配買取', 'ご来店が難しい方は、宅配買取をご相談ください。', 'まずは受付方法・送付先・必要書類をご確認ください。発送方法や費用、お支払いの条件は受付時にご案内します。', array(
        array('事前にご相談', 'LINEでお品物の内容と付属品をお知らせください。受付の可否、送付先、本人確認の方法や必要書類を確認します。'),
        array('梱包・発送', '受付時の案内に沿って必要書類をご用意し、お品物と付属品を破損しないよう梱包して発送してください。'),
        array('査定・お支払い', 'お品物の到着後に査定し、金額をご案内します。ご承諾後、受付時にご案内した方法でお支払いします。キャンセル・返送条件も事前にご確認ください。')
    ))
);
?>
<div class="ch-method-area" id="ch-request"><div class="ch-container" data-purchase-methods>
    <fieldset class="ch-method-choices"><legend>買取方法をお選びください</legend><div class="ch-method-options">
    <?php foreach ($ch_method_guides as $ch_key => $ch_guide) : ?><label><input type="radio" name="chrome-purchase-method" value="<?php echo esc_attr($ch_key); ?>"<?php if ($ch_key === 'store') { echo ' checked'; } ?> aria-controls="ch-method-<?php echo esc_attr($ch_key); ?>"><span><?php echo esc_html($ch_guide[0]); ?></span></label><?php endforeach; ?>
    </div></fieldset>
    <div class="ch-method-panels" id="ch-flow">
    <?php foreach ($ch_method_guides as $ch_key => $ch_guide) : ?>
        <section class="ch-method-panel ch-method-guide" id="ch-method-<?php echo esc_attr($ch_key); ?>" aria-labelledby="ch-method-title-<?php echo esc_attr($ch_key); ?>">
            <div class="ch-method-intro"><h3 id="ch-method-title-<?php echo esc_attr($ch_key); ?>"><?php echo esc_html($ch_guide[1]); ?></h3><p><?php echo esc_html($ch_guide[2]); ?></p></div>
            <h4><?php echo esc_html($ch_guide[0]); ?>の流れ</h4>
            <ol class="ch-method-flow"><?php foreach ($ch_guide[3] as $ch_index => $ch_step) : ?><li><span class="ch-method-step-number"><?php echo sprintf('%02d', $ch_index + 1); ?></span><h5><?php echo esc_html($ch_step[0]); ?></h5><p><?php echo esc_html($ch_step[1]); ?></p></li><?php endforeach; ?></ol>
            <?php if ($ch_key === 'store') : ?>
            <details class="ch-identity-details"><summary>ご利用いただける身分証明書・お取引の条件</summary>
                <ul class="ch-identity-list"><?php foreach (array('運転免許証', '年金手帳', '生活保護受給証', '学生証（住所記入欄のあるもの）', '身体障がい者手帳', '健康保険資格確認書', '療育手帳', '在留カード', '精神障がい者保健福祉手帳', '特別永住者証明書', 'マイナンバーカード', '小型船舶操縦士免許証') as $ch_identity) : ?><li><?php echo esc_html($ch_identity); ?></li><?php endforeach; ?></ul>
                <ul class="ch-identity-notes"><li>20歳未満及び高校生の方はお取引できません。</li><li>「身分証明書」は行政機関が発行したものになります。</li><li>現住所・生年月日が記載された有効期限内の原本に限ります。買取成立の場合、古物営業法によりご本人さま確認書類の記録（コピー）をとらせていただきますのであらかじめご了承ください。</li><li>法人のお客様の場合、ご来店される方の本人確認書類、会社の登記事項証明書又は印鑑登録証明書が必要となります。代表者以外の方がご来店される場合は、会社からの委任状が必要となります。</li><li>現金で200万円を超える貴金属・宝石等の売買については本人特定事項等の取引時確認を行います。</li><li>買取が不成立の場合は、商品をお持ち帰りいただきます。</li></ul>
            </details>
            <div class="ch-actions"><?php \MandaiLuxury\button('店舗・アクセスを見る', '#ch-store', 'ch-button-gold', true); ?></div>
            <?php elseif ($ch_key === 'line') : ?><div class="ch-actions"><?php \MandaiLuxury\button('LINEで査定する', $ch_line, 'ch-button-gold'); ?></div>
            <?php else : ?><div class="ch-actions"><?php $ch_delivery_url = \MandaiLuxury\safe_url($ch_links['delivery']); \MandaiLuxury\button($ch_delivery_url ? '宅配買取の申込・詳細' : 'LINEで宅配買取を相談する', $ch_delivery_url ?: $ch_line, 'ch-button-gold'); ?></div><?php endif; ?>
        </section>
    <?php endforeach; ?>
    </div>
    <?php if ($ch_purchase) : ?><div class="ch-method-contact"><?php \MandaiLuxury\button('買取申込へ進む', $ch_purchase, 'ch-button-gold'); ?></div><?php endif; ?>
</div></div>
