<?php
session_start();
if (!$_SESSION['loggedin'] == true) {
    // Redirect to the login page:
    header('Location: login.php');
}
include("./inc/header.inc.php");
include("connect.php");
//connect to user db to check admin rights etc
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
<title>Mi-Admin | Dashboard</title>
<!-- /Page Title -->
</head>

<body>


    <!-- Main Body Of Page -->
    <main class="main">
   
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
        <div class="breadcrumbs"><span>Home / </span></div>
        <div class="main-dashboard">
        
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <span>25</span>
                    <img src="assets/img/icons/tags.svg" alt="">
                </div>
                <h2>Services</h2>
                <a href="">Manage</a>
            </div>
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <span>50</span>
                    <img src="assets/img/icons/newspaper.svg" alt="">
                </div>
                <h2>News Articles</h2>
                <a href="news.php">Manage</a>
            </div>
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <span>10</span>
                    <img src="assets/img/icons/image.svg" alt="">
                </div>
                <h2>Photo Gallery</h2>
                <a href="">Manage</a>
            </div>
            <?php if($user_type =="Admin"):?>
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <span>2</span>
                    <img src="assets/img/icons/users.svg" alt="">
                </div>
                <h2>Users</h2>
                <a href="users.php">Manage</a>
            </div>
            <?php endif;?>
        </div>

       
        </section>

        <div class="main-cards">
            <h2>Recent Articles</h2>
            <div class="std-card">
                <img src="assets/img/dermal.jpg" alt="">
                <h3>Aesthetics Special Offer</h3>
                <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Omnis, porro?</p>
                <a href="">View</a>
            </div>
            <div class="std-card">
                <img src="assets/img/dermal.jpg" alt="">
                <h3>Aesthetics Special Offer</h3>
                <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Omnis, porro?</p>
                <a href="">View</a>
            </div>
            <div class="std-card">
                <img src="assets/img/dermal.jpg" alt="">
                <h3>Aesthetics Special Offer</h3>
                <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Omnis, porro?</p>
                <a href="">View</a>
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

        $(".btn-close").click(function() {
            $(".nav-bar").fadeOut(500);
        })
    </script>
</body>

</html>