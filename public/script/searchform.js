$(document).ready(function () {
    $('input.auto-complete').autocomplete({
        source : function(request, response) {
            var re   = new RegExp(request.term, 'i'),
                list = [];
                    
            $.each(config.users, function(i, _user) {
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

    $(window).keydown(function(e){
        if(event.ctrlKey || event.metaKey){
            if(e.keyCode === 70 || e.keyCode === 76){
                $('#searchword-input').focus().select();
                return false;
            }
        }
    });
});