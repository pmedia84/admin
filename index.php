<?php
include("connect.php");
//look for a business setup in the db, if not then direct to the setup page
$business_query = ('SELECT business_id FROM business');
$business = $db->query($business_query);
if($business -> num_rows ==0){
    header('Location: setup.php?action=setup_business');
}
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
//find news articles
$news_query = ('SELECT * FROM news_articles WHERE news_articles_status="Published" ORDER BY news_articles_date LIMIT 3 ');
$news = $db->query($news_query);
$num_articles = $news -> num_rows;
//find the amount of articles listed
$article_num = ('SELECT news_articles_id FROM news_articles  ');
$article_num = $db->query($article_num);
$article_amt = $article_num -> num_rows;
//find the amount of images listed
$image_num = ('SELECT image_id FROM images  ');
$image_num = $db->query($image_num);
$image_amt = $image_num -> num_rows;
//find the amount of users listed
$user_num = ('SELECT user_id FROM users');
$user_num = $db->query($user_num);
$user_amt = $user_num -> num_rows;
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
                    <span><?=$article_amt;?></span>
                    <img src="assets/img/icons/newspaper.svg" alt="">
                </div>
                <h2>News Articles</h2>
                <a href="news.php">Manage</a>
            </div>
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <span><?=$image_amt;?></span>
                    <img src="assets/img/icons/image.svg" alt="">
                </div>
                <h2>Photo Gallery</h2>
                <a href="gallery.php">Manage</a>
            </div>
            <?php if($user_type =="Admin"):?>
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <span><?=$user_amt;?></span>
                    <img src="assets/img/icons/users.svg" alt="">
                </div>
                <h2>Users</h2>
                <a href="users.php">Manage</a>
            </div>
            <?php endif;?>
        </div>

       
        </section>

        <div class="main-cards">
        <h2>Published Articles</h2>
        <?php foreach ($news as $article):
                    $news_article_body= html_entity_decode($article['news_articles_body']);
                    $news_articles_date = strtotime($article['news_articles_date']);
                   
                    if($article['news_articles_status'] == "Published"){
                        $news_articles_status = "<p class='news-item-status published'>Published <i class='fa-solid fa-check'></i></p>";
                    }
                    if($article['news_articles_status'] == "Draft"){
                        $news_articles_status = "<p class='news-item-status draft'>Draft <i class='fa-solid fa-flag'></i></p>";
                    }
                    ?>
            <div class="news-card news-card-dashboard">
                        <?php if($article['news_articles_img']==null):?>
                                <img src="./assets/img/news/news-item.jpg" alt="">
                            <?php else:?>
                                <img src="./assets/img/news/<?=$article['news_articles_img'];?>" alt="">
                        <?php endif;?>
                <p class="news-create-date my-2"><?=date('d-M-y',$news_articles_date);?></p>
                <h3><?=$article['news_articles_title'];?></h3>
                <div class="news-card-body">
                    <p><?=$news_article_body;?></p>
                </div>
                <div class="card-actions"><a href="news_article.php?action=view&news_articles_id=<?=$article['news_articles_id'];?>"><i class="fa-solid fa-eye"></i> View Article</a></div>
            </div>

        <?php endforeach;?>            
            


        </div>


    </main>
    <!-- /Main Body Of Page -->

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