万代 一番町店 買取・販売ページ

構成
page_ichibancho.php          WordPressの一番町店テンプレート
page_brand_faq.php           FAQ一覧テンプレート
lp_ichibancho_assets/        本番用CSS・JavaScript・設定データ・PHP部品・画像
preview.html                メインページのローカルプレビュー
preview-faq.html            FAQ一覧のローカルプレビュー
scripts/build-preview.cjs   プレビュー生成スクリプト
tests/                      CLI検証、ブラウザ検証、プレビュー用サンプル商品
tests/fixtures/             内容維持の検証に使う比較用テンプレート

WordPressへの設置
2つのpage_*.phpとlp_ichibancho_assets/を使用中のテーマ内へ同じ構造で配置します。
ヘッダー・フッターは設置先のテーマを使用します。
preview*.html、scripts/、tests/、README.txtは本番設置不要です。
プレビューには明示したサンプル商品が含まれます。本番ではWooCommerceの実商品を表示します。

設定
lp_ichibancho_assets/data/content.jsonでURL・FAQ・商品カテゴリーなどを管理します。
店頭・LINE・宅配買取の案内はinc/purchase-guide.phpにあります。
買取申込・宅配買取などの未設定URLは、実際の受付先に合わせて設定してください。
FAQ一覧はpage_brand_faq.phpを使用する固定ページをWordPressで作成してください。

プレビュー再生成・検証（Node.js、PHP CLIが必要）
node scripts/build-preview.cjs
php -n tests/contracts.php
PHPがPATHにない場合は、環境変数MANDAI_PHPにPHP実行ファイルのパスを設定します。
Windowsでは.local-tools/php/php.exeが存在する場合、自動的に利用します。
ブラウザ検証: node tests/browser-qa.cjs（WindowsのGoogle Chromeが必要）
ブラウザ検証で生成するスクリーンショットと結果JSONはGit対象外です。

画像
現在のFV、店舗外観、売場写真はimages/内のichibancho*.jpg/pngです。
images/chrome-hearts/の画像は装飾・取扱イメージとして使用しています。
画像はテンプレート、CSS、設定データ、プレビューの参照を維持しています。
