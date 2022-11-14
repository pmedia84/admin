<?php
$response = "";
if (isset($_POST['news_articles_title'])) {
    if ($_POST['action'] == "addnew") {

        //sort the image upload first
        $dir = "../assets/img/news/";
        $file = $dir . basename($_FILES['news_articles_img']['name']);
        $imageFileType = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image

        $check = getimagesize($_FILES["news_articles_img"]["tmp_name"]);
        if ($check !== false) {
            $upload = 1;
        } else {
            $response = '<div class="form-response error"><p>File type not supported. Please try again.</p><a href=""news_createarticle.php>Try Again</a></div>';
        }
        // Check file size
        if ($_FILES["news_articles_img"]["size"] > 1048576) {
            $response = '<div class="form-response error"><p>Image size is too large.</p><a href=""news_createarticle.php>Try Again</a></div>';
            $upload = 0;
        }

        // Allow certain file formats
        if (
            $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"

        ) {
            $response = '<div class="form-response error"><p>Sorry, only JPG, JPEG, PNG images are supported.</p><a href=""news_createarticle.php>Try Again</a></div>';
            $uploadOk = 0;
        }

        // Check if $upload is set to 0 by an error
        if ($upload == 0) {
            $response = '<div class="form-response error"><p>Error, your article image was not saved. You can try again by editing your image</p></div>';
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["news_articles_img"]["tmp_name"], $file)) {
                echo "The file " . htmlspecialchars(basename($_FILES["news_articles_img"]["name"])) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }
    //  $news_article_body = $_POST['news_articles_body'];
    // echo $_POST['news_articles_body'];
}

//$response = '<p class="form-response">Article Saved, you can no longer edit it from this screen. ADD LINK HERE TO THE NEWS ITEM </p>';

// echo $response;

echo $response;
