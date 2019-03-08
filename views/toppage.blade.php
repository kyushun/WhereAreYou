@extends('layouts.search-form')
@section('pagename', 'トップページ')
@section('stylesheet1')
    <link href="/style/about.css" rel="stylesheet">
@endsection

@section('main')
    <div class="card round-card suggest-section">
        <div class="toppage-section-header suggest-01-header"><div class="toppage-section-number">1</div></div>
        <div class="suggest-summary suggest-01-summary">内線番号と予定を<span class="suggest-01-mphasized">同時</span>に確認</div>
        <div class="suggest-content">
            <img src="./img/suggest/1-1.png" />
            <p>
                ○○さんの内線番号って？<wbr />今どこにいるんだろう…？<br /><br />
                いまどこ検索なら<wbr /><span class="suggest-01-mphasized">すべてを一度</span>で。<wbr /><br />
                Rakumoを開くよりも<wbr /><span class="suggest-01-mphasized">高速</span>に。
            </p>
        </div>
    </div>
    
    <div class="card round-card suggest-section">
        <div class="toppage-section-header suggest-02-header"><div class="toppage-section-number">2</div></div>
        <div class="suggest-summary suggest-02-summary">名前の<span class="suggest-02-mphasized">あいまい</span>検索</div>
        <div class="suggest-content">
            <img src="./img/suggest/2-1.png" />
            <p>
                あの人の漢字<wbr />どれだろう・・・<br />
                斎？斉？齋？齊？<br /><br />
                ひらがな・<wbr />メールアドレス・<wbr />漢字表記、<wbr /><span class="suggest-02-mphasized">すべて対応</span>。<br />
                電話のような<wbr /><span class="suggest-02-mphasized">急な対応</span>でも<wbr />焦る必要は<wbr />ありません。
            </p>
        </div>
    </div>
    
    <div class="card round-card suggest-section">
        <div class="toppage-section-header suggest-03-header"><div class="toppage-section-number">3</div></div>
        <div class="suggest-summary suggest-03-summary">予定の<span class="suggest-03-mphasized">詳細</span>を確認</div>
        <div class="suggest-content">
            <img src="./img/suggest/3-1.png" />
            <p>
                <i class="material-icons rakumo-icons light-blue-text">event_note</i>←このアイコンを<wbr />クリックすると<wbr />Rakumoで直接その予定が<wbr />見れちゃうんです！<br /><br />
                参加者や居場所の<wbr />確認に<wbr /><span class="suggest-03-mphasized">手間取りません</span>。<br />
                予定の<wbr /><span class="suggest-03-mphasized">変更も可能</span>。
            </p>
        </div>
    </div>

    <div class="card round-card suggest-section">
        <div class="toppage-section-header suggest-04-header"><div class="toppage-section-number">4</div></div>
        <div class="suggest-summary suggest-04-summary">便利な<span class="suggest-04-mphasized">ショートカット</span></div>
        <div class="suggest-content">
            <p>
                <span class="suggest-04-mphasized">[Ctrl + F]</span>で<wbr />検索フォームに<wbr />自動でフォーカス。<br />
                <span class="suggest-04-mphasized">[Ctrl + Y]</span>で<wbr />Rakumoを<wbr />開きます。
            </p>
        </div>
    </div>
    
    <div class="card round-card suggest-section">
        <div class="toppage-section-header suggest-05-header"><div class="toppage-section-number">5</div></div>
        <div class="suggest-summary suggest-05-summary">その他<span class="suggest-05-mphasized">問い合わせ</span></div>
        <div class="suggest-content">
            <p>
                使用上の要望や<wbr />バグ等を<wbr />発見した方は<span class="suggest-05-mphasized">@kyushun</span>まで<wbr />お問い合わせ<wbr />ください。
            </p>
        </div>
    </div>
@endsection
