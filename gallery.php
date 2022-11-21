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

//find business details.
$business = $db->prepare('SELECT * FROM business WHERE business_id =' . $business_id);

$business->execute();
$business->store_result();
$business->bind_result($business_id, $business_name, $address_id, $business_phone, $business_email, $business_contact_name);
$business->fetch();
$business->close();
//////////////////////////////////////////////////////////////////Everything above this applies to each page\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\


?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | News</title>
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
                <a href="index.php" class="breadcrumb">Home</a> / Manage Image Gallery
            </div>
            <div class="main-cards loading">

                <h1>Image Gallery</h1>

                <?php if ($user_type == "Admin") : ?>
                    <button class="btn-primary" id="upload_image"><i class="fa-solid fa-upload"></i>Upload Image </button>
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
    <div class="modal">
        <div class="modal-body">
            <div class="modal-close">
                <button type="button" class="btn-close" id="modal-btn-close" aria-label="Close"></button>
            </div>
            <h2>Upload New Image</h2>
            <form action="scripts/gallery.script.php" id="img-upload" method="post" enctype="multipart/form-data">
                <div class="form-input-wrapper">
                    <label for="image_title">Image Title</label>
                    <!-- input -->
                    <input class="text-input input" type="text" name="image_title" id="image_title" placeholder="Image Title" maxlength="45">
                </div>
                <div class="form-input-wrapper">
                    <label for="image_description">Image Description</label>
                    <!-- input -->
                    <input class="text-input input" type="text" name="image_description" id="image_description" placeholder="Image Description" maxlength="45">
                </div>
                <div class="form-input-wrapper my-2">
                    <label for="gallery_img">Upload Image</label>
                    <p class="form-hint-small">This can be in a JPG, JPEG or PNG format. And no larger than 1MB.</p>
                    <!-- input -->
                    <input type="file" name="gallery_img" id="gallery_img" accept="image/*">
                </div>

                <h3>Image Placement</h3>
                <div class="my-2">
                    
                        
                        <label class="checkbox-form-control" for="home">
                        <input type="checkbox" id="home" name="img_placement[]" value="Home"/>
                        Home Screen
                        </label>
                   
                        
                        <label class="checkbox-form-control" for="gallery">
                        <input type="checkbox" id="gallery" name="img_placement[]" value="Gallery"/>    
                        Photo Gallery
                        </label>
                        
                  

                </div>

                <div class="modal-button-section">
                    <button class="btn-primary form-controls-btn loading-btn" type="submit"><span id="loading-btn-text" class="loading-btn-text"><i class="fa-solid fa-upload"></i>Upload Image</span> <img id="loading-icon" class="loading-icon d-none" src="./assets/img/icons/loading.svg" alt=""></button>
                    <button class="btn-primary btn-secondary form-controls-btn" id="cancel" type="button"><i class="fa-solid fa-ban"></i>Cancel</button>
                </div>
            </form>

            <div id="response" class="d-none">



            </div>
        </div>


        </form>
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
    <script>
        //script for uploading a new image and posting to backend article
        $("#img-upload").submit(function(event) {
            event.preventDefault();


            var formData = new FormData($("#img-upload").get(0));

            formData.append("action", "newimg");

            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/gallery.script.php",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() { //animate button
                    $("#loading-icon").show(400);
                },
                complete: function() {
                    $("#loading-icon").hide(400);
                },
                success: function(data, responseText) {
                    $("#response").html(data);
                    $("#response").slideDown(400);
                    $("#img-upload")[0].reset();
                    url = "scripts/gallery.script.php?action=loadgallery";
                    $.ajax({ //load image gallery
                        type: "GET",
                        url: url,
                        encode: true,

                        success: function(data, responseText) {
                            $("#img-gallery").html(data);
                            

                        }
                    });

                }
            });

        });
    </script>
</body>

</html>