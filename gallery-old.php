<?php
session_start();
$location=$_SERVER['REQUEST_URI'];
$location=urlencode($_SERVER['REQUEST_URI']);
if (!$_SESSION['loggedin'] == TRUE) {
    // Redirect to the login page:
    
    header("Location: login.php?location=".$location);
}

include("./connect.php");
include("inc/head.inc.php");
include("inc/settings.php");
////////////////Find details of the cms being used, on every page\\\\\\\\\\\\\\\
//Variable for name of CMS
//wedding is the name of people
//business name
$cms_name ="";
$user_id = $_SESSION['user_id'];
if ($cms_type == "Business") {
    //look for the business set up and load information
    //find business details.
    $business = $db->prepare('SELECT * FROM business');

    $business->execute();
    $business->store_result();
    $business->bind_result($business_id, $business_name, $address_id, $business_phone, $business_email, $business_contact_name);
    $business->fetch();
    $business->close();
    //set cms name
    $cms_name = $business_name;
    //find user details for this business
    $business_users = $db->prepare('SELECT users.user_id, users.user_name, business_users.business_id, business_users.user_type FROM users NATURAL LEFT JOIN business_users WHERE users.user_id='.$user_id);

    $business_users->execute();
    $business_users->bind_result($user_id, $user_name,$business_id, $user_type);
    $business_users->fetch();
    $business_users->close();
}

//run checks to make sure a wedding has been set up correctly
if ($cms_type == "Wedding") {
    //look for the Wedding set up and load information
    //find Wedding details.
    $wedding = $db->prepare('SELECT * FROM wedding');

    $wedding->execute();
    $wedding->store_result();
    $wedding->bind_result($wedding_id, $wedding_name, $wedding_date, $wedding_time,  $wedding_email, $wedding_phone, $wedding_contact_name);
    $wedding->fetch();
    $wedding->close();
    //set cms name
    $cms_name = $wedding_name;
    //find user details for this business
    $business_users = $db->prepare('SELECT users.user_id, users.user_name, wedding_users.wedding_id, wedding_users.user_type FROM users NATURAL LEFT JOIN wedding_users WHERE users.user_id='.$user_id);

    $business_users->execute();
    $business_users->bind_result($user_id, $user_name,$business_id, $user_type);
    $business_users->fetch();
    $business_users->close();
}


//////////////////////////////////////////////////////////////////Everything above this applies to each page\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\


?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Photo Gallery</title>
<!-- /Page Title -->
</head>

<body>
    <!-- Main Body Of Page -->
    <main class="main col-2">


        <!-- Header Section -->
        <?php include("inc/header.inc.php");?>
        <!-- Nav Bar -->
        <?php include("./inc/nav.inc.php"); ?>
        <!-- /nav bar -->
        <section class="body">


            <div class="breadcrumbs mb-2">
                <a href="index.php" class="breadcrumb">Home</a> / Manage Image Gallery
            </div>
            <div class="main-cards loading">

                <h1><i class="fa-solid fa-images"></i> Image Gallery</h1>

                <?php if ($user_type == "Admin" || $user_type=="Developer") : ?>
                    <a class="btn-primary" href="image?action=create"><i class="fa-solid fa-upload"></i>Upload Image </a>
                    <div id="img-gallery">




                    </div>
                <?php else : ?>
                    <p class="font-emphasis">You do not have the necessary Administrator rights to view this page.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <div class="loader">
        <img class="loader-spinner" src="./assets/img/icons/loading.svg" alt="">
    </div>
    
    </div>
    <!-- /Main Body Of Page -->

    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->
    <script>
        $("document").ready(function() {
            url = "scripts/gallery.script.php?action=loadgallery";
            $.ajax({ //load image gallery
                type: "GET",
                url: url,
                encode: true,
                complete: function() { //remove loader
                    $(".loader").fadeOut(400);
                },
                success: function(data, responseText) {
                    $("#img-gallery").html(data);

                }
            });
        })
    </script>

</body>

</html>