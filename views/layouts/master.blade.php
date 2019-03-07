<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title')</title>
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
    @yield('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="/script/toHebon.js"></script>
    @yield('script')
</body>
</html>