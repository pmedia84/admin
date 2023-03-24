<?php
function check_login(){
    if(!isset($_SESSION['loggedin'])){
        $location = urlencode($_SERVER['REQUEST_URI']);
        header("Location: login.php?location=" . $location);
    }
}

?>