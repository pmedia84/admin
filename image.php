<?php
session_start();
if (!$_SESSION['loggedin'] == true) {
    // Redirect to the login page:
    header('Location: login.php');
}
include("./inc/header.inc.php");
include("connect.php");
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
//image variable
$image_id = $_GET['image_id'];
//find image details

$image = $db->prepare('SELECT * FROM images WHERE image_id =' . $image_id);

$image->execute();
$image->store_result();


?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Create News Article</title>
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
                    <h1>Edit Image</h1>
                <?php endif; ?>
                <?php if ($_GET['action'] == "view") : ?>
                    <h1>View Image</h1>
                <?php endif; ?>
                <?php if ($_GET['action'] == "delete") : ?>
                    <h1>Delete Image</h1>
                <?php endif; ?>

                <?php if ($_GET['action'] == "edit") : ?>
                    <p class="font-emphasis">This page is best viewed on a large screen</p>
                <?php else : ?>
                <?php endif; ?>
                <?php if ($user_type == "Admin") : //detect if user is an admin or not 
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
                                $file =  "assets/img/gallery/".$image_filename;
                               
                                if(fopen($file,"w")){
                                    unlink($file);
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
                                <form class="form-card" id="edit_image" action="scripts/gallery.script.php" method="POST" enctype="multipart/form-data">
                                    <div class="form-input-wrapper">

                                        <p><strong>File Name:</strong> <?= $image_filename; ?></p>
                                        <label for="image_title"><strong>Image Title</strong></label>
                                        <!-- input -->
                                        <input class="text-input input" type="text" name="image_title" id="image_title" placeholder="Image Title" required="" maxlength="45" value="<?= $image_title; ?>">
                                    </div>
                                    <div class="form-input-wrapper my-2">
                                        <img src="./assets/img/news/<?= $image_filename ?>" alt="">
                                    </div>
                                    <div class="form-input-wrapper my-2">
                                        <label for="image_description"><strong>Image Description</strong></label>
                                        <p class="form-hint-small">This is not essential, but can be useful.</p>
                                        <input class="text-input input" type="text" id="image_description" name="image_description" placeholder="Image Description" value="<?= $image_description; ?>">
                                    </div>
                                    <div class="my-2">

                                        <h2>Image Placement</h2>
                                        <p class="form-hint-small my-2">This determines where you image will be displayed on your website. You can use the same image in different locations if you wish.</p>
                                        <label class="checkbox-form-control" for="home">
                                            <input type="checkbox" id="home" name="img_placement[]" value="Home" <?php if (str_contains($image_placement, "Home")) :?>Checked <?php endif; ?> />
                                            Home Screen
                                        </label>


                                        <label class="checkbox-form-control" for="gallery">
                                            <input type="checkbox" id="gallery" name="img_placement[]" value="Gallery" <?php
                                                                                                                        if (str_contains($image_placement, "Gallery")) :
                                                                                                                        ?>Checked <?php endif; ?> />
                                            Photo Gallery
                                        </label>



                                    </div>
                                    <div class="button-section my-3">
                                        <button class="btn-primary form-controls-btn" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save Changes </button>
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

                    <?php if ($_GET['action'] == "view") : ?>
                        <?php if (($image->num_rows) > 0) :
                            $image->bind_result($image_id, $image_title, $image_description, $image_filename, $image_upload_date, $image_placement);
                            $image->fetch();
                            $upload_date = strtotime($image_upload_date);
                        ?>
                            <div class="std-card">
                                <h2 class="my-2"><?= $image_title; ?></h2>
                                <img src="./assets/img/news/<?= $image_filename ?>" alt="">
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



            </div>
        <?php endif; ?>

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
    <script>
        $(".nav-btn").click(function() {
            $(".nav-bar").fadeToggle(500);
        });
        $(".btn-close").click(function() {
            $(".nav-bar").fadeOut(500);
        })
    </script>
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
                    window.location.replace('image.php?action=view&image_id=' + image_id);
                }
            });

        });
    </script>
</body>

</html>