<?php
session_start();
if (!$_SESSION['loggedin'] == true) {
    // Redirect to the login page:
    header('Location: login.php');
}
include("./inc/header.inc.php");
include("./connect.php");
//find users and display on screen.
//connect to user db to check admin rights etc
//find username and email address to display on screen.
$user = $db->prepare('SELECT user_id,  user_type, business_id FROM users WHERE user_id = ?');
$user->bind_param('s', $_SESSION['user_id']);
$user->execute();
$user->store_result();
$user->bind_result($user_id, $user_type, $business_id);
$user->fetch();
$user->close();
//find social media info
$socials_query = ('SELECT business_socials.business_socials_id, business_socials.socials_type_id, business_socials.business_socials_url, business_socials.business_id, business_socials_types.socials_type_id, business_socials_types.socials_type_name   FROM business_socials  NATURAL LEFT JOIN business_socials_types WHERE  business_socials.business_id =' . $business_id);
$socials = $db->query($socials_query);
$social_result = $socials->fetch_assoc();

//find business details.
$business = $db->prepare('SELECT * FROM business WHERE business_id =' . $business_id);

$business->execute();
$business->store_result();
$business->bind_result($business_id, $business_name, $address_id, $business_phone, $business_email, $business_contact_name);
$business->fetch();
$business->close();

//////////////////////////////////////////////////////////////////Everything above this applies to each page\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
//find business address details.
$business = $db->prepare('SELECT * FROM addresses WHERE address_id =' . $address_id);

$business->execute();
$business->store_result();
$business->bind_result($address_id, $address_house, $address_road, $address_town, $address_county, $address_pc);
$business->fetch();
$business->close();
$db->close();
?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Settings</title>
<!-- /Page Title -->
</head>

<body>


    <!-- Main Body Of Page -->
    <main class="main col-2">


        <!-- Header Section -->
        <div class="header">
            <h1 class="header-name mb-3"><?php date_default_timezone_set("Europe/London");
                                            $h = date('G');
                                            if ($h >= 5 && $h <= 11) {
                                                echo "Good morning";
                                            } else if ($h >= 12 && $h <= 15) {
                                                echo "Good Afternoon";
                                            } else {
                                                echo "Good Evening";
                                            }
                                            ?> <?= $_SESSION['user_name']; ?></h1>
            <div class="header-actions">
                <div class="header-actions-btn-section">
                    <div class="header-actions-navbtn">
                        <a href="#" class="nav-btn" id="nav-btn"><img src="assets/img/icons/menu-bars.svg" alt=""></a>
                    </div>

                    <a class="header-actions-btn-user" href="">
                        <img src="assets/img/icons/user.svg" alt="">
                        <img src="assets/img/icons/down.svg" alt="">
                    </a>
                    <div class="header-actions-business-name">
                        <h2><?= $business_name; ?></h2>
                    </div>

                    <a class="header-actions-btn-logout" href="logout.php"><span>Logout</span><img src="assets/img/icons/logout.svg" alt=""></a>
                </div>
            </div>
        </div>
        <!-- Nav Bar -->
        <?php include("./inc/nav.inc.php"); ?>
        <!-- /nav bar -->
        <section class="body">


            <div class="breadcrumbs mb-2"><a href="index.php" class="breadcrumb">Home</a> / Settings</div>
            <div class="main-cards">

                <h1>Settings</h1>
                <?php
                if ($user_type == "Admin") :

                ?>
                    <div class="std-card">
                        <h2>Business Details</h2>
                        <p><strong>Business Name:</strong> <?= $business_name; ?></p>
                        <p><strong>Email Address:</strong> <?= $business_email; ?></p>
                        <p><strong>Primary Contact No.:</strong> <?= $business_phone; ?></p>
                        <p><strong>Business Contact Name.:</strong> <?= $business_contact_name; ?></p>

                        <a href="edit_businessdetails.php" class="my-2">Edit Business Details</a>
                    </div>
                    <div class="std-card">
                        <h2>Social Media Details</h2>
                        <p>These are your social media details, make sure these links are correct, clients will follow these links from your website to your social media pages.</p>
                        <?php

                        foreach ($socials as $social) : ?>
                            <p><strong>Name:</strong> <?= $social['socials_type_name']; ?></p>
                            <p><strong>URL:</strong> <?= $social['business_socials_url']; ?></p>
                            
                        <?php endforeach; ?>
                        <a class="my-2" href="edit_socialmedia.php">Edit Social Media Details</a>

                    </div>
                    <div class="std-card">
                        <h2>Primary Business Address</h2>
                        <p>Make sure this is up to date, this address is displayed on your contact page.</p>
                        <p><?= $address_house ?></p>
                        <p><?= $address_road ?></p>
                        <p><?= $address_town ?></p>
                        <p><?= $address_county ?></p>
                        <p><?= $address_pc ?></p>
                        <a class="my-2" href="edit_address.php">Edit Address</a>
                    </div>
                <?php else : ?>
                    <p class="font-emphasis">You do not have the necessary Administrator rights to view this page.</p>
                <?php endif; ?>
            </div>

        </section>


    </main>

    <!-- /Main Body Of Page -->
    <!-- Quote request form script -->

    <!-- /Quote request form script -->
    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->

</body>

</html>