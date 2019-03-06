<?php
require_once '../vendor/autoload.php';
require_once '../lib/GoogleApi.php';
require_once '../lib/Convert_Event.php';

define('MAX_QUERIES', 3);

// グローバルに使う変数
$calendars = array();
$numbers = array();
$exceedQueries = false;


$googleApi = new GoogleApi;
$allUsers = DomainUsers::LoadDomainUsers();
$queryUsers = [];
$query = isset($_GET['q']) ? $_GET['q'] : null;

// ユーザー検索
if (isset($query) && $query !== '') {
    // 検索クエリを区切り単語で分割
    $qs = explode(',', $query);

    // 複数検索の場合はあいまい検索が1件のみ 
    $__count = 0;
    if (count($qs) === 1) {
        $queryUsers = DomainUsers::searchUsers($qs[0], MAX_QUERIES);
    } else {
        foreach ($qs as $q) {
            if ($__count >= MAX_QUERIES) {
                $exceedQueries = true;
                break;
            }
            $queryUsers = array_merge($queryUsers, DomainUsers::searchUsers($q, 1));
            $__count++;
        }
    }

    foreach ($queryUsers as $qu) {
        if (isset($qu['ignoreEmailSearch']) && $qu['ignoreEmailSearch'] == true) {
            $__cal = new Calendar();
            $__cal->setName($qu['name']);
            $calendars[] = $__cal;
        } else {
            $calendars[] = $googleApi->getCalendar($qu['email']);
        }
        $numbers[] = isset($qu['phone']) ? $qu['phone'] : [];
    }
}

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>いまどこ検索</title>
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans+JP" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-flash.min.css" rel="stylesheet">
    <link href="/style/materialize.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Noto Sans JP', -apple-system, BlinkMacSystemFont, "Helvetica Neue", YuGothic, "ヒラギノ角ゴ ProN W3", Hiragino Kaku Gothic ProN, Arial, "メイリオ", Meiryo, sans-serif;
    }
    ::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }
    ::-webkit-scrollbar-track {
        border-radius: 10px;
        box-shadow: inset 0 0 6px rgba(0, 0, 0, .1);
    }
    ::-webkit-scrollbar-thumb:hover {
        background-color: rgba(0, 0, 0, .3);
    }
    ::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, .2);
        border-radius: 10px;
        box-shadow:0 0 0 1px rgba(255, 255, 255, .3);
    }
    
    hr {
        border: 0;
        border-bottom: 1px solid #DFE1E5;
        margin: 0;
    }
    .title-header {
        display: block;
        margin: 1rem auto;
        height: 40px;
        pointer-events: none;
    }
    .last-updated {
        text-align: right;
        margin-bottom: 0;
    }
    .card-panel {
        padding: 8px;
        text-align: center;
    }
    .round-card {
        border-radius: 10px;
    }
    .search-block {
        margin-bottom: 0;
    }
    .search-card {
        transition: box-shadow 0.2s ease-in-out;
    }
    
    .search-field-wrapper {
        margin-bottom: 0;
    }
    .search-field-form {
        padding: 0 !important;
    }
    .search-field {
        display: flex;
        padding: 0 1.5rem;
        align-items: center;
        transition: box-shadow 0.2s ease-in-out;
    }
    .search-field-input-query {
        flex: 1;
    }
    .search-field-submit-button {
        margin: 0 0 0 1rem;
    }
    .username-section {
        padding: .5rem 0 0;
        font-size: 1.5rem;
        font-weight: bold;
    }
    .card-section+ .card-section {
        margin-top: 1.5rem;
    }
    .descript-section {
        font-size: 1.25rem;
        font-weight: bold;
    }
    .section-divide {
        margin: .25rem 0 1rem;
    }
    .phone-numbers {
        display: flex;
        align-items: center;
        font-weight: bold;
        overflow-x: auto;
    }
    .phone-numbers span {
        padding: 0 1em 0 .5em;
    }
    .event-content {
        margin-bottom: .75rem;
    }
    .time-frame {
        display: inline-block;
        margin-left: .5em;
        padding: 0 1em;
        color: #FFFFFF;
        border: none;
        border-radius: 4px;
    }
    .event-summary-wrapper {
        display: flex;
        align-items: baseline;
        padding-left: 1em;
    }
    .no-events {
        display: block;
        text-align: center;
        font-weight: normal !important;
    }
    .tag-red {
        background: #e57373;
    }
    .tag-green {
        background: #81c784 ;
    }
    .rakumo-icons {
        vertical-align: bottom;
        cursor: pointer;
    }
    .divide {
        margin: 1rem 0;
    }
    .ui-widget.ui-widget-content {
        border: none !important;
        -webkit-box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14), 0 3px 1px -2px rgba(0,0,0,0.12), 0 1px 5px 0 rgba(0,0,0,0.2);
        box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14), 0 3px 1px -2px rgba(0,0,0,0.12), 0 1px 5px 0 rgba(0,0,0,0.2);
        max-height: 300px;
        overflow-y: auto;
        overflow-x: hidden;
        border-radius: 8px;
    }
    .ui-menu .ui-menu-item-wrapper {
        padding: 1em;
    }
    .ui-state-active {
        margin: 0 !important;
        border: none !important;
        background: #eee !important;
        color: #000 !important;
    }
    </style>
</head>

<body>
    <div class="container">
        <a href="/"><object class="title-header" type="image/svg+xml" data="/logo/logo.svg"></object></a>

        <div class="row search-field-wrapper">
            <form id="search-field-form" class="col s12 offset-m2 m8 search-field-form" action="" method="GET">
                <div id="search-field" class="round-card search-field z-depth-1">
                    <div class="input-field search-field-input-query">
                        <i class="material-icons prefix">search</i>
                        <input type="text" name="q" id="searchword-input" class="auto-complete" value="<?= h($query) ?>">
                        <label for="searchword-input">名前 or メールアドレス</label>
                    </div>
                    <div class="input-field search-field-submit-button">
                        <button id="search-field-form-btn" class="btn waves-effect waves-light" type="submit">検索</button>
                    </div>
                </div>
            </form>
        </div>

        <?php if (isset($query)): ?>
            <div class="row last-updated"><p class="col s12 offset-m2 m8 last-updated"><?= date("Y/m/d H:i:s", time()) ?>更新</p></div>
        <?php endif;?>

        <?php if (isset($query) && count($calendars) <= 0): ?>
            <div class="card-panel red lighten-1">
                <p class="white-text">対象ユーザーが見つかりませんでした</p>
            </div>
        <?php endif; ?>

        <?php if ($exceedQueries === true): ?>
            <div class="card-panel red lighten-1">
                <p class="white-text">一部の検索は省略されました<br />同時に検索できるのは<?= MAX_QUERIES ?>人までです</p>
            </div>
        <?php endif; ?>        
        
        <?php
        $i = 0;
        foreach($calendars as $cal):
        ?>
            <div class="username-section">
                <?= $cal->getName() ?>
                <?php if ($cal->getId()) : ?>
                    <a target=”_blank” href="<?= 'https://a-rakumo.appspot.com/calendar#calendar/'.$cal->getId() ?>"><i class="material-icons rakumo-icons">event_note</i></a>
                <?php endif; ?>
            </div>
            <div class="card round-card">
                <div class="card-content">
                    <?php if ($numbers[$i] != null) : ?>
                        <div class="card-section">
                            <div class="phone-numbers">
                                <?php foreach ($numbers[$i] as $n) : ?>
                                <i class="small material-icons">contact_phone</i><span><?= $n ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif;?>

                    <div class="card-section">
                        <div class="descript-section">現在の予定</div>
                        <hr class="section-divide" />
                        <?php
                        $currentEvents = $cal->currentEvents();
                        if (count($currentEvents) > 0) :
                                foreach ($currentEvents as $e) :?>
                                <div class="event-content">
                                    <?php if ($e->getAllDay() === true) : ?>
                                        <div class="time-frame tag-red">終日</div>
                                    <?php else: ?>
                                        <div class="time-frame tag-red">～<?= date('H:i', $e->getEndTimeAsTime()) ?></div>
                                    <?php endif; ?>
                                        <div class="event-summary-wrapper">
                                            <a target=”_blank” href="<?= 'https://a-rakumo.appspot.com/calendar#event/google:'.$cal->getId().'/'.$e->getId() ?>"><i class="material-icons rakumo-icons">event_note</i></a>
                                            <span class="event-summary"><?= $e->getSummary() ?></span>
                                        </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="event-summary no-events">現在の予定はありません</span>
                        <?php endif; ?>
                    </div>

                    <div class="card-section">
                        <div class="descript-section">今後の予定</div>
                        <hr class="section-divide" />
                        <?php
                            $followEvents = $cal->followEvents();
                            if (count($followEvents) > 0) :
                                foreach ($followEvents as $e) :?>
                                <div class="event-content">
                                    <?php if ($e->getAllDay() === true) : ?>
                                        <div class="time-frame tag-green">終日</div>
                                    <?php else: ?>
                                        <div class="time-frame tag-green"><?= date('H:i', $e->getStartTimeAsTime()) ?>～<?= date('H:i', $e->getEndTimeAsTime()) ?></div>
                                    <?php endif; ?>
                                        <div class="event-summary-wrapper">
                                            <a target=”_blank” href="<?= 'https://a-rakumo.appspot.com/calendar#event/google:'.$cal->getId().'/'.$e->getId() ?>"><i class="material-icons rakumo-icons">event_note</i></a>
                                            <span class="event-summary"><?= $e->getSummary() ?></span>
                                        </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="event-summary no-events">今後の予定はありません</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php
        $i++;
        endforeach;
        ?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="/script/toHebon.js"></script>
    <script type="text/javascript">
        var config = {};config.users = <?= json_encode($allUsers) ?>;
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('input.auto-complete').autocomplete({
                source : function(request, response) {
                    var re   = new RegExp(request.term, 'i'),
                        list = [];
                            
                    $.each(config.users, function(i, _user) {
                        var _name = '', _email = '';
                        if (_user.ignoreAllSearch == null || _user.ignoreAllSearch == false) {
                            if (_user.ignoreEmailSearch == null || _user.ignoreEmailSearch == false) {
                                var furiganaReg = new RegExp(toHebon(request.term), 'i');
                                if (_user.email.match(re) || _user.email.match(furiganaReg)) {
                                    list.push(_user.name);
                                    return true;
                                }
                            }
                            if (config.users[i].ignoreNameSearch == null || config.users[i].ignoreNameSearch == false) {
                                if (_user.name.match(re)) {
                                    list.push(_user.name);
                                    return true;
                                }
                            }
                        }
                    });

                    response(list);
                },
                delay: 0
            }).on( "autocompleteselect", function(event, ui){
                setTimeout(function() {
                    $('#search-field-form').submit();
                }, 0);
            });

            $('#search-field-form').submit(function(e) {
                $('#search-field-form-btn').prop('disabled', true);
                return true;
            });

            $('#searchword-input').focusin(function(e) {
                $('#search-field').removeClass('z-depth-1');
                $('#search-field').addClass('z-depth-3');
            });
            $('#searchword-input').focusout(function() {
                $('#search-field').removeClass('z-depth-3');
                $('#search-field').addClass('z-depth-1');
            });

            $(window).keydown(function(e){
                if(event.ctrlKey){
                    if(e.keyCode === 70 || e.keyCode === 76){
                        $('#searchword-input').focus().select();
                        return false;
                    }
                }
            });
        });
    </script>
</body>

</html>