<?php
session_start();
if (!$_SESSION['loggedin'] == true) {
    // Redirect to the login page:
    header('Location: login.php');
}
include("./inc/header.inc.php");
include("./connect.php");


if (isset($_GET['business_socials_id'])) { //only run the query if the get request has been submitted correctly
    //find social media information
    $socials_query = "SELECT * FROM business_socials";
    $socials = mysqli_query($db, $socials_query);
    ///declare the variables
    $business_socials_id = $_GET['business_socials_id'];
    //find social media details for the selected profile.
    $social_profile = $db->prepare('SELECT business_socials.business_socials_id, business_socials.socials_type_id, business_socials.business_socials_url, business_socials.business_id, business_socials_types.socials_type_id, business_socials_types.socials_type_name   FROM business_socials  NATURAL LEFT JOIN business_socials_types WHERE  business_socials.business_socials_id =' . $business_socials_id);
    $social_profile->execute();
    $social_profile->store_result();
}


?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Edit your </title>
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
                <a href="settings.php" class="breadcrumb">Settings</a> /
                <a href="edit_socialmedia.php" class="breadcrumb">Social Media Profiles</a> /
                Edit Social Media Profile
            </div>
            <div class="main-cards">
                <h1>Edit Your Social Media Profile</h1>
                <p>This information will be displayed on your contact page and on your footer.</p>
                <?php if ($user_type == "Admin") : ?>
                    <?php if ($_GET['action'] == "edit") : ?>
                        <?php if (($social_profile->num_rows) > 0) :
                            $social_profile->bind_result($business_socials_id, $socials_type_id, $business_socials_url, $Business_id, $socials_type_id, $socials_type_name);
                            $social_profile->fetch();
                        ?>
                            <div class="std-card user-card" id="social-profile">
                                <h2><?= $socials_type_name; ?></h2>
                                <form class="form-card" id="edit_socials" action="scripts/edit_socialmedia-script.php" method="POST">
                                    <div class="form-input-wrapper my-2">
                                        <label for="socials_type_id">Social Media Platform</label>
                                        <!-- input -->
                                        <select class="form-select" aria-label="Social Platform" name="socials_type_id" id="socials_type_id" required>
                                            <option value="<?= $socials_type_id; ?>" selected><?= $socials_type_name; ?></option>
                                        </select>
                                    </div>
                                    <div class="form-input-wrapper">
                                        <label for="business_socials_url">URL</label>
                                        <!-- input -->
                                        <textarea class="text-input input" type="text" name="business_socials_url" id="business_socials_url" placeholder="URL" required><?= $business_socials_url; ?></textarea>
                                    </div>


                                    <div class="button-section my-3">
                                        <button class="btn-primary form-controls-btn" type="submit">Save Profile <img src="./assets/img/icons/floppy-disk.svg" alt=""></button>
                                        <button class="btn-primary btn-secondary form-controls-btn btn-delete" id="delete-profile" type="button">Delete Profile <img src="./assets/img/icons/delete.svg" alt="" ></button>

                                    </div>
                                    <div id="edit-response" class="d-none">

                                    </div>
                                </form>
                            </div>
                        <?php else : ?>
                            <div class="std-card">
                                <h2>Error</h2>
                                <p>There has been an error, please return to the last page and try again.</p>
                            </div>
                        <?php endif; ?>

                    <?php else : ?>
                        <div class="std-card">
                            <h2>Error</h2>
                            <p>There has been an error, please return to the last page and try again.</p>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <p class="font-emphasis">You do not have the necessary Administrator rights to view this page.</p>
                <?php endif; ?>
            </div>

        </section>
        <div class="modal">
            <div class="modal-body">
                <div class="modal-close">
                    <button type="button" class="btn-close" id="modal-btn-close" aria-label="Close"></button>
                </div>
                <h2>Delete Your Social Media Profile: <?= $socials_type_name; ?></h2>
                <p class="font-emphasis">Are you sure you want to delete this profile? This can't be reversed once done. </p>
                <div class="btn-wrapper my-3">
                    <div class="button-section " id="delete-modal-btns">
                        <button class="btn-primary form-controls-btn" id="delete-submit">Delete Profile <img src="./assets/img/icons/delete.svg" alt=""></button>
                        <button class="btn-primary btn-secondary form-controls-btn" id="delete-cancel">Cancel <img src="./assets/img/icons/cancel.svg" alt=""></button>
                    </div>
                </div>
                <div id="response" class="d-none">
                </div>
                </form>
            </div>
        </div>

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
    </script>
    <script>
        $("#nav-btn-close").click(function() {
            $(".nav-bar").fadeIn(500);
        });

        $("#nav-btn-close").click(function() {
            $(".nav-bar").fadeOut(500);
        })
    </script>


    <script>
        //script for editing profile
        $("#edit_socials").submit(function(event) {
            event.preventDefault();
            //declare form variables and collect POST request information
            business_id = '<?php echo $business_id; ?>';
            business_socials_id = '<?php echo $business_socials_id; ?>';
            var formdata = {
                business_id,
                business_socials_id,
                socials_type_id: $("#socials_type_id").val(),
                business_socials_url: $("#business_socials_url").val(),
                action: "update"

            }
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/edit_socialmedia-script.php",
                data: formdata,
                encode: true,
                success: function(data, responseText) {
                    $("#edit-response").html(data);
                    $("#edit-response").slideDown(400);
                }
            });
        });

        $("#delete-profile").click(function() {
            $(".modal").addClass("modal-active");
        })

        //close modal when close button is clicked
        $("#modal-btn-close").click(function() {
            $(".modal").removeClass("modal-active");

        })
        //close modal when close button is clicked
        $("#delete-cancel").click(function() {
            $(".modal").removeClass("modal-active");

        })
    </script>
    <script>
        $("#delete-submit").click(function() {
            business_socials_id = '<?php echo $business_socials_id; ?>';
            var submit = {
                business_socials_id,
                action: "delete"
            }
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/edit_socialmedia-script.php",
                data: submit,
                encode: true,
                success: function(data, responseText) {
                    $("#social-profile").fadeOut(400);
                    $("#response").html(data);
                    $("#response").slideDown(400);
                    $("#delete-modal-btns").fadeOut(400);
                }
            });
        })
    </script>
</body>

</html>