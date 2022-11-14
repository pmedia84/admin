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
$business = $db->prepare('SELECT business_name, address_id FROM business WHERE business_id =' . $business_id);

$business->execute();
$business->store_result();
$business->bind_result($business_name, $address_id);
$business->fetch();
$business->close();

//Find Social Media Types
$social_types_query = "SELECT * FROM business_socials_types";
$social_types = mysqli_query($db, $social_types_query);

?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Social Media Settings</title>
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


            <div class="breadcrumbs mb-2"><a href="index.php" class="breadcrumb">Home</a> / <a href="settings.php" class="breadcrumb">Settings</a> / Social Media Profiles</div>
            <div class="main-cards">

                <h1>Social Media Profiles</h1>
                <p>This information will be displayed on your contact page and on your footer.</p>
                <?php
                if ($user_type == "Admin") :

                ?>


                <div id="socials">

                </div>


                    
                    <button class="btn-primary" id="add_social">Add A Social Media Profile</button>
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
            <h2>Add Social Media Profile</h2>
            <form class="form-card" id="add_social_media_profile" action="scripts/edit_socialmedia-script.php" method="POST">
            <div class="form-input-wrapper my-2">
                                <label for="user_email">Social Media Platform</label>
                                <!-- input -->
                                <select class="form-select" aria-label="Social Platform" name="socials_type_id" id="socials_type_id" required>
                                    <option value="" selected>Select a Platform</option>
                                    <?php foreach ($social_types as $types) : ?>

                                        <option value="<?= $types['socials_type_id']; ?>"><?= $types['socials_type_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>    
            
            <div class="form-input-wrapper">
                    <label for="business_socials_url">URL</label>
                    <!-- input -->
                    <input class="text-input input" type="text" name="business_socials_url" id="business_socials_url" placeholder="URL" required >
                </div>


                <div class="button-section my-3">
                    <button class="btn-primary form-controls-btn" type="submit">Add Profile <img src="./assets/img/icons/floppy-disk.svg" alt=""></button>
                
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
            $(".nav-bar").fadein(500);
        });

        $("#nav-btn-close").click(function() {
            $(".nav-bar").fadeOut(500);
        })
    </script>
    <script>
        $("#add_social").click(function(event) {
            event.preventDefault();
            $(".modal").addClass("modal-active");
        })

        //close modal when close button is clicked
        $("#modal-btn-close").click(function() {
            $(".modal").removeClass("modal-active");

        })
        //close modal when confirm button is clicked
        $(".btn-confirm").on("click", function() {
            $(".modal").removeClass("modal-active");
        })
    </script>

    <script>
        $(document).ready(function() {
            business_id = '<?php echo $business_id; ?>';
            url = "scripts/edit_socialmedia-script.php?action=load&business_id="+business_id;
            $.ajax({ //load current address
                type: "GET",
                url: url,
                encode: true,
                success: function(data, responseText) {
                    $("#socials").html(data);

                }
            });
        })
        //script for adding a new social profile reset
        $("#add_social_media_profile").submit(function(event) {
            event.preventDefault();
            //declare form variables and collect GET request information
            business_id = '<?php echo $business_id; ?>';
            var formdata = {
                business_id,
                socials_type_id: $("#socials_type_id").val(),
                business_socials_url: $("#business_socials_url").val(),
                action: "addnew",

            }
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/edit_socialmedia-script.php",
                data: formdata,
                encode: true,
                success: function(data, responseText) {
                    $(".modal").removeClass("modal-active");
                    $("#add_social_media_profile")[0].reset();
                    
                }
            });
            url = "scripts/edit_socialmedia-script.php?action=load&business_id="+business_id;
            $.ajax({ //load current address
                type: "GET",
                url: url,
                encode: true,
                success: function(data, responseText) {
                    $("#socials").html(data);

                }
            });
        });
    </script>

</body>

</html>