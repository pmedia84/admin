<?php
session_start();
$location = $_SERVER['REQUEST_URI'];
$location = urlencode($_SERVER['REQUEST_URI']);
if (!$_SESSION['loggedin'] == TRUE) {
    // Redirect to the login page:

    header("Location: login.php?location=" . $location);
}

include("./connect.php");
include("inc/head.inc.php");
include("inc/settings.php");
////////////////Find details of the cms being used, on every page\\\\\\\\\\\\\\\
//Variable for name of CMS
//wedding is the name of people
//business name
$cms_name = "";
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
    $business_users = $db->prepare('SELECT users.user_id, users.user_name, business_users.business_id, business_users.user_type FROM users NATURAL LEFT JOIN business_users WHERE users.user_id=' . $user_id);

    $business_users->execute();
    $business_users->bind_result($user_id, $user_name, $business_id, $user_type);
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
    $business_users = $db->prepare('SELECT users.user_id, users.user_name, wedding_users.wedding_id, wedding_users.user_type FROM users NATURAL LEFT JOIN wedding_users WHERE users.user_id=' . $user_id);

    $business_users->execute();
    $business_users->bind_result($user_id, $user_name, $business_id, $user_type);
    $business_users->fetch();
    $business_users->close();
}


//////////////////////////////////////////////////////////////////Everything above this applies to each page\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

//load images
$gallery_query = $db->query('SELECT * FROM images');
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
        <?php include("inc/header.inc.php"); ?>
        <!-- Nav Bar -->
        <?php include("./inc/nav.inc.php"); ?>
        <!-- /nav bar -->
        <section class="body">
            <div class="breadcrumbs mb-2">
                <a href="index.php" class="breadcrumb">Home</a> / Manage Image Gallery
            </div>
            <div class="main-cards">
                <h1><i class="fa-solid fa-images"></i> Image Gallery</h1>
                <?php if ($user_type == "Admin" || $user_type == "Developer") : ?>
                    <div class="gallery-body" id="gallery-body">
                        <form action="scripts/gallery.scriptnew.php" id="gallery" method="POST">
                            <div class="form-controls gallery-controls">
                                <button class="btn-primary form-controls-btn" data-action="delete" id="delete-btn"><i class="fa-solid fa-trash"></i>Delete Selected Images </button>
                                <button class="btn-primary" type="button" id="upload-show"><i class="fa-solid fa-upload"></i>Upload Images </button>
                                <div class="form-input-wrapper">
                                    <label for="placement">Image Placement</label>
                                    <select name="placement" id="placement" data-action="placement">
                                        <option value="">Select</option>
                                        <option value="Home">Home Page</option>
                                        <option value="Gallery">Gallery</option>
                                    </select>
                                </div>
                            </div>
                            <div class="d-none  my-2" id="upload-card">
                                <div class="form-input-wrapper gallery-card">
                                    <div class="close"><button class="btn-close" type="button" id="close-upload"><i class="fa-solid fa-xmark"></i></button></div>
                                    <label for="gallery_img">Upload Images</label>
                                    <p class="form-hint-small">This can be in a JPG, JPEG or PNG format</p>
                                    <!-- input -->
                                    <input type="file" name="gallery_img[]" id="gallery_img" accept="image/*" multiple>
                                    <div class="button-section"><button class="btn-primary my-2 form-controls-btn loading-btn" type="button" id="upload-btn" data-action="upload"><span id="loading-btn-text" class="loading-btn-text"><i class="fa-solid fa-upload"></i>Upload Image</span> <img id="loading-icon" class="loading-icon d-none" src="./assets/img/icons/loading.svg" alt=""></button></div>
                                </div>
                            </div>
                            <p class="text-center my-2">To change a caption, tap or click on caption itself.</p>
                            <div class="gallery-card">
                                <table class="gallery-table">
                                    <tbody>
                                        <tr>
                                            <th><input type="checkbox" name="" id="check_all"></th>
                                            <th class="image-details">Image</th>
                                            <th>Caption</th>
                                            <th>Image Placement</th>
                                        </tr>
                                        <?php foreach ($gallery_query as $img) : ?>
                                            <tr>
                                                <td class="gallery-select"><input class="gallery-select" data-select="false" type="checkbox" name="image_id[]" id="" value="<?= $img['image_id']; ?>"></td>
                                                <td class="gallery-thumb"><a href=""><img src="/admin/assets/img/gallery/<?= $img['image_filename']; ?>" alt=""><?= $img['image_filename']; ?></a></td>
                                                <td class="caption" contenteditable="true" data-imgid="<?= $img['image_id']; ?>" data-action="edit_caption"><?= $img['image_description']; ?></td>
                                                <td><?= $img['image_placement']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                <?php else : ?>
                    <p class="font-emphasis">You do not have the necessary Administrator rights to view this page.</p>
                <?php endif; ?>
            </div>
        </section>
        <div class="d-none" id="response-card-wrapper">
            <div class="response-card">
                <div class="response-card-icon">
                <i class="fa-solid fa-circle-info"></i>
                </div>
                <div class="response-card-body">
                    <p>Images Uploaded Successfully</p>
                </div>
            </div>
        </div>
    </main>


    </div>
    <!-- /Main Body Of Page -->

    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->
    <script>

    </script>
    <script src="assets/js/gallery.js"></script>

</body>

</html>