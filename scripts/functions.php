<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/SMTP.php';
require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/Exception.php';
function check_login()
{
    if (!isset($_SESSION['loggedin'])) {
        $location = urlencode($_SERVER['REQUEST_URI']);
        header("Location: login.php?location=" . $location);
    }
}

class Cms
{
    public $cms_type;
    //?variables for wedding CMS
    public $wedding_name;
    public $wedding_date;
    public $wedding_id;
    public $wedding_time;
    //?
    //?Variables for business CMS
    public $business_id;
    public $business_name;
    public $business_tel;
    public $business_email;
    public $business_contact;
    //?
    //*return the type of cms
    function type()
    {
        //find the cms type first
        include("../connect.php");
        $cms_q = $db->query('SELECT cms_type FROM settings');
        $cms_r = mysqli_fetch_assoc($cms_q);
        $this->cms_type = $cms_r['cms_type'];
        return $this->cms_type;
    }

    function setup()
    {
        include("../connect.php");
        //business
      
            //look for a business setup in the db, if not then direct to the setup page
            $business_query = ('SELECT business_id FROM business');
            $business = $db->query($business_query);
            if ($business->num_rows == 0) {
                header('Location: setup.php?action=setup_business');
                $this->business_id=$business['business_id'];
                return ;
            }
            //check that there are users set up 
            $business_user_query = ('SELECT * FROM business_users');
            $business_user = $db->query($business_user_query);
            if ($business_user->num_rows < 2) {
                header('Location: setup.php?action=check_users_business');
                return;
            }
        

    }

    //load business info
    function business_load()
    {
        include("../connect.php");
        $business_q = $db->query('SELECT * FROM business');
        $business_r = mysqli_fetch_assoc($business_q);
        $name = $business_r['business_name'];
        $this->business_name = $name;
    }


    //*return all business info
    function b_name()
    {
        include("../connect.php");
        $business_q = $db->query('SELECT * FROM business');
        $business_r = mysqli_fetch_assoc($business_q);
        $name = $business_r['business_name'];
        $this->business_name = $name;
        return $this->business_name;
    }
    function b_id(){
        include("./connect.php");
        $business_query = ('SELECT business_id FROM business');
        $business = $db->query($business_query);
        $r=mysqli_fetch_assoc($business);
        $this->business_id=$r['business_id'];
        return $this->business_id;
    }
}



function cms_type()
{
    include("../connect.php");
    $cms_q = $db->query('SELECT cms_type FROM settings');
    $cms_r = mysqli_fetch_assoc($cms_q);
    $cms_type = $cms_r['cms_type'];
    return $cms_type;
}

class Module
{

    public $name;
    public $status;

    function module_name($name)
    {

        $this->name = $name;
    }
    function status()
    {
        include("../connect.php");
        $modules_query = $db->query('SELECT module_status FROM modules WHERE module_name= "' . $this->name . '"');
        $modules_r = mysqli_fetch_assoc($modules_query);
        $module_status = $modules_r['module_status'];
        $this->status = $module_status;
        return $this->status;
        $db->close();
    }
}

//*modules


$news_m = new Module();
$news_m->module_name("News");

$image_gallery = new Module();
$image_gallery->module_name("Image Gallery");

$events = new Module();
$events->module_name("Events");

$price_list = new Module();
$price_list->module_name("Price List");


$reviews = new Module();
$reviews->module_name("Reviews");

$forms = new Module();
$forms->module_name("Forms");



//* User class for the login system etc
class User
{
    public $user_id;
    public $user_type;
    public $user_name;
    public $logged_in;
    public $user_email;
    public $user_em_status;
    
    function em_status()
    {
        //find email status of the user
        include("../connect.php");
        $q = $db->query("SELECT user_em_status FROM users WHERE user_id=" . $this->user_id() . "");
        $r = mysqli_fetch_assoc($q);
        $status = $r['user_em_status'];
        $this->user_em_status = $status;
        return $this->user_em_status;
    }
    function user_id()
    {
        $this->user_id = $_SESSION['user_id'];
        return $this->user_id;
    }
    function logged_id()
    {
        $this->logged_in = $_SESSION['logged_in'];
        return $this->logged_in;
    }
    function user_type()
    {
        include("../connect.php");
        $user_type_q = $db->query("SELECT user_type FROM users WHERE user_id=" . $this->user_id() . "");
        $user_type_r = mysqli_fetch_assoc($user_type_q);
        $type = $user_type_r['user_type'];
        $this->user_type = $type;
        return $this->user_type;
    }
    function name()
    {
        include("../connect.php");
        $q = $db->query("SELECT user_name FROM users WHERE user_id=" . $this->user_id() . "");
        $r = mysqli_fetch_assoc($q);
        $name = $r['user_name'];
        $this->user_name = $name;
        return $this->user_name;
    }
    function update()
    {
        $email = $_POST['user_email'];
        $user_name = $_POST['user_name'];
        //url for confirming new emails
        $url = $_SERVER['SERVER_NAME'] . "/admin/profile.php?confirm=email";

        //update user details from form
        include("../connect.php");
        //check if new email is different to saved email
        $q = $db->query("SELECT user_email FROM users WHERE user_id=" . $this->user_id() . "");
        $r = mysqli_fetch_assoc($q);
        $old_email = $r['user_email'];
        if ($old_email != $email) {
            //load email config file
            //config file name
            $config_file = "config.json";
            //load config file
            $config = file_get_contents($config_file);
            //decode json file
            $file = json_decode($config, TRUE);
            //set up variables
            $host = $file['email_config']['host'];
            $username = $file['email_config']['username'];
            $pw = $file['email_config']['pw'];
            $fromname = $file['email_config']['fromname'];
            //send email to get user to confirm email
            //set the user email status to unconfirmed
            $em_status = "TEMP";
            //load template
            $body = file_get_contents("inc/User_update_email.html");
            //set up email
            $body = str_replace(["{{user_name}}"], [$user_name], $body);
            $body = str_replace(["{{user_email}}"], [$email], $body);
            $body = str_replace(["{{url}}"], [$url], $body);
            //* Subject
            $subject = "New email address";
            $fromserver = $username;
            $email_to = $email;
            $mail = new PHPMailer(true);
            $mail->IsSMTP();
            $mail->Host = $host; // Enter your host here
            $mail->SMTPAuth = true;
            $mail->Username = $username; // Enter your email here
            $mail->Password = $pw; //Enter your password here
            $mail->Port = 25;
            $mail->From = $username;
            $mail->FromName = $fromname;
            $mail->Sender = $fromserver; // indicates ReturnPath header
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->IsHTML(true);
            $mail->AddAddress($email_to);
            if (!$mail->Send()) {
                echo "Mailer Error: " . $mail->ErrorInfo;
            }
        }
        $update = $db->prepare("UPDATE users SET user_email=?, user_name=?, user_em_status=? WHERE user_id=?");
        $update->bind_param("sssi", $email, $user_name, $em_status,  $this->user_id);
        $update->execute();
        $update->close();
    }
    function verify_email()
    {
        //update users table email as set
        include("connect.php");
        $update = $db->prepare("UPDATE users SET user_em_status=? WHERE user_id=?");
        $this->user_em_status = "SET";
        $update->bind_param("si", $this->user_em_status,  $this->user_id);
        $update->execute();
        $update->close();
    }
    function new_pw($user_id,$pw){
        //new password function
        $code = 200;
        include("../connect.php");
        $update = $db->prepare("UPDATE users SET user_pw=? WHERE user_id=?");
        $pw = password_hash($pw, PASSWORD_DEFAULT);
        $update->bind_param("si", $pw, $user_id);
        $update->execute();
       

        // find user email
        $q = $db->query("SELECT user_email, user_name FROM users WHERE user_id=" . $user_id);
        $r = mysqli_fetch_assoc($q);
        $email = $r['user_email'];
        $user_name = $r['user_name'];
        //load email config file
            //config file name
            $config_file = "../config.json";
            //load config file
            $config = file_get_contents($config_file);
            //decode json file
            $file = json_decode($config, TRUE);
            //set up variables
            $host = $file['email_config']['host'];
            $username = $file['email_config']['username'];
            $db_pw = $file['email_config']['pw'];
            $fromname = $file['email_config']['fromname'];
            //load template
            $body = file_get_contents("../inc/User_update_pw.html");
            //set up email
            $body = str_replace(["{{user_name}}"], [$user_name], $body);
            $subject = "New password";
            $fromserver = $username;
            $email_to = $email;
            $mail = new PHPMailer(true);
            $mail->IsSMTP();
            $mail->Host = $host; // Enter your host here
            $mail->SMTPAuth = true;
            $mail->Username = $username; // Enter your email here
            $mail->Password = $db_pw; //Enter your password here
            $mail->Port = 25;
            $mail->From = $username;
            $mail->FromName = $fromname;
            $mail->Sender = $fromserver; // indicates ReturnPath header
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->IsHTML(true);
            $mail->AddAddress($email_to);
            if (!$mail->Send()) {
                echo "Mailer Error: " . $mail->ErrorInfo;
            }
        $response = array("response_code" => $code);
        echo json_encode($response);
    }
}

//image class for all image operations
class Img
{
    public $img_id;
    //Image placemement
    public $placement;
    //ID of the guest image submission request
    public $submission_id;
    // Total amount of images posted from user submission
    public $img_total;
    public $msg;
    public $response_code;
    public $status;
    //image submission ID
    public $sub_id;
    //the amount of images that were not successful
    public $img_errors;
    //total amount of images successful
    public $success_img;
    //* Response Codes
    //200: Success
    //400: Error
    function __construct()
    {
        $this->status = "Approved";
        $this->img_errors = 0;
        $this->success_img = 0;
        $this->img_total = 0;
        $this->placement = "Gallery";
        if (isset($_POST['submission_id'])) {
            $this->submission_id = $_POST['submission_id'];
        }
    }

    //? Upload new images from admin panel
    function upload()
    {

        //check the post method has been sent
        if ($_SERVER['REQUEST_METHOD'] !== "POST") {
            $this->msg = "Request Method Not Set";
            $this->response_code = 400;
            return;
        }
        $this->img_total = count($_FILES['gallery_img']['name']);
        //insert into db    
        include("../connect.php");
        //prepare the insert query for images table
        $img = $db->prepare('INSERT INTO images (image_filename,  image_placement, status)VALUES(?,?,?)');
        //set the file name
        $newimgname = "gallery-img-0.webp";
        //set the upload path for admin
        $dir = $_SERVER['DOCUMENT_ROOT'] . "/admin/assets/img/gallery/" . $newimgname;
        foreach ($_FILES['gallery_img']['name'] as $key => $val) {
            // Reject uploaded file larger than 3MB
            //only process files that are below the max file size
            if ($_FILES["gallery_img"]["size"][$key] < 20971520) {
                //check for errors
                if ($_FILES['gallery_img']['error'][$key] !== UPLOAD_ERR_OK) {
                    switch ($_FILES['error']['gallery_img'][$key]) {
                        case UPLOAD_ERR_PARTIAL:
                            $this->msg = "File only partially uploaded";
                            $this->response_code = 400;
                            return;
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            $this->msg = "No file was uploaded";
                            $this->response_code = 400;
                            return;
                            break;
                        case UPLOAD_ERR_EXTENSION:
                            $this->msg = "File upload stopped by a PHP extension";
                            $this->response_code = 400;
                            return;
                            break;
                        case UPLOAD_ERR_FORM_SIZE:
                            $this->msg = "File exceeds MAX_FILE_SIZE in the HTML form";
                            $this->response_code = 400;
                            return;
                            break;
                        case UPLOAD_ERR_INI_SIZE:
                            $this->msg = "File exceeds upload_max_filesize in php.ini";
                            $this->response_code = 400;
                            return;
                            break;
                        case UPLOAD_ERR_NO_TMP_DIR:
                            $this->msg = "Temporary folder not found";
                            $this->response_code = 400;
                            return;
                            break;
                        case UPLOAD_ERR_CANT_WRITE:
                            $this->msg = "Failed to write file";
                            $this->response_code = 400;
                            return;
                            break;
                        default:
                            $this->msg = "Unknown upload error";
                            $this->response_code = 400;
                            return;
                            break;
                    }
                }

                // Use fileinfo to get the mime type
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime_type = $finfo->file($_FILES["gallery_img"]["tmp_name"][$key]);
                $mime_types = ["image/gif", "image/png", "image/jpeg", "image/jpg"];
                if (!in_array($_FILES["gallery_img"]["type"][$key], $mime_types)) {
                    $this->msg = "Invalid file type, only JPG, JPEG, PNG or Gif is allowed. One of your files has the type of: " . $mime_type;
                    $this->response_code = 400;
                    return;
                }
                $i = 0;
                //if the file exists already, set a prefix
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
                    @$exif = exif_read_data($_FILES["gallery_img"]["tmp_name"][$key]);
                }
                //rotate the image after converting
                if (isset($exif['Orientation']) && $exif['Orientation'] == 6) {
                    $image = imagerotate($image, -90, 0);
                }
                //convert into a webp image, if unsuccessful then increment into the error variable 
                if (!imagewebp($image, $dir, 60)) {
                    $this->img_errors++;
                    return;
                } else {
                    //set up posting to db
                    $image_filename = $newimgname;

                    //insert into database
                    $img->bind_param('sss',  $image_filename,  $this->placement,  $this->status);
                    $img->execute();
                    $image_id = $img->insert_id;
                    /// copy to website paths
                    $guests_dir = $_SERVER['DOCUMENT_ROOT'] . "/guests/assets/img/gallery/";
                    //copy the image to the guest directory
                    if (!copy($dir, $guests_dir . $newimgname)) {
                        //if unsuccessful
                        $this->msg = "Images were not copied successfully";
                        $this->response_code = 400;
                        return;
                    } else {
                        //if successful increment the successful img count
                        $this->success_img++;
                        $this->response_code = 200;
                    }
                }
            } else {
                $this->img_errors++;
            }
        }
        $img->close();
    }
    function save_submission()
    {
        //Only run this section if images have been posted from user 
        if (isset($_POST['gallery_img'])) {
            $this->img_total = count($_POST['gallery_img']);
            require("../connect.php");
            //update the individual submission items first
            $sub_item = $db->prepare('UPDATE image_sub_items SET sub_item_status=? WHERE image_id=?');
            $submission = $db->prepare('UPDATE image_submissions SET submission_status=? WHERE submission_id=?');
            //! finish from here, update submission table 
            $img = $db->prepare('UPDATE images SET status=? WHERE image_id=?');
            foreach ($_POST['gallery_img'] as $image) {
                $sub_item->bind_param("si", $this->status, $image['image_id']);
                $sub_item->execute();
                $img->bind_param("si", $this->status, $image['image_id']);
                $img->execute();
                $this->response_code = 200;
                $this->success_img++;
            }
            $sub_item->close();
            $img->close();
            //! find the total amount of images in the submission, if they have all been accepted then mark submission as approved. If not mark as partially approved so users can still see the images that have not been approved for the website.
            $sub_count = $db->query("SELECT  COUNT(sub_item_id) AS count FROM image_sub_items WHERE submission_id=" . $this->submission_id . " AND sub_item_status = 'Awaiting'");
            $count_r = mysqli_fetch_assoc($sub_count);
            $t = $count_r['count'];
            if ($t > $this->img_total) {
                $this->status = "Partial";
            }
            $submission->bind_param("si", $this->status, $this->submission_id);
            $submission->execute();
            $submission->close();
        } else {
            //if no images have been selected, return an error
            $this->response_code = 400;
            $this->msg = "No images have been submitted, please try again";
        }
    }
    //?Delete Images
    function delete()
    {
        //image array, contains the db image id and the filename
        $images = $_POST['gallery_img'];
        //define how many images have been request for delete
        $this->img_total = count($images);
        /// copy to website paths
        $guests_dir = $_SERVER['DOCUMENT_ROOT'] . "/guests/assets/img/gallery/";
        //admin file path for deleting the images
        $dir = $_SERVER['DOCUMENT_ROOT'] . "/admin/assets/img/gallery/";
        //$this->img_total = count($_POST['image_id']);
        //loop through the image ID array and delete images
        include("../connect.php");
        //test the connection
        if (mysqli_connect_error()) {
            $this->msg = "Connect error" . mysqli_connect_error();
            $this->response_code = 400;
            return;
        }
        //check the post method has been sent
        if ($_SERVER['REQUEST_METHOD'] !== "POST") {
            $this->msg = "Request Method Not Set";
            $this->response_code = 400;
            return;
        }
        //Loop through each image in the POST array, delete the files and the db entry
        foreach ($images as $image) {

            if ($db->query('DELETE FROM images WHERE image_id =' . $image['image_id'])) {
                //increment the success total by one for each successful image deleted
                $this->success_img++;
                $this->response_code = 200;
            } else {
                $this->img_errors++;
                $this->response_code = 400;
                $this->msg = "Could not delete image";
                return;
            }
            if (fopen($guests_dir . $image['image_filename'], "w")) {
                unlink($guests_dir . $image['image_filename']);
            } else {
                $this->img_errors++;
                $this->response_code = 400;
                $this->msg = "Could not delete image";
                return;
            }
            if (fopen($dir . $image['image_filename'], "w")) {
                unlink($dir . $image['image_filename']);
            } else {
                $this->img_errors++;
                $this->response_code = 400;
                $this->msg = "Could not delete image";
                return;
            };
        }
    }
    //total images in post request
    function img_total()
    {
        return $this->img_total;
    }
    //return the message if any
    function msg()
    {
        return $this->msg;
    }
    //return the response code
    function response_code()
    {
        return $this->response_code;
    }
    //return how many images have errors
    function img_error_amt()
    {
        return $this->img_errors;
    }
    //return how many images were successfully processed.
    function img_success_amt()
    {
        return $this->success_img;
    }
}

//? function to update Reviews API
function reviews_api()
{
    include("../connect.php");
    $place_id = $_POST['place_id'];
    $api_key = $_POST['api_key'];
    //code 200 for success, change if an error occurs
    $code = 200;
    //check if there is already an API key
    $q = $db->query("SELECT api_id FROM reviews_api");
    $create =  $db->prepare("INSERT INTO reviews_api (place_id, api_key) VALUES(?,?)");
    $update = $db->prepare("UPDATE reviews_api SET place_id=?, api_key=? WHERE api_id=?");
    if ($q->num_rows > 0) {
        $r = mysqli_fetch_assoc($q);
        $api_id = $r['api_id'];

        $update->bind_param("ssi", $place_id, $api_key, $api_id);
        $update->execute();
        $update->close();
    } else {
        $create->bind_param('ss', $place_id, $api_key);
        $create->execute();
        $create->close();
    }
    $response = array("response_code" => $code);
    echo json_encode($response);
}
if (isset($_POST['action']) && $_POST['action'] == "reviews_api") {
    reviews_api();
}
///

//Update user profile
if (isset($_POST['action']) && $_POST['action'] == "user_pw_change") {
    $pw=$_POST['password'];
    $user_id = $_POST['user_id'];
    $user = new User();
    $user->new_pw($user_id, $pw);
    
}
