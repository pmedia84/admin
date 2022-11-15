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
$business = $db->prepare('SELECT * FROM business WHERE business_id =' . $business_id);

$business->execute();
$business->store_result();
$business->bind_result($business_id, $business_name, $address_id, $business_phone, $business_email, $business_contact_name);
$business->fetch();
$business->close();
//////////////////////////////////////////////////////////////////Everything above this applies to each page\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$article_id = $_GET['news_articles_id'];

//load news article
$article = $db->prepare('SELECT * FROM news_articles  WHERE news_articles_id=' . $article_id);
$article->execute();
$article->store_result();

//find news articles
$news_query = ('SELECT * FROM news_articles WHERE news_articles_status="Published" ORDER BY news_articles_date LIMIT 3 ');
$news = $db->query($news_query);

?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Create News Article</title>
<!-- /Page Title -->
</head>
<script>
    tinymce.init({
        selector: 'textarea#news_article_body',
        height: 600,

        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinymcespellchecker permanentpen         autocorrect',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat | ',
        tinycomments_mode: 'embedded',

        tinycomments_author: 'Author name',
        mergetags_list: [{
                value: 'First.Name',
                title: 'First Name'
            },
            {
                value: 'Email',
                title: 'Email'
            },
        ]
    });
</script>

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
                <a href="news.php" class="breadcrumb">News</a>
                <?php if ($_GET['action'] == "edit") : ?>
                    / Edit Article
                <?php endif; ?>
                <?php if ($_GET['action'] == "delete") : ?>
                    / Delete Article
                <?php endif; ?>
                <?php if ($_GET['action'] == "view") : ?>
                    / View Article
                <?php endif; ?>
            </div>
            <div class="main-cards">
                <?php if ($_GET['action'] == "edit") : ?>
                    <h1>Edit Article</h1>
                <?php endif; ?>
                <?php if ($_GET['action'] == "view") : ?>
                    <h1>View Article</h1>
                <?php endif; ?>
                <?php if ($_GET['action'] == "delete") : ?>
                    <h1>Delete Article</h1>
                <?php endif; ?>

                <?php if ($_GET['action'] == "edit") : ?>
                    <p class="font-emphasis">This page is best viewed on a large screen</p>
                <?php else:?>
                <?php endif;?>
                <?php if ($user_type == "Admin")://detect if user is an admin or not ?>
                    <?php if($_GET['action'] == "delete"): //if action is delete, detect if the confirm is yes or no?>
                        <?php if($_GET['confirm']=="yes"): //if yes then delete the article?>
                            <?php if (($article->num_rows) > 0) :
                            $article->bind_result($news_articles_id, $news_articles_title, $news_articles_date, $news_articles_body, $news_articles_img, $news_articles_author, $news_articles_status);
                            $article->fetch();
                            $news_articles_body = html_entity_decode($news_articles_body); 
                                    // connect to db and delete the record
                            $delete_article = "DELETE FROM news_articles WHERE news_articles_id=".$news_articles_id;
                            if(mysqli_query($db, $delete_article)){
                                echo'<div class="news-create"><div class="form-response error"><p>'.$news_articles_title.' Has Been Deleted</p></div></div>';
                            }else{
                                 echo'<div class="form-response error"><p>Error deleting article, please try again.</p></div>';
                             }
                            ?>

                            <?php endif;?>
                        <?php else: //if not then display the message to confirm the user wants to delete the news article?>
                            <?php if (($article->num_rows) > 0) :
                            $article->bind_result($news_articles_id, $news_articles_title, $news_articles_date, $news_articles_body, $news_articles_img, $news_articles_author, $news_articles_status);
                            $article->fetch();
                            $news_articles_body = html_entity_decode($news_articles_body); ?>
                            <div class="news-create">
                                <h2 class="text-alert">Delete: <?=$news_articles_title;?></h2>
                                <p>Are you sure you want to delete this article?</p>
                                <p><strong>This Cannot Be Reversed</strong></p>
                                <div class="button-section">
                                    <a class="btn-primary btn-delete my-2" href="news_article.php?action=delete&confirm=yes&news_articles_id=<?=$news_articles_id;?>"><i class="fa-solid fa-trash"></i>Delete Article</a>
                                    <a class="btn-primary btn-secondary my-2" href="news_article.php?action=view&news_articles_id=<?=$news_articles_id;?>"><i class="fa-solid fa-ban"></i>Cancel</a>
                                </div>
                            </div>
                        <?php endif;?>
                        <?php endif;?>    
                        
                        

                    <?php endif;?> 
                    
                    <?php if ($_GET['action'] == "edit") : ?>
                        <?php if (($article->num_rows) > 0) :
                            $article->bind_result($news_articles_id, $news_articles_title, $news_articles_date, $news_articles_body, $news_articles_img, $news_articles_author, $news_articles_status);
                            $article->fetch();
                            $news_articles_body = html_entity_decode($news_articles_body);
                        ?>
                            <div class="news-create">
                                <form class="form-card" id="edit_news_article" action="scripts/news_createarticle-script.php" method="post" enctype="multipart/form-data">
                                    <div class="form-input-wrapper">
                                        <label for="news_article_title">Title</label>
                                        <!-- input -->
                                        <input class="text-input input" type="text" name="news_articles_title" id="news_articles_title" placeholder="Article Title" required="" maxlength="45" value="<?= $news_articles_title; ?>">
                                    </div>
                                    <div class="form-input-wrapper my-2">
                                        <label for="business_email">Header Image</label>
                                        <img src="./assets/img/news/<?= $news_articles_img; ?>" alt="">
                                        <!-- input -->
                                        <p class="form-hint-small">Change the image by uploading a new one here:</p>
                                        <input type="file" name="news_articles_img" id="news_articles_img" accept="image/*">
                                    </div>
                                    <div class="form-input-wrapper my-2">
                                        <label for="news_article_body">Article Body</label>
                                        <textarea id="news_article_body" name="news_article_body">
                                            <?= $news_articles_body;?>
                                        </textarea>
                                    </div>

                                    <div class="form-input-wrapper my-2">
                                        <label for="news_articles_status">Status</label>
                                        <p class="form-hint-small">Set as a draft to come back and finish, or set as published to publish to your website straight away.</p>
                                        <select name="news_articles_status" id="news_articles_status" required="">
                                            <option value="<?= $news_articles_status; ?>" selected><?= $news_articles_status; ?></option>
                                            <option value="Draft">Draft</option>
                                            <option value="Published">Published</option>
                                        </select>
                                    </div>
                                    <div class="button-section my-3">
                                        <button class="btn-primary form-controls-btn" type="submit">Update Article <i class="fa-solid fa-floppy-disk"></i></button>
                                    </div>
                                    <div id="response" class="d-none">
                                        <p>Article Saved <img src="./assets/img/icons/check.svg" alt=""></p>
                                    </div>
                                </form>
                            </div>
                        <?php else : ?>
                            <div class="std-card">
                                <h2>Error</h2>
                                <p>There has been an error, please return to the last page and try again.</p>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($_GET['action'] == "view") : ?>
                        <?php if (($article->num_rows) > 0) :
                            $article->bind_result($news_articles_id, $news_articles_title, $news_articles_date, $news_articles_body, $news_articles_img, $news_articles_author, $news_articles_status);
                            $article->fetch();
                            $news_articles_body = html_entity_decode($news_articles_body);
                            $news_articles_date = date('d-M-y');
                            if ($news_articles_status == "Published") {
                                $news_articles_status = "<p class='news-item-status published'>Published <i class='fa-solid fa-check'></i></p>";
                            }
                            if ($news_articles_status == "Draft") {
                                $news_articles_status = "<p class='news-item-status draft'>Draft <i class='fa-solid fa-flag'></i></p>";
                            } ?>
                            <div class="news-create">
                                <span class="news-create-status">
                                    <?= $news_articles_status; ?>
                                </span>
                                <h2 class="my-2"><?= $news_articles_title; ?></h2>
                                <?php if ($news_articles_img == null) : ?>
                                    <img src="./assets/img/news/news-item.jpg" alt="">
                                <?php else : ?>
                                    <img src="./assets/img/news/<?= $news_articles_img ?>" alt="">
                                <?php endif; ?>
                                <p class="news-create-date my-2"><?= $news_articles_date; ?></p>
                                <div class="news-create-body"><?= $news_articles_body; ?></div>
                                <div class="news-create-actions">
                                    <a class="my-2" href="news_article.php?action=edit&news_articles_id=<?= $news_articles_id; ?>"><i class="fa-solid fa-pen-to-square"></i> Edit Article </a><br>
                                    <a class="my-2" href="news_article.php?action=delete&confirm=no&news_articles_id=<?= $news_articles_id; ?>"><i class="fa-solid fa-trash"></i> Delete Article </a>
                                </div>
                            <?php endif;?>
                            </div>

            </div>



            </div>
        <?php endif; ?>
        <h2>Recent Published Posts</h2>
        <a class="my-2" href="news.php">View All</a>
        <div class="news-grid">
            <?php foreach ($news as $article) :
                        $news_article_body = html_entity_decode($article['news_articles_body']);
                        $news_articles_date = strtotime($article['news_articles_date']);

                        if ($article['news_articles_status'] == "Published") {
                            $news_articles_status = "<p class='news-item-status published'>Published <i class='fa-solid fa-check'></i></p>";
                        }
                        if ($article['news_articles_status'] == "Draft") {
                            $news_articles_status = "<p class='news-item-status draft'>Draft <i class='fa-solid fa-flag'></i></p>";
                        } ?>

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
        <?php else : ?>
            <p class="font-emphasis">You do not have the necessary Administrator rights to view this page.</p>
        <?php endif; ?>
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
        //script for editing a news article
        $("#edit_news_article").submit(function(event) {
            tinyMCE.triggerSave();
            event.preventDefault();
            //declare form variables and collect GET request information
            news_article_id = '<?php echo $news_articles_id; ?>';
            var formData = new FormData($("#edit_news_article").get(0));
            formData.append("action", "edit");
            formData.append("news_articles_id", news_article_id);
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/news_createarticle-script.php",
                data: formData,
                contentType: false,
                processData: false,
                success: function(data, responseText) {
                    $("#response").html(data);
                    $("#response").slideDown(400);
                    window.location.replace('news_article.php?action=view&news_articles_id=' + news_article_id);
                }
            });

        });
    </script>
</body>

</html>