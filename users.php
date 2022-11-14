<?php
session_start();
if (!$_SESSION['loggedin'] == true) {
    // Redirect to the login page:
    header('Location: login.php');
}
include("./inc/header.inc.php");
include("./connect.php");
//find users and display on screen.
$user_query = "SELECT user_id, user_name,user_type, user_email FROM users";
$users = mysqli_query($db, $user_query);
//find username and email address to display on screen.
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
    
    
            <div class="breadcrumbs mb-2"><a href="index.php" class="breadcrumb">Home</a> / Users</div>
            <div class="main-cards">
    
                <h1>Manage Users</h1>
                <?php
                if($user_type =="Admin"):
                foreach ($users as $user):?>
                <div class="std-card">
                    <h2><?=$user['user_name'];?></h2>
                    <p><strong>Email Address:</strong> <?=$user['user_email'];?></p>
                    <p><strong>Access Level: </strong><?=$user['user_type'];?></p>
                    <a href="edit_user.php?user_id=<?=$user['user_id'];?>">Edit User</a>
                </div>
                <?php endforeach;?>
                <?php else:?>
                    <p class="font-emphasis">You do not have the necessary Administrator rights to view this page.</p>
                    <?php endif;?>
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
</body>

</html>