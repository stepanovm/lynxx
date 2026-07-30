<?php
/** required: */
/** @var bool $isLoggedIn */
?>



<script>
    $('#btnSignIn').on('click', function(){
        console.log('test');
        doAuth();
    });

    function doAuth() {
        console.log('auth launched!');
        $.ajax({
            url: '/auth/auth_by_pass',
            type: 'POST',
            dataType: 'json',
            data: { login : $('#login').val(), pass : $('#password').val() },
            success: function(jsonData){
                if(jsonData['error']){
                    alert('Ошибка: ' + jsonData['error_msg']);
                    console.log(jsonData);
                } else {
                    console.log(jsonData);
                    if( location.href.indexOf('registration') > 0 ){
                        setTimeout(function(){ goToUrl('/'); }, 400);
                        return;
                    }
                    //let url = location.href.indexOf('?') > 0 ? location.href + '&auth=1' : location.href + '?auth=1';
                    setTimeout(function(){ location.href = location.href; }, 400);
                }
            },
            error: function(jqXHR, status, msg){
                alert('Произошла ошибка, попробуйте повторить позже.');
                console.log(jqXHR); console.log(msg+' '+status);
            }
        });
    }
</script>


<?php if($isLoggedIn){ ?>
    <a href="/user/"><?php \Lynxx\Lynxx::Auth()->getCurrentUser()->getLogin(); ?></a>
    <span onclick="logout();">Выход</span>
<?php } else { ?>
    <form>
        <p>Логин:</p>
        <input id="login" name="login" type="text" value="">
        <p>Пароль:</p>
        <input id="password" name="password" type="password" value="">
        <input type="button" id="btnSignIn" value="Войти" />
    </form>
<?php } ?>