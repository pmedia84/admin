<?php
session_start();
if (!$_SESSION['loggedin'] == true) {
    // Redirect to the login page:
    header('Location: login.php');
}

include("./inc/header.inc.php");
include("./connect.php");
//find if this module is on or off

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
    echo $business_id;
}

//run checks to make sure a wedding has been set up correctly
if ($cms_type == "Wedding") {

    //look for a wedding setup in the db, if not then direct to the setup page
    $wedding_query = ('SELECT wedding_id, wedding_name FROM wedding');
    $wedding = $db->query($wedding_query);
    $wedding_details = mysqli_fetch_assoc($wedding);
    if ($wedding->num_rows == 0) {
        header('Location: setup.php?action=setup_wedding');
    }
    //check that there are users set up 
    $wedding_user_query = ('SELECT wedding_user_id FROM wedding_users');
    $wedding_user = $db->query($wedding_user_query);
    if ($wedding_user->num_rows == 0) {
        header('Location: setup.php?action=check_users_wedding');
    }

    if (!$_SESSION['loggedin'] == true) {
        // Redirect to the login page:
        header('Location: login.php');
    }
}

//////////////////////////////////////////////////////////////////Everything above this applies to each page\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\


?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Reviews</title>
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


            <div class="breadcrumbs mb-2">
                <a href="index.php" class="breadcrumb">Home</a> / Manage Reviews
            </div>
            <div class="main-cards">
                <?php if($module_reviews =="On"):?>
                    
                <h1>Reviews</h1>
                <p>Your 5 most recent reviews are displayed here and on your website.</p>
                <p>This is updated once a week, you can also update it here by clicking the below button.</p>
                <button class="btn-primary" id="get_reviews_btn" type="button"><i class="fa-solid fa-download"></i>Fetch Recent Reviews</button>
                <?php if($user_type == "Admin"):?>
                    <div id="reviews">

                    </div>
            </div>

                <?php else:?>
                    <p class="font-emphasis">You do not have the necessary Administrator rights to view this page.</p>
                <?php endif; ?>
            </div>
            <?php else:?>
                <h1>Module not activated for your website!</h1>
                <p>Contact us to find out how you can get this feature set up.</p>
            <?php endif;?>        
        </section>


    </main>

    <!-- /Main Body Of Page -->

    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->
    <script>
        $("document").ready(function() {
            url = "scripts/reviews-script.php?action=loadreviews";
            $.ajax({ //load reviews
                type: "GET",
                url: url,
                encode: true,

                success: function(data, responseText) {
                    $("#reviews").html(data);

                }
            });
        })
    </script>

    <script>
        //download reviews with button
        $("#get_reviews_btn").click(function() {
            url = "scripts/reviews-script.php?action=download";
            $.ajax({ //load reviews
                type: "GET",
                url: url,
                encode: true,

                success: function(data, responseText) {
                    $("#reviews").html(data);

                }
            });
        })
    </script>
</body>

</html>