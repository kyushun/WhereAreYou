@extends('layouts.master')
@section('title')
いまどこ検索 - @yield('pagename')
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
                            var emailReg = new RegExp('^.*?' + request.term +'.*?@', 'i');
                            var furiganaReg = new RegExp('^.*?' + toHebon(request.term) +'.*?@', 'i');
                            if (_user.email.match(emailReg) || _user.email.match(furiganaReg)) {
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
            delay: 200
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
@endsection