*このプラグインは開発中です。*  
*現在サポートは受け付けておりません。*

*開発者がrain318_1995からmcpekatsuoへ変更されました。*

## 目次
* HoneyChestとは
* ライセンス
* Config.yml
* コマンド
* パーミッションノート
* サポート/お問い合わせ
* バージョンについて
* 既知の不具合

## HoneyChestとは
HoneyChestは、荒らし対策Pluginの一種です。  
プレイヤーがハニーチェストに指定されたチェストを開くと、自動的に動作します。  
kick、BANはもちろん、MCPEbansから提供されるGBANや、各種コマンドにも対応しています。  
尚、マルチワールドには対応していません。

※MCPEbansから提供されるGlobalBANは準備中です

## ライセンス
* CC BY-NC 4.0に準拠します。
* 改変、再配布は許可されますが、再配布する場合は著作権表記をする必要があります。改変した場合は著作権表記とともに改変した旨を表示する必要があります。
* 商用利用は許可されません。

## Config.yml
Plugin導入後、一度サーバーを起動させる必要があります。  
サーバーを起動させるとconfig.ymlが生成されるので、編集をしてください。

|config|説明|
|-----|-----|
|Config version|変更しないでください。|
|BroadCaster|HoneyChestが動作した際に、表示するメッセージを記載してください。<br>何も表示させたくない場合は「none」と記載してください。|
|Action|動作する際に、何を行うか記載してください。<br>kick(プレイヤーをサーバーから強制切断します)<br>ban(プレイヤーをサーバーから永久通報します)<br>cmd(任意のコマンドを実行します。)|
|Command|Action: cmd にした際に使用します。<br>動作した際に行ってほしい、コマンドを入力してください。<br>例: Command: say HoneyChest Test Msg|
|Do you agree with the license?|日本語訳すると「ライセンスに同意しますか？」です。<br>同意する場合は、trueと入力してください。|

## コマンド
|コマンド|説明|
|-----|-----|
|/hc info|Pluginの情報を表示します。|
|/hc help|Pluginのコマンド一覧を表示します。|
|/hc set|HoneyChestの登録に追加するチェストを選択します。|
|/hc remove|HoneyChestの登録を解除するチェストを選択します。|
|/hc reload|HoneyChest PluginのConfig.ymlを再読み込みします。|

「/hc set」「/hc remove」を入力した後に、該当チェストをタップする必要があります。

## パーミッションノート
|コマンド|説明|
|-----|-----|
|honeychest.*|HoneyChest Pluginで提供される全機能が使用可能になります。|
|honeychest.info|/hc info が使用可能になります。|
|honeychest.help|/hc help が使用可能になります。|
|honeychest.set|/hc set が使用可能になります。|
|honeychest.remove|/hc remove が使用可能になります。|
|honeychest.reload|/hc reload が使用可能になります。|
|honeychest.exception|この権限がある場合、チェスト内を操作しても動作しません。|

info, helpはデフォルトで誰でも利用可能になっております。  
その他の権限は、デフォルトでOP権限者に付与されています。

## サポート/お問い合わせ
古いバージョンのサポートは行っておりません。最新版のみサポートを行います。  
※「最新版」に、Build Versionは含みません。バージョンは、「x.y.z」の事を指します。  
連絡は、[作者Twitter](http://twitter.com/Katsuoserver)にて受け付けております。  
質問/要望/提案 がある場合、まずは[作者Twitter](http://twitter.com/Katsuoserver)に連絡を頂けると幸いです。

## バージョンについて
バージョンは、基本的に「X.Y.Z」で構成されております。

|値|説明|
|:-----:|-----|
|X|インターフェースの変更によって下位互換性が失われる大きな変更|
|Y|新しい機能の追加(ただし既存インターフェースの下位互換は保証)|
|Z|インターフェースに影響を与えない内部的な変更(バグ修正等々)|

また、開発途中のプログラムの場合は、「X.Y.Z [Build #]」となり、  
プラグイン開発が完了した際に、Build番号は取り除かれます。  
例: 「0.0.0 [Build 3]」  >>>  「0.0.0」

