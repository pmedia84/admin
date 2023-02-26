<?php
include("../connect.php");
$new_image = $db->prepare('INSERT INTO images (image_title, image_description, image_filename,  image_placement)VALUES(?,?,?,?)');
//error codes
// 0= Success
// 1= Not Successful
$response = 1;
//detect if the post method has been sent, or the user has just browsed here by the url
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    $response = '<div class="form-response error"><p>Post Method Not Set</p></div>';
    echo $response;
}

//check the file upload is set
if (empty($_FILES)) {
    $response = '<div class="form-response error"><p>File upload is empty, check your server settings and try again.</p></div>';
    echo $response;
    exit();
}
//set the file name
$newimgname = "gallery-img-0.webp";
//set the upload path
$dir = $_SERVER['DOCUMENT_ROOT'] . "/admin/assets/img/gallery/" . $newimgname;

print_r($_FILES);
$no_file_errors=0;
foreach ($_FILES['gallery_img']['name'] as $key => $val) {
    // Reject uploaded file larger than 3MB
    //only process files that are below the max file size
    if ($_FILES["gallery_img"]["size"][$key] < 20971520) {
    //check for errors
    if ($_FILES['gallery_img']['error'][$key] !== UPLOAD_ERR_OK) {

        switch ($_FILES['error']['gallery_img'][$key]) {
            case UPLOAD_ERR_PARTIAL:
                exit('File only partially uploaded');
                break;
            case UPLOAD_ERR_NO_FILE:
                exit('No file was uploaded');
                break;
            case UPLOAD_ERR_EXTENSION:
                exit('File upload stopped by a PHP extension');
                break;
            case UPLOAD_ERR_FORM_SIZE:
                exit('File exceeds MAX_FILE_SIZE in the HTML form');
                break;
            case UPLOAD_ERR_INI_SIZE:
                exit('File exceeds upload_max_filesize in php.ini');
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                exit('Temporary folder not found');
                break;
            case UPLOAD_ERR_CANT_WRITE:
                exit('Failed to write file');
                break;
            default:
                exit('Unknown upload error');
                break;
        }
    }
    echo "<p>" . $_FILES['gallery_img']['name'][$key] . "</p>";

    // Use fileinfo to get the mime type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($_FILES["gallery_img"]["tmp_name"][$key]);
    echo $mime_type;
    $mime_types = ["image/gif", "image/png", "image/jpeg", "image/jpg"];
    if (!in_array($_FILES["gallery_img"]["type"][$key], $mime_types)) {
        $response = '<div class="form-response error"><p>Invalid file type, only JPG, JPEG, PNG or Gif is allowed. Please try again</p></div>';
        echo $response;
        exit();
    } 1;
    $i=0;
    //if the file exists already, set a suffix
    while (file_exists($dir)) {
        $newimgname = "gallery-img-" . $i . ".webp";
        $dir = $_SERVER['DOCUMENT_ROOT'] . "/admin/assets/img/gallery/" . $newimgname;
        $i++;
    }


    // convert into webp
    $info = getimagesize($_FILES['gallery_img']['tmp_name'][$key]);
    if ($info['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($_FILES['gallery_img']['tmp_name'][$key]);
    } elseif ($info['mime'] == 'image/gif') {
        $image = imagecreatefromgif($_FILES['gallery_img']['tmp_name'][$key]);
    } elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($_FILES['gallery_img']['tmp_name'][$key]);
    }
    imagepalettetotruecolor($image);
    imagealphablending($image, true);
    imagesavealpha($image, true);
    if ($info['mime'] == 'image/jpeg') {
        //detect the orientation of the uploaded file
        $exif = exif_read_data($_FILES["gallery_img"]["tmp_name"][$key]);
    }

    //rotate the image after converting
    if (isset($exif['Orientation']) && $exif['Orientation'] == 6) {
        $image = imagerotate($image, -90, 0);
    }
    //set an image id to the img name and increment by 1


    if (!imagewebp($image, $dir, 60)) {
        exit('error');
    } else {
        //set up posting to db
        $image_title = mysqli_real_escape_string($db, $_POST['image_title']);
        $image_description = mysqli_real_escape_string($db, $_POST['image_description']);
        $image_filename = $newimgname;
        if (!isset($_POST['img_placement'])) {
            //if blank then set as Other
            $img_place = "Other";
        } else { //else set store in db as an array
            $img_place = implode(",", $_POST['img_placement']);
        }
        $new_image->bind_param('ssss', $image_title, $image_description, $image_filename,  $img_place);
        $new_image->execute();

        /// copy to website paths
        $website_dir = $_SERVER['DOCUMENT_ROOT'] . "/assets/img/gallery/";
        copy($dir, $website_dir . $newimgname);
        $response = 0;
    }
        
    }else{
        $no_file_errors ++;
        echo '<div class="form-response error"><p>'.$no_file_errors.' of your images was over the maximum limit and was not uploaded.</p></div>';
    }
    $new_image->close();
   
}
