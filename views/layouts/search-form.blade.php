@extends('layouts.master')
@section('title')
いまどこ検索 - @yield('pagename')
@endsection
@section('stylesheet')
@yield('stylesheet1')
@endsection

@section('content')
    <div class="container">
        <a href="/"><object class="title-header" type="image/svg+xml" data="/logo/logo.svg"></object></a>
    
        <div class="row search-field-wrapper">
            <form id="search-field-form" class="col s12 offset-m2 m8 search-field-form" action="" method="GET">
                <div id="search-field" class="round-card search-field z-depth-1">
                    <div class="input-field search-field-input-query">
                        <i class="material-icons prefix">search</i>
                        <input type="text" name="q" id="searchword-input" class="auto-complete" value="@yield('query')">
                        <label for="searchword-input">名前 or メールアドレス</label>
                    </div>
                    <div class="input-field search-field-submit-button">
                        <button id="search-field-form-btn" class="btn waves-effect waves-light" type="submit">検索</button>
                    </div>
                </div>
            </form>
        </div>
        @yield('error')
        @yield('main')
    </div>
@endsection

@section('script')
<script type="text/javascript">
    var config = {};config.users = @json(DomainUsers::LoadDomainUsers());
</script>
<script src="/script/searchform.js"></script>
@endsection