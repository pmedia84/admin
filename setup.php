<?php
session_start();
if (empty($_GET)) {
    //if user arrives at this page without a get request, redirect to the index page
    header('location: index.php');
}
include("connect.php");
require("scripts/functions.php");
$cms = new Cms();

//page meta variables
$meta_description = "Parrot Media - Client Admin Area";
$meta_page_title = "Mi-Admin | Setup";

?>

<head>
    <?php include("./inc/Page_meta.php"); ?>
</head>

<body>
    <main class="login">

        <div class="login-wrapper">
            <img src="assets/img/logo.png" alt="">
            <?php if ($cms->type() == "Business") : ?>
                <?php if ($_GET['action'] == "setup_business") : 
                     $business_query = ('SELECT business_id FROM business');
                     $business = $db->query($business_query);
                    ?>
                    <?php if ($business->num_rows == 0) : ?>
                        <h1>Setup Business</h1>
                        <p>You need to set up a business first.</p>
                        <p><strong>Note:</strong> This can only be done by a developer. Contact us if you are not a developer.</p><br>
                        <form class="form-card" id="setup-business" action="scripts/setup.php" method="post">
                            <div class="form-input-wrapper">
                                <h2>Business Name</h2>
                                <label for="business_name">Business Name</label>
                                <!-- input -->
                                <input type="text" name="business_name" id="business_name" placeholder="Enter Business Name" required="" maxlength="45">
                            </div>
                            <h2>Address</h2>
                            <div class="form-input-wrapper">
                                <label for="address_line_1">Address Line 1</label>
                                <!-- input -->
                                <input class="text-input input" type="text" name="address_line_1" id="address_line_1" placeholder="Address Line1" required="" autocomplete="address-line1">
                            </div>
                            <div class="form-input-wrapper">
                                <label for="address_line_2">Address Line 2</label>
                                <!-- input -->
                                <input type="text" name="address_line_2" id="address_line_2" placeholder="Address Line 2" autocomplete="address-line2">
                            </div>
                            <div class="form-input-wrapper">
                                <label for="address_line_3">Town or City</label>
                                <!-- input -->
                                <input type="text" name="address_line_3" id="address_line_3" placeholder="Town or City" autocomplete="address-line3" required="" maxlength="45">
                            </div>
                            <div class="form-input-wrapper">
                                <label for="address_county">County</label>
                                <!-- input -->
                                <input type="text" name="address_county" id="address_county" placeholder="County" autocomplete="address-level1" required="">
                            </div>
                            <div class="form-input-wrapper">
                                <label for="address_pc">Postal Code</label>
                                <!-- input -->
                                <input type="text" name="address_pc" id="address_pc" placeholder="Postal Code" autocomplete="postal-code" required="">
                            </div>
                            <h2>Business Contact Details</h2>
                            <div class="form-input-wrapper">
                                <label for="business_email">Business eMail Address:</label>

                                <!-- input -->
                                <input type="email" name="business_email" id="business_email" placeholder="Email Address" autocomplete="email" required="" maxlength="45">
                            </div>
                            <div class="form-input-wrapper">
                                <label for="business_phone">Business Primary Phone No.:</label>
                                <!-- input -->
                                <input type="text" name="business_phone" id="business_phone" placeholder="Business Phone No." autocomplete="tel" required="" maxlength="45">
                            </div>
                            <div class="form-input-wrapper">
                                <label for="business_contact_name">Primary Contact Name:</label>
                                <!-- input -->
                                <input type="text" name="business_contact_name" id="business_contact_name" placeholder="Business Contact Name" autocomplete="given-name" required="" maxlength="45">
                            </div>
                            <div class="button-section my-3">
                                <button class="btn-primary form-controls-btn loading-btn" type="submit">Set Up Business <img id="loading-icon" class="loading-icon d-none" src="./assets/img/icons/loading.svg" alt=""></button>
                            </div>
                            <div id="response" class="d-none">
                            </div>
                        </form>
                    <?php else : ?>
                        <h1>Setup Business</h1>
                        <p><strong>Business already setup!</strong></p>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($_GET['action'] == "check_users_business") :

                    //check that there are users set up 
                    $business_user_query = ('SELECT * FROM business_users');
                    $business_user = $db->query($business_user_query); ?>
                    <?php if ($business_user->num_rows < 2) : ?>
                        <?php
                        //display business name
                        $business_query = ('SELECT business_id, business_name FROM business ORDER BY business_id LIMIT 1');
                        $business = $db->query($business_query);
                        $business_result = $business->fetch_array();
                        ?>
                        <?php
                        //find an admin for this business
                        $admin_user_query = ('SELECT user_type, business_id FROM business_users WHERE business_id=' . $cms->b_id() . ' AND user_type = "Admin" ');

                        if ($admin_user = $db->query($admin_user_query)) {
                            $admin_user_result = $admin_user->fetch_assoc();
                        } else {
                            $admin_user_result = "";
                        }


                        //find a Developer for this business
                        $dev_user_query = ('SELECT user_type, business_id FROM business_users WHERE business_id=' . $cms->b_id() . ' AND user_type = "Developer" ');

                        if ($dev_user = $db->query($dev_user_query)) {
                            $dev_user_result = $dev_user->fetch_assoc();
                        } else {
                            $dev_user_result = "";
                        }

                        ?>
                        <?php if ($admin_user_result == NULL) : ?>
                            <h1><?= $business_result['business_name']; ?></h1>
                            <p><strong>Admin User Required</strong></p>
                            <p>You need to set up an admin user for this business.</p>
                            <p><strong>Note:</strong> This can only be done by a developer. Contact us if you are not a developer.</p>
                            <p>Two user types are required: Admin and Developer</p><br>
                            <h2>Setup Admin User</h2>
                            <form class="form-card" id="add_user" action="scripts/setup.script.php" method="post" data-business_id="<?=$cms->b_id();?>">
                                <div class="form-input-wrapper">
                                    <label for="user_name">Name:</label>
                                    <!-- input -->
                                    <input class="text-input input" type="text" name="username" id="username" placeholder="Name" required="" maxlength="45">
                                </div>
                                <div class="form-input-wrapper">
                                    <label for="user_email">eMail Address:</label>
                                    <!-- input -->
                                    <input type="text" name="user_email" id="user_email" placeholder="Email Address" autocomplete="email" required="" maxlength="45">
                                </div>
                                <div class="form-input-wrapper">
                                    <label for="user_email">Access Level</label>
                                    <p><strong>Admin:</strong> For Clients login</p>
                                    <!-- input -->
                                    <select class="form-select" name="user_type" id="user_type">
                                        <option value="Admin">Admin</option>
                                    </select>
                                    <p>A password will be randomly generated and emailed to the user to make their own password.</p>
                                </div>
                                <div class="button-section my-3">
                                    <button class="btn-primary form-controls-btn loading-btn" type="submit">Add User <img id="loading-icon" class="loading-icon d-none" src="./assets/img/icons/loading.svg" alt=""></button>
                                </div>
                                <div id="response" class="d-none">
                                </div>
                            </form>
                        <?php else : ?>
                            <?php if ($dev_user_result == null) : ?>
                                <h1><?= $business_result['business_name']; ?></h1>
                                <p><strong>Developer User Required</strong></p>
                                <p>You need to set up a Developer user for this business.</p>
                                <p><strong>Note:</strong> This can only be done by a developer. Contact us if you are not a developer.</p>
                                <p>Two user types are required: Admin and Developer</p><br>
                                <h2>Setup Developer User</h2>
                                <form class="form-card" id="add_user" action="scripts/setup.script.php" method="post" data-business_id="<?=$cms->b_id();?>">
                                    <div class="form-input-wrapper">
                                        <label for="user_name">Name:</label>
                                        <!-- input -->
                                        <input class="text-input input" type="text" name="username" id="username" placeholder="Name" required="" maxlength="45">
                                    </div>
                                    <div class="form-input-wrapper">
                                        <label for="user_email">eMail Address:</label>
                                        <!-- input -->
                                        <input type="text" name="user_email" id="user_email" placeholder="Email Address" autocomplete="email" required="" maxlength="45">
                                    </div>
                                    <div class="form-input-wrapper">
                                        <label for="user_email">Access Level</label>
                                        <p><strong>Developer:</strong> For Developer to setup business and provide tech support.</p>
                                        <!-- input -->
                                        <select class="form-select" name="user_type" id="user_type">
                                            <option value="Developer">Developer</option>
                                        </select>
                                        <p>A password will be randomly generated and emailed to the user to make their own password.</p>
                                    </div>
                                    <div class="button-section my-3">
                                        <button class="btn-primary form-controls-btn loading-btn" type="submit">Add User <img id="loading-icon d-none" class="loading-icon" src="./assets/img/icons/loading.svg" alt=""></button>
                                    </div>
                                    <div id="response" class="d-none">
                                    </div>
                                </form>

                            <?php else : ?>
                                <script>
                                    window.location.replace("login.php");
                                </script>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>

            <?php endif; ?>
            






    </main>
    <!-- /Footer -->
    <?php if ($cms->type() == "Business") : ?>
        <script>
            //script for saving new business details then redirects to set up users
            $("#setup-business").submit(function(event) {
                event.preventDefault();
                var formData = new FormData($("#setup-business").get(0));
                var url = "setup.php?action=check_users_business"
                formData.append("action", "create_business");

                $.ajax({ //start ajax post
                    type: "POST",
                    url: "scripts/setup.script.php",
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

                        window.location.replace(url);



                    }
                });

            });
        </script>
        <script>
            //script for adding new users
            $("#add_user").submit(function(event) {
                event.preventDefault();
                var formData = new FormData($("#add_user").get(0));
                var business_id = $(this).data("business_id");
                var url = "setup.php?action=check_users_business"
                formData.append("action", "create_user_business");
                formData.append("business_id", business_id);

                $.ajax({ //start ajax post
                    type: "POST",
                    url: "scripts/setup.script.php",
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
                        if (data == "User already exists with that email address") {
                            $("#response").addClass("form-response error");
                            $("#response").html(data);
                            $("#response").slideDown(400);

                        }
                        window.location.replace(url);
                    }
                });

            });
        </script>
    <?php endif; ?>
    
</body>

</html>