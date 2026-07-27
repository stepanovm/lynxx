<?php



?>

<?php // if(app\core\Auth::isLoggedIn()){ ?>
    <a href="/user/"><?php // echo app\core\CurrentUser::instance()->name(); ?></a>
    <!--<span onclick="logout();">Выход</span>-->
<?php // } else { ?>
    <span onclick="toggleLoginForm();">войти в Личный кабинет</span>
<?php // }
