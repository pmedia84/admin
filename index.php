<?php
session_start();
require("scripts/functions.php");
check_login();
include("connect.php");
include("inc/settings.php");
//find news articles
$news_query = ('SELECT * FROM news_articles WHERE news_articles_status="Published" ORDER BY news_articles_date LIMIT 3 ');
$news = $db->query($news_query);
$num_articles = $news->num_rows;
//find the amount of articles listed
$article_num = ('SELECT news_articles_id FROM news_articles  ');
$article_num = $db->query($article_num);
$article_amt = $article_num->num_rows;
//find the amount of articles listed
$services = ('SELECT service_id FROM services  ');
$services = $db->query($services);
$services_amt = $services->num_rows;
//page meta variables
$meta_description = "Parrot Media - Client Admin Area";
$meta_page_title = "Mi-Admin | Dashboard";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include("./inc/Page_meta.php");?>
</head>

<body>
    <!-- Main Body Of Page -->
    <main class="main">
        <!-- Header Section -->
        <?php include("inc/header.inc.php"); ?>
        <!-- Nav Bar -->
        <?php include("./inc/nav.inc.php"); ?>
        <!-- /nav bar -->
        <section class="body">
            <div class="breadcrumbs"><span><svg class="icon feather-icon"><use xlink:href="assets/img/icons/feather.svg#home"></use></svg> Home / </span></div>
            <div class="main-dashboard">
                <?php if ($news_m->status() == "On") : ?>
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <span><?= $article_amt; ?></span>
                            <svg class="icon"><use xlink:href="assets/img/icons/solid.svg#newspaper"></use></svg>
                        </div>
                        <h2>News Posts</h2>
                        <a href="news.php">Manage</a>
                    </div>
                <?php endif; ?>
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <span><?= $services_amt; ?></span>
                        <svg class="icon"><use xlink:href="assets/img/icons/solid.svg#tags"></use></svg>
                    </div>
                    <h2>Services</h2>
                    <a href="price_list.php">Manage</a>
                </div>

                
            </div>


        </section>
        <?php if ($news_m->status() == "On") : ?>
            <div class="main-cards sidebar">
                <h2>Published Posts</h2>
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
                    <div class="news-card news-card-dashboard">
                        <?php if ($article['news_articles_img'] == null) : ?>
                            <a href="news_article.php?action=view&news_articles_id=<?= $article['news_articles_id']; ?>"><img src="./assets/img/news/news-item.webp" alt=""></a>
                        <?php else : ?>
                            <a href="news_article.php?action=view&news_articles_id=<?= $article['news_articles_id']; ?>"><img src="./assets/img/news/<?= $article['news_articles_img']; ?>" alt=""></a>
                        <?php endif; ?>
                        <p class="news-create-date my-2"><?= date('d-M-y', $news_articles_date); ?></p>
                        <h3><a href="news_article.php?action=view&news_articles_id=<?= $article['news_articles_id']; ?>"><?= $article['news_articles_title']; ?></a></h3>
                        <div class="news-card-body">
                            <p><?= $news_article_body; ?></p>
                        </div>
                        <div class="card-actions"><a href="news_article.php?action=view&news_articles_id=<?= $article['news_articles_id']; ?>"><i class="fa-solid fa-eye"></i> View Article</a></div>
                    </div>

                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Footer -->
        <?php include("./inc/footer.inc.php"); ?>
        <!-- /Footer -->
    </main>
</body>

</html>