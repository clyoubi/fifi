console.log('Welcome to red');

var isConnected = false;
$(document).ready(function() {
    $.ajaxSetup({
        cache: true
    });

    $.getScript('https://connect.facebook.net/en_US/sdk.js', function() {
        FB.init({
            appId: '705362880329562',
            version: 'v2.7' // or v2.1, v2.2, v2.3, ...
        });
        $('#loginButton').removeClass('d-none');
        $('#loginButton').removeAttr('disabled');
        
        FB.getLoginStatus(function(response) {
            statusChangeCallback(response)
        });
    });


});



function statusChangeCallback(response) {
    if( response.status == 'connected' ){
        var token = response.authResponse.accessToken;
        fields = "id, first_name, picture";
        FB.api('/me?fields='+fields, function(response) {
                $("#loginButton .header__button-text").html('Salut '+response.first_name+'!');
                $(".facebook-icon").addClass('d-none');
                $("#pp").removeClass('d-none');
                $("#pp").attr('src', response.picture.data.url);
                isConnected = true;
                console.log(response);
                syncUser( response.id, token );
            });
    }else{
        isConnected = false;
        $(".facebook-icon").removeClass('d-none');
        $("#pp").addClass('d-none');
        $("#loginButton .header__button-text").html('Connecte toi');
    }
    
}


function login() {


    if( isConnected ){
        if ( confirm("Voulez-vous vous déconnecter?!") ) {
            logout();
            return;
          }

    }

    FB.login(function(response) {
        if (response.authResponse) {
            location.reload();
        } else {
            console.log('User cancelled login or did not fully authorize.');
        }
    });

   
}

function logout() {
    FB.logout(function(response) {
        //statusChangeCallback(response)
        location.reload();
        console.log(response);
    });
}


function syncUser( id, token ){

    var form_data = jQuery(this).serializeArray();
    //form_data.push({ "name": "security", "value": ajax_nonce });

    form_data.push({ "name": "facebook_id", "value": id });
    form_data.push({ "name": "facebook_token", "value": token });

    console.log(form_data);
    $.ajax({
        type: "POST",
        url: 'https://rouge.smookcreative.com/api/user',
        data: form_data,
        dataType: 'json',
        success: function (response) {
            console.log(response);
            if (response.return) {
                window.location.href = response.return;
            }
        },
        fail: function (err) {
            console.log(err);
        }
    });
}