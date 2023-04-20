<?php
//////////////////////////////////////////////////////////settings script for all cms websites\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

//connect to settings table
$settings = $db->prepare('SELECT cms_type FROM settings');
$settings->execute();
$settings->store_result();
$settings->bind_result($cms_type);
$settings->fetch();
/// Define what type of website this is for \\\
//Business with services and reviews etc
//Or wedding site with rsvp features etc
// Business
// Wedding
$cms_type = $cms_type;

//email settings for contact forms
//Settings for all form scripts

include($_SERVER['DOCUMENT_ROOT']."/email_settings.php");

////////////////Modules Available\\\\\\\\\\\\\\\\\\\\
//connect to modules table and load available modules
$modules_query = ('SELECT module_name, module_status FROM modules');
$modules = $db->query($modules_query);
$modules_result = $modules->fetch_assoc();
//Reviews
$module_reviews = "On";
$api_key = ""; //api key from google source
$place_id = ""; //Found from google places api
//Image Gallery



