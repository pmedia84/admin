<?php
//////////////////////////////////////////////////////////settings script for all cms websites\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
/// Define what type of website this is for \\\
//Business with services and reviews etc
//Or wedding site with rsvp features etc
// Business
// Wedding
// 
$cms_type = "Wedding";

//email settings for contact forms
//Settings for all form scripts

/// Define who the emails get sent to from forms filled out
$email_to = "";

$host = "admin.parrotmedia.co.uk"; /// Hostname
$username = "admin@admin.parrotmedia.co.uk"; ///Username
$pass = "Krb833908"; /// Password
$from = $username; ///Email address

$fromname = "Parrot Media"; /// Username and how you want your name to be displayed on emails
$emailheaderlogo = "https://www.parrotmedia.co.uk/img/pmedia-logo-new.png";//logo url for inserting into the top of email bodies

////////////////Modules Available\\\\\\\\\\\\\\\\\\\\
//Reviews
$module_reviews = "On";
$api_key = ""; //api key from google source
$place_id = ""; //Found from google places api
//Image Gallery
$module_gallery = "On";
//Services
$module_services = "On";
//News
$module_news = "On";
//Guest List
$module_guest_list = "On";
?>

