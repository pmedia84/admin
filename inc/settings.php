<?php
include($_SERVER['DOCUMENT_ROOT'] . "/email_settings.php");
$user = new User();
$cms = new Cms();

if ($cms->type() == "Business") {
    $cms->business_load();
}
$cms->setup();
