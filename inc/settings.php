<?php
//email settings for contact forms
//Settings for all form scripts
include($_SERVER['DOCUMENT_ROOT'] . "/email_settings.php");
$user = new User();
$cms = new Cms();
$cms->setup();
if ($cms->type() == "Wedding") {
    $cms->wedding_load();
}
if ($cms->type() == "Business") {
    $cms->business_load();
}
