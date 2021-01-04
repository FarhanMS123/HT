<?php
    include __DIR__ . "/config.php";
    include $root_path . "/login.api.php";
    include $root_path . "/team.api.php";
    
    $current_user = getCurrentUser(false);
    $team = false;
    if($current_user) $team = checkUserTeam($current_user["email"]);

    function onlyWordDigit($str){
        $str_c = preg_replace("/[^\w\d]/", "", $str);
        return $str_c;
    }

    // this is only works if browser respect HTTP_X_REQUESTED_WITH header, and users use new browser.
    // https://stackoverflow.com/a/1756970/5832341
    function checkXHR(){
        return isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' );
    }

    // this is only works if browser respect ajax, and users use new browser.
    // https://stackoverflow.com/questions/2896623/how-to-prevent-my-site-page-to-be-loaded-via-3rd-party-site-frame-of-iframe
    function checkIFrame(){
        return true;
    }

    // use `htmlspecialchars` instead
    // function holdQuotesHTML($str){ // " ' ` < >
    //     //
    // }

    function checkEmail($email){
        return preg_match("/^[\w\-\.]+@([\w\-]+\.)+[\w\-]{2,4}$/", $email);
    }
?>