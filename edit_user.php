<?php
session_start();
if (!$_SESSION['loggedin'] == true) {
    // Redirect to the login page:
    header('Location: login.php');
}
include("./inc/header.inc.php");
include("./connect.php");
//find user admin rights.
$user = $db->prepare('SELECT user_id,  user_type FROM users WHERE user_id = ?');
$user->bind_param('s', $_SESSION['user_id']);
$user->execute();
$user->store_result();
$user->bind_result($user_id, $user_type);
$user->fetch();
$user->close();

?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Users</title>
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
                        <h2>Lashes Brows & Aesthetics</h2>
                    </div>

                    <a class="header-actions-btn-logout" href="logout.php"><span>Logout</span><img src="assets/img/icons/logout.svg" alt=""></a>
                </div>
            </div>
        </div>
        <!-- Nav Bar -->
        <?php include("./inc/nav.inc.php"); ?>
        <!-- /nav bar -->
        <section class="body">
            <div class="breadcrumbs"><a href="index.php" class="breadcrumb">Home</a> / <a href="users.php">Users</a> / Edit User</div>
            <div class="grid-col">
                <?php
                if ($user_type == "Admin") :
                    if (empty($_GET['user_id'])) : ?>
                        <h1 class="text-center">Edit User </h1>
                        <div class="std-card user-card">
                            <p class="font-emphasis">No user found, please return to the users page and try again</p>
                            <a href="users.php">Users</a>
                        </div>
                    <?php else :
                        //find users and display on screen.
                        $user = $db->prepare('SELECT user_id, user_email, user_name, user_type FROM users WHERE user_id = ?');
                        $user->bind_param('s', $_GET['user_id']);
                        $user->execute();
                        $user->store_result();
                        $user->bind_result($user_id, $email, $name, $user_type);
                        $user->fetch();
                        $user->close();
                    ?>
                        <h1 class="text-center">Edit User: <?= $name; ?></h1>
                        <div class="std-card user-card">
                            <form class="form-card" id="edit_user" action="scripts/auth.php" method="post">
                                <div class="form-input-wrapper">
                                    <label for="password">Name:</label>
                                    <!-- input -->
                                    <input class="text-input input" type="text" name="username" id="username" placeholder="Username" required="" maxlength="45" value="<?= $name; ?>">
                                </div>
                                <div class="form-input-wrapper">
                                    <label for="user_email">eMail Address:</label>
                                    <!-- input -->
                                    <input type="text" name="user_email" id="user_email" placeholder="Email Address" autocomplete="email" required="" maxlength="45" value="<?= $email; ?>">
                                </div>
                                <div class="form-input-wrapper">
                                    <label for="user_email">Access Level</label>
                                    <!-- input -->
                                    <select class="form-select" aria-label="Message regarding" name="msgtype" id="msgtype">
                                                    <option value="<?=$user_type;?>" selected><?=$user_type;?></option>
                                                    <option value="Admin" selected>Admin</option>
                                                    <option value="Editor" selected>Editor</option>
                                                </select>
                                </div>
                                <p>Need to change your password?</p>
                                <p>You can do that here: <a href="resetpw.php">Reset Password</a></p>
                                <div class="button-section my-3">
                                    <button class="btn-primary" type="submit">Save Changes</button>
                                    <a href="users.php" class="btn-primary btn-secondary">Cancel Changes</a>
                                </div>
                                <div id="response" class="d-none">
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php else:?>
                    <h1 class="text-center">Edit User </h1>
                    <p class="font-emphasis text-center">You do not have the necessary Administrator rights to view this page.</p>
                <?php endif;
                $db->close(); ?>
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
        //script for requesting password reset
        $("#edit_user").submit(function(event) {
            event.preventDefault();
            //declare form variables and collect GET request information
            user_id = '<?php echo $user_id; ?>';
            //collect form data and GET request information to pass to back end script
            var formdata = {
                user_email: $("#user_email").val(),
                user_id,
                user_name: $("#username").val(),
            }
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/edit_user-script.php",
                data: formdata,
                encode: true,
                success: function(data, responseText) {
                    $("#response").html(data);
                    $("#response").slideDown(400);
                    if (data === 'success') {
                        window.location.replace('users.php');
                    }

                }
            });
        });
    </script>
</body>

</html>