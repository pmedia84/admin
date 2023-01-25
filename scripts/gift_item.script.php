<?php
if ($_POST['action'] == "edit") { //check if the action type of edit has been set in the post request
    include("../connect.php");
    //determine variables
   $gift_item_id = $_POST['gift_item_id'];
   $gift_item_name = mysqli_real_escape_string($db, $_POST['gift_item_name']);
   $gift_item_desc = mysqli_real_escape_string($db, $_POST['gift_item_desc']);
   $gift_item_url = mysqli_real_escape_string($db, $_POST['gift_item_url']);
   $gift_item_type = $_POST['gift_item_type'];
   $gift_item_img = $_FILES['gift_item_img']['name'];
   //use this for removing an old image if a new one has been uploaded
    $gift_item_img_old = $_POST['gift_item_img_old'];
    //check that an image has been uploaded
    if ($_FILES['gift_item_img']['name'] == null) {

        
    } else { //if there is an image uploaded then save it to the folder
        //////////////////////sort the image upload first////////////////////////////////////////
        $dir = "../assets/img/gift_list/";
        $file = $dir . basename($_FILES['gift_item_img']['name']);
        $imageFileType = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["gift_item_img"]["tmp_name"]);
        if ($check !== false) {
            $upload = 1;
        } else {
            $response = '<div class="form-response error"><p>File type not supported. Please try again.</p><a href=""news_createarticle.php>Try Again</a></div>';
        }
        // Check if file already exists
        if (file_exists($file)) {
            $response = '<div class="form-response error"><p>Image already exists</p></div>';
            echo $response;
            exit();
            $uploadOk = 0;
        }

        // Allow certain file formats
        if (
            $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $upload = 0;

        }

        // Check if $upload is set to 0 by an error
        if ($upload == 0) {
            $response = '<div class="form-response error"><p>Error, your article image was not saved. You can try again by editing your image</p></div>';
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["gift_item_img"]["tmp_name"], $file)) {
                //define articles img variable
                $image_filename = basename($_FILES['gift_item_img']['name']);
            }
        }
        // Check file size
        if ($_FILES["gift_item_img"]["size"] > 1048576) {
            //if the image is too big then run a compression. If not then leave the file as it is

            $imagename = $_FILES['gift_item_img']['name'];
            $source = $_FILES['new_image']['tmp_name'];
            $target = $dir.$imagename;
            move_uploaded_file($source, $target);
            
            $imagepath = $imagename;
            $save =  $dir. $imagepath; //This is the new file you saving
            $file =  $dir. $imagepath; //This is the original file
  
            list($width, $height) = getimagesize($file); 
  
            $tn = imagecreatetruecolor($width, $height);
  
            //$image = imagecreatefromjpeg($file);
            $info = getimagesize($target);
            if ($info['mime'] == 'image/jpeg'){
              $image = imagecreatefromjpeg($file);
            }elseif ($info['mime'] == 'image/gif'){
              $image = imagecreatefromgif($file);
            }elseif ($info['mime'] == 'image/png'){
              $image = imagecreatefrompng($file);
            }
  
            imagecopyresampled($tn, $image, 0, 0, 0, 0, $width, $height, $width, $height);
            imagejpeg($tn, $save, 60);
        }
        //remove the old image
        $old_img = $gift_item_img_old;
        $file_path = $dir.$old_img;
        if(fopen($file_path,"w")){
            unlink($file_path);
        };
    }
    //Update gift item
    $gift_item = $db->prepare('UPDATE gift_list SET gift_item_name=?, gift_item_desc=?, gift_item_url=?, gift_item_type=?, gift_item_img=?  WHERE gift_item_id =?');
    $gift_item->bind_param('sssssi', $gift_item_name, $gift_item_desc, $gift_item_url, $gift_item_type, $gift_item_img, $gift_item_id);
    $gift_item->execute();
    $gift_item->close();

    
}


if ($_POST['action'] == "create") { //check if the action type of create has been set in the post request
    include("../connect.php");
    //determine variables
    $gift_item_name = mysqli_real_escape_string($db, $_POST['gift_item_name']);
    $gift_item_desc = mysqli_real_escape_string($db, $_POST['gift_item_desc']);
    $gift_item_url = mysqli_real_escape_string($db, $_POST['gift_item_url']);
    $gift_item_type = $_POST['gift_item_type'];
    $gift_item_img = $_FILES['gift_item_img']['name'];

    //process any images that have been uploaded
    //check that an image has been uploaded
    if ($_FILES['gift_item_img']['name'] == null) {

        $gift_item_img = "";
    } else { //if there is an image uploaded then save it to the folder
        //////////////////////sort the image upload first////////////////////////////////////////
        $dir = $_SERVER['DOCUMENT_ROOT']. "/assets/img/gallery/";
        $file = $dir . basename($_FILES['gift_item_img']['name']);
        $imageFileType = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["gift_item_img"]["tmp_name"]);
        if ($check !== false) {
            $upload = 1;
        } else {
            $response = '<div class="form-response error"><p>File type not supported. Please try again.</p><a href=""news_createarticle.php>Try Again</a></div>';
        }
        // Check if file already exists
        if (file_exists($file)) {
            $response = '<div class="form-response error"><p>Image already exists</p></div>';
            echo $response;
            exit();
            $uploadOk = 0;
        }

        // Allow certain file formats
        if (
            $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $upload = 0;

        }

        // Check if $upload is set to 0 by an error
        if ($upload == 0) {
            $response = '<div class="form-response error"><p>Error, your article image was not saved. You can try again by editing your image</p></div>';
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["gift_item_img"]["tmp_name"], $file)) {
                //define articles img variable
                $image_filename = basename($_FILES['gift_item_img']['name']);
            }
        }
        // Check file size
        if ($_FILES["gift_item_img"]["size"] > 1048576) {
            //if the image is too big then run a compression. If not then leave the file as it is

            $imagename = $_FILES['gift_item_img']['name'];
            $source = $_FILES['new_image']['tmp_name'];
            $target = $dir.$imagename;
            move_uploaded_file($source, $target);
            
            $imagepath = $imagename;
            $save =  $dir. $imagepath; //This is the new file you saving
            $file =  $dir. $imagepath; //This is the original file
  
            list($width, $height) = getimagesize($file); 
  
            $tn = imagecreatetruecolor($width, $height);
  
            //$image = imagecreatefromjpeg($file);
            $info = getimagesize($target);
            if ($info['mime'] == 'image/jpeg'){
              $image = imagecreatefromjpeg($file);
            }elseif ($info['mime'] == 'image/gif'){
              $image = imagecreatefromgif($file);
            }elseif ($info['mime'] == 'image/png'){
              $image = imagecreatefrompng($file);
            }
  
            imagecopyresampled($tn, $image, 0, 0, 0, 0, $width, $height, $width, $height);
            imagejpeg($tn, $save, 60);
        }
    }
    //insert gift item
    $guest = $db->prepare('INSERT INTO gift_list (gift_item_name, gift_item_desc, gift_item_url, gift_item_type, gift_item_img) VALUES (?,?,?,?,?)');
    $guest->bind_param('sssss', $gift_item_name, $gift_item_desc, $gift_item_url, $gift_item_type, $gift_item_img);
    $guest->execute();
    $guest->close();
}
