<?php
session_start();
require("scripts/functions.php");
check_login();

include("connect.php");
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
    $wedding->bind_result($wedding_id, $wedding_name, $wedding_date, $wedding_time,   $wedding_email, $wedding_phone, $wedding_contact_name);
    $wedding->fetch();
    $wedding->close();
    //set cms name
    $cms_name = $wedding_name;
    //find user details for this business
    $wedding_users = $db->prepare('SELECT users.user_id, users.user_name, wedding_users.wedding_id, wedding_users.user_type FROM users NATURAL LEFT JOIN wedding_users WHERE users.user_id=' . $user_id);

    $wedding_users->execute();
    $wedding_users->bind_result($user_id, $user_name, $wedding_id, $user_type);
    $wedding_users->fetch();
    $wedding_users->close();
}

//////////////////////////////////////////////////////////////////Everything above this applies to each page\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
//image variable
if (isset($_GET['image_id'])) {
    $image_id = $_GET['image_id'];
    //find image details

    $image = $db->prepare('SELECT * FROM images WHERE image_id =' . $image_id);

    $image->execute();
    $image->store_result();
}else{
    $image_id="";
}




?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Manage Image Gallery</title>
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
                <a href="index.php" class="breadcrumb">Home</a> /
                <a href="gallery.php" class="breadcrumb">Image Gallery</a>
                <?php if ($_GET['action'] == "edit") : ?>
                    / Edit Image
                <?php endif; ?>
                <?php if ($_GET['action'] == "delete") : ?>
                    / Delete Image
                <?php endif; ?>
                <?php if ($_GET['action'] == "view") : ?>
                    / View Image
                <?php endif; ?>
            </div>
            <div class="main-cards">
                <?php if ($_GET['action'] == "edit") : ?>
                    <h1><i class="fa-solid fa-image"></i> Edit Image</h1>
                <?php endif; ?>
                <?php if ($_GET['action'] == "view") : ?>
                    <h1><i class="fa-solid fa-image"></i> View Image</h1>
                <?php endif; ?>
                <?php if ($_GET['action'] == "delete") : ?>
                    <h1><i class="fa-solid fa-image"></i> Delete Image</h1>
                <?php endif; ?>


                <?php if ($user_type == "Admin" || $user_type == "Developer") : //detect if user is an admin or not 
                ?>
                    <?php if ($_GET['action'] == "delete") : //if action is delete, detect if the confirm is yes or no
                    ?>
                        <?php if ($_GET['confirm'] == "yes") : //if yes then delete the article
                        ?>
                            <?php if (($image->num_rows) > 0) :
                                $image->bind_result($image_id, $image_title, $image_description, $image_filename, $image_upload_date, $image_placement);
                                $image->fetch();
                                // connect to db and delete the record
                                $delete_image = "DELETE FROM images WHERE image_id=" . $image_id;
                                //delete image on server
                                $file = $_SERVER['DOCUMENT_ROOT'] . "/admin/assets/img/gallery/" . $image_filename;
                                $gallery = $_SERVER['DOCUMENT_ROOT'] . "/assets/img/gallery/" . $image_filename;
                                if (fopen($file, "w")) {
                                    unlink($file);
                                };
                                if (fopen($gallery, "w")) {
                                    unlink($gallery);
                                };
                                if (mysqli_query($db, $delete_image)) {
                                    echo '<div class="std-card"><div class="form-response error"><p>' . $image_title . ' Has Been Deleted</p></div></div>';
                                } else {
                                    echo '<div class="form-response error"><p>Error deleting image, please try again.</p></div>';
                                }
                            ?>
                            <?php else : ?>
                                <div class="std-card">
                                    <h2>Error</h2>
                                    <p>There has been an error, please return to the last page and try again.</p>
                                </div>
                            <?php endif; ?>
                        <?php else : //if not then display the message to confirm the user wants to delete the news article
                        ?>
                            <?php if (($image->num_rows) > 0) :
                                $image->bind_result($image_id, $image_title, $image_description, $image_filename, $image_upload_date, $image_placement);
                                $image->fetch();



                            ?>
                                <div class="std-card">
                                    <h2 class="text-alert">Delete: <?= $image_title; ?></h2>
                                    <p><?= $image_filename; ?></p>
                                    <img src="./assets/img/gallery/<?= $image_filename; ?>" alt="" class="delete-thumb my-3">
                                    <p>Are you sure you want to delete this image?</p>
                                    <p><strong>This Cannot Be Reversed</strong></p>
                                    <div class="button-section">
                                        <a class="btn-primary btn-delete my-2" href="image.php?action=delete&confirm=yes&image_id=<?= $image_id; ?>"><i class="fa-solid fa-trash"></i>Delete Image</a>
                                        <a class="btn-primary btn-secondary my-2" href="image.php?action=view&image_id=<?= $image_id; ?>"><i class="fa-solid fa-ban"></i>Cancel</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>



                    <?php endif; ?>

                    <?php if ($_GET['action'] == "edit") : ?>
                        <?php if (($image->num_rows) > 0) :
                            $image->bind_result($image_id, $image_title, $image_description, $image_filename, $image_upload_date, $image_placement);
                            $image->fetch();

                        ?>
                            <div class="std-card">
                                <form id="edit_image" action="scripts/gallery.script.php" method="POST" enctype="multipart/form-data">
                                    <div class="form-input-wrapper">

                                        <p><strong>File Name:</strong> <?= $image_filename; ?></p>
                                        <label for="image_title"><strong>Image Title</strong></label>
                                        <!-- input -->
                                        <input class="text-input input" type="text" name="image_title" id="image_title" placeholder="Image Title" maxlength="45" value="<?= $image_title; ?>">
                                    </div>
                                    <div class="form-input-wrapper my-2">
                                        <img src="./assets/img/gallery/<?= $image_filename ?>" alt="">
                                    </div>
                                    <div class="form-input-wrapper my-2">
                                        <label for="image_description"><strong>Image Caption</strong></label>
                                        <p class="form-hint-small">This is not essential, but can be useful.</p>
                                        <input class="text-input input" type="text" id="image_description" name="image_description" placeholder="Image Caption" value="<?= $image_description; ?>">
                                    </div>
                                    <div class="my-2">

                                        <h2>Image Placement</h2>
                                        <p class="form-hint-small my-2">This determines where you image will be displayed on your website. You can use the same image in different locations if you wish.</p>
                                        <label class="checkbox-form-control" for="home">
                                            <input type="checkbox" id="home" name="img_placement[]" value="Home" <?php if (strpos($image_placement, "Home") !== FALSE) : ?>Checked <?php endif; ?> />
                                            Home Screen
                                        </label>


                                        <label class="checkbox-form-control" for="gallery">
                                            <input type="checkbox" id="gallery" name="img_placement[]" value="Gallery" <?php if (strpos($image_placement, "Gallery") !== FALSE) : ?>Checked <?php endif; ?> />
                                            Photo Gallery
                                        </label>



                                    </div>
                                    <div class="button-section my-3">
                                        <button class="btn-primary form-controls-btn" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save Changes </button>
                                        <a href="gallery" class="btn-primary btn-secondary form-controls-btn"><i class="fa-solid fa-ban"></i> Cancel Changes</a>
                                    </div>
                                    <div id="response" class="d-none">
                                        <p>Article Saved <img src="./assets/img/icons/check.svg" alt=""></p>
                                    </div>
                                </form>
                            </div>
                        <?php else : ?>
                            <div class="std-card">
                                <h2>Error</h2>
                                <p>There has been an error, please return to the last page and try again.</p>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($_GET['action'] == "create") : ?>

                        <div class="std-card">
                            <h1><i class="fa-solid fa-image"></i> Upload New Image</h1>
                            <form action="scripts/gallery-multiple.php" id="img-upload" method="POST" enctype="multipart/form-data">
                                <div class="form-input-wrapper">
                                    <label for="image_title">Image Title</label>
                                    <!-- input -->
                                    <input class="text-input input" type="text" name="image_title" id="image_title" placeholder="Image Title" maxlength="45">
                                </div>
                                <div class="form-input-wrapper">
                                    <label for="image_description">Image Description</label>
                                    <p class="form-hint-small">This is shown in your slideshow</p>
                                    <!-- input -->
                                    <input class="text-input input" type="text" name="image_description" id="image_description" placeholder="Image Description" maxlength="45">
                                </div>
                                <div class="form-input-wrapper my-2">
                                    <label for="gallery_img">Upload Image</label>
                                    <p class="form-hint-small">This can be in a JPG, JPEG or PNG format. And no larger than 1MB.</p>
                                    <!-- input -->
                                    <input type="file" name="gallery_img[]" id="gallery_img" accept="image/*" multiple>
                                </div>

                                <h3>Image Placement</h3>
                                <div class="my-2">
                                    <label class="checkbox-form-control" for="home">
                                        <input type="checkbox" id="home" name="img_placement[]" value="Home" />
                                        Home Screen
                                    </label>
                                    <label class="checkbox-form-control" for="gallery">
                                        <input type="checkbox" id="gallery" name="img_placement[]" value="Gallery" />
                                        Photo Gallery
                                    </label>
                                </div>

                                <div class="button-section my-3">
                                    <button class="btn-primary form-controls-btn loading-btn" type="submit"><span id="loading-btn-text" class="loading-btn-text"><i class="fa-solid fa-upload"></i>Upload Image</span> <img id="loading-icon" class="loading-icon d-none" src="./assets/img/icons/loading.svg" alt=""></button>
                                    <a class="btn-primary btn-secondary" href="gallery" type="button"><i class="fa-solid fa-ban"></i>Cancel</a>
                                </div>
                                <div id="response" class="d-none"></div>
                            </form>
                        </div>

                    <?php endif; ?>


                    <?php if ($_GET['action'] == "view") : ?>
                        <?php if (($image->num_rows) > 0) :
                            $image->bind_result($image_id, $image_title, $image_description, $image_filename, $image_upload_date, $image_placement);
                            $image->fetch();
                            $upload_date = strtotime($image_upload_date);
                        ?>
                            <div class="std-card">
                                <h2 class="my-2"><?= $image_title; ?></h2>
                                <img src="./assets/img/gallery/<?= $image_filename ?>" alt="">
                                <p class="my-2">Image Uploaded: <?= date('d-m-y', $upload_date); ?></p>
                                <div class="news-create-body"><?= $image_description; ?></div>
                                <p><strong>Image Placement:</strong></p>
                                <p><?= $image_placement; ?></p>
                                <div class="card-actions">
                                    <a class="my-2" href="image.php?action=edit&image_id=<?= $image_id; ?>"><i class="fa-solid fa-pen-to-square"></i> Edit Image </a><br>
                                    <a class="my-2" href="image.php?action=delete&confirm=no&image_id=<?= $image_id; ?>"><i class="fa-solid fa-trash"></i> Delete Image </a>
                                </div>
                            <?php else : ?>
                                <div class="std-card">
                                    <h2>Error</h2>
                                    <p>There has been an error, please return to the last page and try again.</p>
                                </div>
                            <?php endif; ?>
                            </div>

            </div>



            
        <?php endif; ?>

    <?php else : ?>
        <p class="font-emphasis">You do not have the necessary Administrator rights to view this page.</p>
    <?php endif; ?>
    

        </section>


    </main>

    <!-- /Main Body Of Page -->
    <!-- Quote request form script -->

    <!-- /Quote request form script -->
    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->

    <script>
        //script for editing a news article
        $("#edit_image").submit(function(event) {

            event.preventDefault();
            //declare form variables and collect GET request information
            image_id = '<?php echo $image_id; ?>';
            var formData = new FormData($("#edit_image").get(0));
            formData.append("action", "edit");
            formData.append("image_id", image_id);
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/gallery.script.php",
                data: formData,
                contentType: false,
                processData: false,
                success: function(data, responseText) {
                    $("#response").html(data);
                    $("#response").slideDown(400);
                    window.location.replace('gallery');
                }
            });

        });
    </script>
    <script>
        //script for uploading a new image and posting to backend article
        $("#img-upload").submit(function(event) {
            event.preventDefault();


            var formData = new FormData($("#img-upload").get(0));

            formData.append("action", "newimg");

            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/gallery-multiple.php",
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
                    if (data === "0") {
                        window.location.replace("gallery");
                    }else{
                        $("#response").html(data);
                        $("#response").slideDown(400);
                    }
                    $("#img-upload")[0].reset();


                }
            });

        });
    </script>
</body>

</html>