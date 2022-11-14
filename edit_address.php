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


//find business address details.
$business = $db->prepare('SELECT * FROM addresses WHERE address_id =' . $address_id);

$business->execute();
$business->store_result();
$business->bind_result($address_id, $address_line_1, $address_line_2, $address_line_3, $address_county, $address_pc);
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
<title>Mi-Admin | Address Settings</title>
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


            <div class="breadcrumbs mb-2"><a href="index.php" class="breadcrumb">Home</a> / <a href="settings.php" class="breadcrumb">Settings</a> / Edit Address</div>
            <div class="main-cards">

                <h1>Address</h1>
                <?php
                if ($user_type == "Admin") :

                ?>
                    <div class="std-card">
                        <h2>Primary Business Address</h2>
                        <p>Make sure this is up to date, this address is displayed on your contact page.</p>
                        <div id="curaddress">

                        </div>
                        <button class="btn-primary my-2" id="modal-active-btn">Edit Address</button>
                    </div>


                <?php else : ?>
                    <p class="font-emphasis">You do not have the necessary Administrator rights to view this page.</p>
                <?php endif; ?>
            </div>

        </section>


    </main>
    <div class="modal">
        <div class="modal-body">
            <div class="modal-close">
                <button type="button" class="btn-close" id="modal-btn-close" aria-label="Close"></button>
            </div>
            <h2>Edit Address</h2>
            <form class="form-card" id="edit_address" action="scripts/load_addresses-script.php" method="post">
                <div class="form-input-wrapper">
                    <label for="address_line_1">Address Line 1</label>
                    <!-- input -->
                    <input class="text-input input" type="text" name="address_line_1" id="address_line_1" placeholder="Address_line1" required="" autocomplete="address-line1" value="<?= $address_line_1; ?>">
                </div>
                <div class="form-input-wrapper">
                    <label for="address_line_2">Address Line 2</label>
                    <!-- input -->
                    <input type="text" name="address_line_2" id="address_line_2" placeholder="Address Line 2" required="" autocomplete="address-line2" value="<?= $address_line_2; ?>">
                </div>
                <div class="form-input-wrapper">
                    <label for="address_line_3">Town or City</label>
                    <!-- input -->
                    <input type="text" name="address_line_3" id="address_line_3" placeholder="Town or City" autocomplete="address-line3" required="" maxlength="45" value="<?= $address_line_3; ?>">
                </div>
                <div class="form-input-wrapper">
                    <label for="address_county">County</label>
                    <!-- input -->
                    <input type="text" name="address_county" id="address_county" placeholder="County" autocomplete="address-level1" required="" value="<?= $address_county; ?>">
                </div>
                <div class="form-input-wrapper">
                    <label for="address_pc">Postal Code</label>
                    <!-- input -->
                    <input type="text" name="address_pc" id="address_pc" placeholder="Postal Code" autocomplete="postal-code" required="" value="<?= $address_pc; ?>">
                </div>

                <div class="button-section my-3">
                    <button class="btn-primary form-controls-btn" type="submit">Save Changes <img src="./assets/img/icons/floppy-disk.svg" alt=""></button>
                
                </div>
                <div id="response" class="d-none">
                </div>
            </form>
        </div>
    </div>
    <!-- /Main Body Of Page -->
    <!-- Quote request form script -->

    <!-- /Quote request form script -->
    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->
    <script>
        $("#nav-btn-close").click(function() {
            $(".nav-bar").fadein(500);
        });

        $("#nav-btn-close").click(function() {
            $(".nav-bar").fadeOut(500);
        })
    </script>
    <script>
        $("#modal-active-btn").click(function() {
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
            address_id = '<?php echo $address_id; ?>';
            url = "scripts/load_addresses-script.php?action=loadaddress&address_id=" + address_id
            $.ajax({ //load current address
                type: "GET",
                url: url,
                encode: true,
                success: function(data, responseText) {
                    $("#curaddress").html(data);
                    
                }
            });
        })
        //script for requesting password reset
        $("#edit_address").submit(function(event) {
            event.preventDefault();
            //declare form variables and collect GET request information
            address_id = '<?php echo $address_id; ?>';
            //collect form data and GET request information to pass to back end script
            var formdata = {
                address_id,
                address_line_1: $("#address_line_1").val(),
                address_line_2: $("#address_line_2").val(),
                address_line_3: $("#address_line_3").val(),
                address_line_3: $("#address_line_3").val(),
                address_line_3: $("#address_line_3").val(),
                address_county: $("#address_county").val(),
                address_pc: $("#address_pc").val(),
            }
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/load_addresses-script.php",
                data: formdata,
                encode: true,
                success: function(data, responseText) {
                    $("#curaddress").html(data);
                    $(".modal").removeClass("modal-active");
                }
            });
        });
    </script>

</body>

</html>