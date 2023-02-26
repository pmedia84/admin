<?php
    include("../connect.php");
    $new_image = $db->prepare('INSERT INTO images (image_title, image_description, image_filename,  image_placement)VALUES(?,?,?,?)');
//error codes
// 0= Success
// 1= Not Successful
$response=1;
//check the post method has been sent
if($_SERVER['REQUEST_METHOD'] !=="POST"){
    $response='<div class="form-response error"><p>Post Method Not Set</p></div>';
    echo $response;
    
}
//check the file upload is set
if(empty($_FILES)){
    $response='<div class="form-response error"><p>File upload is empty, check your server settings and try again.</p></div>';
    echo $response;
    exit();
}
print_r($_FILES['gallery_img']);
foreach($_FILES['gallery_img']['name'] as $file){
    
    echo $file;
}
//check for error codes
if ($_FILES['gallery_img']['error'] !== UPLOAD_ERR_OK) {

    switch ($_FILES["gallery_img"]["error"]) {
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
// Reject uploaded file larger than 1MB
if ($_FILES["gallery_img"]["size"] > 3145728 ) {
    exit('File too large (max 1MB)');
}
// Use fileinfo to get the mime type
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime_type = $finfo->file($_FILES["gallery_img"]["tmp_name"]);

$mime_types = ["image/gif", "image/png", "image/jpeg"];
        
if ( ! in_array($_FILES["gallery_img"]["type"], $mime_types)) {
    exit("Invalid file type");
}
//detect the orientation of the uploaded file
$exif = exif_read_data($_FILES["gallery_img"]["tmp_name"]);


//set the file name
$newimgname = "gallery-img-0.webp";
//set the upload path
$dir = $_SERVER['DOCUMENT_ROOT']. "/admin/assets/img/gallery/".$newimgname;
$i = 1;
while(file_exists($dir)){
    $newimgname = "gallery-img-".$i.".webp";
    $dir = $_SERVER['DOCUMENT_ROOT']. "/admin/assets/img/gallery/".$newimgname;
    $i++;
}
// convert into webp
$info = getimagesize($_FILES['gallery_img']['tmp_name']);
if ($info['mime'] == 'image/jpeg') {
    $image = imagecreatefromjpeg($_FILES['gallery_img']['tmp_name']);
} elseif ($info['mime'] == 'image/gif') {
    $image = imagecreatefromgif($_FILES['gallery_img']['tmp_name']);
} elseif ($info['mime'] == 'image/png') {
    $image = imagecreatefrompng($_FILES['gallery_img']['tmp_name']);
}
imagepalettetotruecolor($image);
imagealphablending($image, true);
imagesavealpha($image, true);
//rotate the image after converting
if(isset($exif['Orientation']) && $exif['Orientation']==6){
    $image = imagerotate($image, -90, 0);
}
//set an image id to the img name and increment by 1


if(! imagewebp($image, $dir, 60)){
    exit('error');
}else{
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
$website_dir = $_SERVER['DOCUMENT_ROOT']. "/assets/img/gallery/";
copy($dir, $website_dir . $newimgname);
$response=0;
}
$new_image->close();
echo $response;
?>