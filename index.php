<?php
require_once 'vendor/autoload.php';
require_once 'GoogleApi.php';
require_once 'Convert_Event.php';

define('MAX_QUERIES', 5);

// グローバルに使う変数
$calendars = array();
$exceedLimitQueries = false;


$googleApi = new GoogleApi;
$users = DomainUsers::LoadDomainUsers();
$search = $_GET['q'];

// ユーザー検索
if (isset($search) && $search !== '') {
    $qs = explode(',', $search);
    if (count($qs) > MAX_QUERIES) $exceedLimitQueries = true;
    array_splice($qs, MAX_QUERIES, count($qs) - MAX_QUERIES);

    foreach ($users as $user) {
        foreach ($qs as $q) {
            if ($q === '' || CountBytes($q) < 3)  continue;
            if (strpos($user['name'], $q) !== false || strpos($user['email'], $q) !== false) {
                $calendars[] = $googleApi->getCalendar($user['email']);
            }
        }
    }
}

function CountBytes($str) {
    $count = 0;
    foreach (preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY) as $char) {
        if ($char === ',') continue;

        if (strlen($char) === 1) {
            $count += 1;
        } else {
            $count += 2;
        }
    }

    return $count;
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sample</title>
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans+JP" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-flash.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Noto Sans JP', -apple-system, BlinkMacSystemFont, "Helvetica Neue", YuGothic, "ヒラギノ角ゴ ProN W3", Hiragino Kaku Gothic ProN, Arial, "メイリオ", Meiryo, sans-serif;
    }
    ::-webkit-scrollbar {
        display: none;
    }
    hr {
        border: 0;
        border-bottom: 1px solid #DFE1E5;
        margin: 0;
    }
    .title-header {
        margin: 1rem 0;
        font-size: 2rem;
        font-weight: bold;
        text-align: center;
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
    .section-description {
        padding: .5rem 0 0;
        font-size: 1.5rem;
        font-weight: bold;
    }
    .event-content {
        margin-bottom: .75rem;
    }
    .time-frame {
        display: inline-block;
        margin-right: .25em;
        padding: .1em .75em;
        color: #FAFAFA;
        border-radius: 5px;
        font-weight: bold;
    }
    .event-summary {
        padding-left: .5em;
        font-weight: bold;
    }
    .no-events {
        display: block;
        text-align: center;
        font-weight: normal !important;
    }
    .tag-red {
        background: #ef5350;
    }
    .tag-green {
        background: #4CAF50;
    }
    .rakumo-icons {
        vertical-align: bottom;
        cursor: pointer;
    }
    .divide {
        margin: 1rem 0;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="title-header black-text"><a href="/" class="black-text">予定検索</a></div>

        <div class="row search-field-wrapper">
            <form class="col s12 offset-m2 m8 search-field-form" action="" method="GET">
                <div id="search-field" class="round-card search-field z-depth-1">
                    <div class="input-field search-field-input-query">
                        <i class="material-icons prefix">search</i>
                        <input type="text" name="q" id="searchword-input" class="autocomplete" value="<?= $search ?>">
                        <label for="searchword-input">名前 or メールアドレス</label>
                    </div>
                    <div class="input-field search-field-submit-button">
                        <button class="btn waves-effect waves-light" type="submit">検索</button>
                    </div>
                </div>
            </form>
        </div>

        <?php if (isset($search)): ?>
            <div class="row last-updated"><p class="col s12 offset-m2 m8 last-updated"><?= date("Y/m/d H:i:s", time()) ?>更新</p></div>
        <?php endif;?>

        <?php if ( isset($search) && count($calendars) <= 0 && CountBytes($search) >= 3): ?>
            <div class="card-panel red lighten-1">
                <p class="white-text">対象ユーザーが見つかりませんでした</p>
            </div>
        <?php endif; ?>

        <?php if ( isset($search) && CountBytes($search) < 3): ?>
            <div class="card-panel red lighten-1">
                <p class="white-text">検索ワードが短すぎます</p>
            </div>
        <?php endif; ?>

        <?php if ($exceedLimitQueries === true): ?>
            <div class="card-panel red lighten-1">
                <p class="white-text">一部の検索は省略されました。<br />同時に検索できるのは5人までです</p>
            </div>
        <?php endif; ?>        
        
        <?php foreach($calendars as $cal): ?>
            <div class="section-description"><?= $cal->getName() ?></div>
            <div class="card round-card">
                <div class="card-content">
                    <?php
                    $currentEvents = $cal->currentEvents();
                    if (count($currentEvents) > 0) :
                            foreach ($currentEvents as $e) :?>
                            <div class="event-content">
                                <?php if ($e->getAllDay() === true) : ?>
                                    <div class="time-frame z-depth-1 tag-red">終日</div>
                                <?php else: ?>
                                    <div class="time-frame z-depth-1 tag-red">～<?= date('H:i', $e->getEndTimeAsTime()) ?></div>
                                <?php endif; ?>
                                    <div class="event-summary">
                                        <span class="event-summary"><?= $e->getSummary() ?></span>
                                        <a target=”_blank” href="<?= 'https://a-rakumo.appspot.com/calendar#event/google:'.$cal->getId().'/'.$e->getId() ?>"><i class="material-icons rakumo-icons">event_note</i></a>
                                    </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="event-summary no-events">現在の予定はありません</span>
                    <?php endif; ?>

                    <hr class="divide" />

                    <?php
                        $followEvents = $cal->followEvents();
                        if (count($followEvents) > 0) :
                            foreach ($followEvents as $e) :?>
                            <div class="event-content">
                                <?php if ($e->getAllDay() === true) : ?>
                                    <div class="time-frame z-depth-1 tag-green">終日</div>
                                <?php else: ?>
                                    <div class="time-frame z-depth-1 tag-green"><?= date('H:i', $e->getStartTimeAsTime()) ?>～<?= date('H:i', $e->getEndTimeAsTime()) ?></div>
                                <?php endif; ?>
                                    <div class="event-summary">
                                        <span class="event-summary"><?= $e->getSummary() ?></span>
                                        <a target=”_blank” href="<?= 'https://a-rakumo.appspot.com/calendar#event/google:'.$cal->getId().'/'.$e->getId() ?>"><i class="material-icons rakumo-icons">event_note</i></a>
                                    </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="event-summary no-events">今後の予定はありません</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script type="text/javascript">
        var config = {};config.users = <?= json_encode($users) ?>;
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('input.autocomplete').autocomplete((function() {
                var args = {};
                args.data = {};
                for (var i = 0; i < config.users.length; i++) {
                    args.data[config.users[i].name] = null;
                    args.data[config.users[i].email] = null;
                }
                return args;
            })());

            $('#searchword-input').focusin(function(e) {
                $('#search-field').removeClass('z-depth-1');
                $('#search-field').addClass('z-depth-3');
            });
            $('#searchword-input').focusout(function() {
                $('#search-field').removeClass('z-depth-3');
                $('#search-field').addClass('z-depth-1');
            });
        });
    </script>
</body>

</html>