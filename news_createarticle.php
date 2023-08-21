<?php
session_start();
require("scripts/functions.php");
check_login();
include("connect.php");
include("inc/settings.php");
$user=new User();

////////////////Find details of the cms being used, on every page\\\\\\\\\\\\\\\
//Variable for name of CMS
//wedding is the name of people
//business name
$cms_name = "";
$user_id = $_SESSION['user_id'];
if ($cms->type() =="Business") {
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
    $business_users = $db->prepare('SELECT users.user_id, users.user_name, business_users.business_id, business_users.user_type FROM users NATURAL LEFT JOIN business_users WHERE users.user_id=' . $user_id);
    $business_users->execute();
    $business_users->bind_result($user_id, $user_name, $business_id, $user_type);
    $business_users->fetch();
    $business_users->close();
}


//page meta variables
$meta_description = "Parrot Media - Client Admin Area";
$meta_page_title = "Mi-Admin | Settings";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include("./inc/Page_meta.php");?>
<script src="https://cdn.tiny.cloud/1/7h48z80zyia9jc41kx9pqhh00e1e2f4pw9kdcmhisk0cm35w/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<script>
    tinymce.init({
        selector: 'textarea#news_article_body',
        height: 500,
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
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
        <?php include("inc/header.inc.php"); ?>
        <!-- Nav Bar -->
        <?php include("./inc/nav.inc.php"); ?>
        <!-- /nav bar -->
        <section class="body">
            <div class="breadcrumbs mb-2">
                <a href="index.php" class="breadcrumb">Home</a> /
                <a href="news.php" class="breadcrumb">News</a>
                / Create News Post
            </div>
            <div class="main-cards">

                <h1><i class="fa-solid fa-newspaper"></i> Create News Post</h1>
                <?php if ($user_type == "Admin" || $user_type == "Developer") : ?>


                    <div class="std-card">
                        <form id="create_news_article" action="scripts/news_createarticle.php" method="post" enctype="multipart/form-data" data-user_id="<?=$user_id;?>">
                            <div class="form-input-wrapper">
                                <label for="news_articles_title">Title</label>
                                <!-- input -->
                                <input class="text-input input" type="text" name="news_articles_title" id="news_articles_title" placeholder="Article Title" required="" maxlength="45" autofocus>
                            </div>
                            <div class="form-input-wrapper my-2">
                                <label for="news_articles_img">Header Image</label>
                                <p class="form-hint-small">This can be in a JPG, JPEG or PNG format. And no larger than 1MB.</p>
                                <!-- input -->
                                <input type="file" name="news_articles_img" id="news_articles_img" accept="image/*">
                            </div>
                            <div class="form-input-wrapper my-2">
                                <label for="news_article_body">Article Body</label>
                                <textarea id="news_article_body" name="news_article_body">

                                </textarea>
                            </div>

                            <div class="form-input-wrapper my-2">
                                <label for="news_articles_status">Status</label>
                                <p class="form-hint-small">Set as a draft to come back and finish, or set as published to publish to your website straight away.</p>
                                <select name="news_articles_status" id="news_articles_status" required="">
                                    <option value="" selected>Select Article Status</option>
                                    <option value="Draft">Draft</option>
                                    <option value="Published">Published</option>
                                </select>
                            </div>
                            <div class="button-section my-3">
                                <button class="btn-primary form-controls-btn" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save Post </button>

                            </div>

                            <div id="response" class="d-none">
                                <p>Article Saved </p>
                            </div>
                        </form>
                    </div>
            </div>

        <?php else : ?>
            <p class="font-emphasis">You do not have the necessary Administrator rights to view this page.</p>
        <?php endif; ?>
        </div>

        </section>
        <div class="d-none" id="response-card-wrapper">
            <div class="response-card">
                <div class="response-card-icon">
                    <i class="fa-solid fa-circle-info"></i>
                </div>
                <div class="response-card-body">
                    <p id="response-msg"></p>
                </div>
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
        $("#form-reset").click(function() {
            $("#create_news_article *").prop("disabled", false);
        });
    </script>
<script src="assets/js/news.js"></script>
</body>

</html>