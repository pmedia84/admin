<?php
session_start();
if (!$_SESSION['loggedin'] == true) {
    // Redirect to the login page:
    header('Location: login.php');
}
include("./inc/header.inc.php");
include("./connect.php");
////////////////Find details of the cms being used, on every page\\\\\\\\\\\\\\\
//Variable for name of CMS
//wedding is the name of people
//business name
$cms_name ="";
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
    $business_users = $db->prepare('SELECT users.user_id, users.user_name, business_users.business_id, business_users.user_type FROM users NATURAL LEFT JOIN business_users WHERE users.user_id='.$user_id);

    $business_users->execute();
    $business_users->bind_result($user_id, $user_name,$business_id, $user_type);
    $business_users->fetch();
    $business_users->close();
}

//run checks to make sure a wedding has been set up correctly
if ($cms_type == "Wedding") {

    //look for a wedding setup in the db, if not then direct to the setup page
    $wedding_query = ('SELECT wedding_id, wedding_name FROM wedding');
    $wedding = $db->query($wedding_query);
    $wedding_details = mysqli_fetch_assoc($wedding);
    if ($wedding->num_rows == 0) {
        header('Location: setup.php?action=setup_wedding');
    }
    //check that there are users set up 
    $wedding_user_query = ('SELECT wedding_user_id FROM wedding_users');
    $wedding_user = $db->query($wedding_user_query);
    if ($wedding_user->num_rows == 0) {
        header('Location: setup.php?action=check_users_wedding');
    }

    if (!$_SESSION['loggedin'] == true) {
        // Redirect to the login page:
        header('Location: login.php');
    }
}
// //find users and display on screen.
// //connect to user db to check admin rights etc
// //find username and email address to display on screen.
// $user = $db->prepare('SELECT user_id,  user_type, business_id FROM users WHERE user_id = ?');
// $user->bind_param('s', $_SESSION['user_id']);
// $user->execute();
// $user->store_result();
// $user->bind_result($user_id, $user_type, $business_id);
// $user->fetch();
// $user->close();
//find news articles
$news_query = ('SELECT * FROM news_articles ORDER BY news_articles_status ');
$news = $db->query($news_query);

//////////////////////////////////////////////////////////////////Everything above this applies to each page\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\


?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | News</title>
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
                        <h2><?= $cms_name;?></h2>
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
                <a href="index.php" class="breadcrumb">Home</a> / Manage News Articles
            </div>
            <div class="main-cards">
            <?php if($module_news == "On"):?>

                <h1>News Articles</h1>
                <a class="btn-primary" id="add_social" href="news_createarticle.php">Create An Article <i class="fa-solid fa-plus"></i></a>
                <?php if ($user_type == "Admin") : ?>

                    <div class="news-grid">
                        <?php foreach ($news as $article) :
                            $news_article_body = html_entity_decode($article['news_articles_body']);
                            $news_articles_date = strtotime($article['news_articles_date']);

                            if ($article['news_articles_status'] == "Published") {
                                $news_articles_status = "<p class='news-item-status published'>Published <i class='fa-solid fa-check'></i></p>";
                            }
                            if ($article['news_articles_status'] == "Draft") {
                                $news_articles_status = "<p class='news-item-status draft'>Draft <i class='fa-solid fa-flag'></i></p>";
                            }
                        ?>
                            <div class="news-card">
                                <div class="news-card-header">
                                    <h2><?= $article['news_articles_title']; ?></h2>
                                    <span class="news-create-status">
                                        <?= $news_articles_status; ?>
                                    </span>
                                </div>
                                <?php if ($article['news_articles_img'] == null) : ?>
                                    <img src="./assets/img/news/news-item.jpg" alt="">
                                <?php else : ?>
                                    <img src="./assets/img/news/<?= $article['news_articles_img']; ?>" alt="">
                                <?php endif; ?>
                                <p class="news-create-date my-2"><?= date('d-m-y', $news_articles_date); ?></p>
                                <div class="news-card-body my-2">
                                    <p><?= $news_article_body; ?></p>
                                </div>


                                <div class="news-card-actions">
                                    <a class="my-2" href="news_article.php?action=view&news_articles_id=<?= $article['news_articles_id']; ?>"><i class="fa-solid fa-eye"></i> View Article</a>
                                    <a class="my-2" href="news_article.php?action=edit&news_articles_id=<?= $article['news_articles_id']; ?>"><i class="fa-solid fa-pen-to-square"></i> Edit Article </a>
                                </div>
                            </div>
                        <?php endforeach; ?>


                    </div>
 
                <?php else : ?>
                    <p class="font-emphasis">You do not have the necessary Administrator rights to view this page.</p>
                <?php endif; ?>

            <?php else :?>
                <h1>Module not activated for your website!</h1>
                <p>Contact us to find out how you can get this feature set up.</p>
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

</body>

</html>