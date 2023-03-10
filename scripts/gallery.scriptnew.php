<?php
include("../connect.php");

if (isset($_POST['action']) && $_POST['action'] == "delete") {
    $delete_image = $db->prepare('DELETE FROM images WHERE image_id =?');
    $image_id = $_POST['image_id'];

    foreach ($image_id as $id) {
        $img_file = "SELECT image_filename FROM images WHERE image_id=" . $id;
        $result =  mysqli_query($db, $img_file);
        $img = mysqli_fetch_assoc($result);

        $file = $_SERVER['DOCUMENT_ROOT'] . "/admin/assets/img/gallery/" . $img['image_filename'];
        $gallery = $_SERVER['DOCUMENT_ROOT'] . "/assets/img/gallery/" . $img['image_filename'];
        if (fopen($file, "w")) {
            unlink($file);
        };
        if (fopen($gallery, "w")) {
            unlink($gallery);
        };
        $delete_image->bind_param('i', $id);
        $delete_image->execute();
    }
    $delete_image->close();
    exit();
}

if (isset($_POST['action']) && $_POST['action'] == "edit_caption") {
    $image_caption = $db->prepare('UPDATE images SET image_description=? WHERE image_id=?');
    $image_id = $_POST['image_id'];
    $caption = mysqli_real_escape_string($db, $_POST['caption']);

    $image_caption->bind_param('si', $caption, $image_id);
    $image_caption->execute();

    $image_caption->close();
    exit();
}
if (isset($_POST['action']) && $_POST['action'] == "placement") {
    $image_place = $db->prepare('UPDATE images SET image_placement=? WHERE image_id=?');
    $image_id = $_POST['image_id'];
    $placement = $_POST['placement'];
    foreach ($image_id as $id) {
        $image_place->bind_param('si', $placement, $id);
        $image_place->execute();
    }
    $image_place->close();
    exit();
}
if (isset($_POST['action']) && $_POST['action'] == "upload") {
    $new_image = $db->prepare('INSERT INTO images (image_title, image_description, image_filename,  image_placement)VALUES(?,?,?,?)');
    //error codes
    // 0= Success
    // 1= Not Successful
    $response = 1;
    //check the post method has been sent
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
    print_r($_FILES['gallery_img']);
    foreach ($_FILES['gallery_img']['name'] as $file) {

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
    if ($_FILES["gallery_img"]["size"] > 3145728) {
        exit('File too large (max 1MB)');
    }
    // Use fileinfo to get the mime type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($_FILES["gallery_img"]["tmp_name"]);

    $mime_types = ["image/gif", "image/png", "image/jpeg"];

    if (!in_array($_FILES["gallery_img"]["type"], $mime_types)) {
        exit("Invalid file type");
    }
    //detect the orientation of the uploaded file
    $exif = exif_read_data($_FILES["gallery_img"]["tmp_name"]);


    //set the file name
    $newimgname = "gallery-img-0.webp";
    //set the upload path
    $dir = $_SERVER['DOCUMENT_ROOT'] . "/admin/assets/img/gallery/" . $newimgname;
    $i = 1;
    while (file_exists($dir)) {
        $newimgname = "gallery-img-" . $i . ".webp";
        $dir = $_SERVER['DOCUMENT_ROOT'] . "/admin/assets/img/gallery/" . $newimgname;
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
    $new_image->close();
    echo $response;
    exit();
}
?>

<?php if (isset($_GET['action']) && $_GET['action'] == "load_gallery") :
    $gallery_query = $db->query('SELECT images.image_id,  images.image_title, images.image_description, images.image_filename, images.image_upload_date, images.image_placement, images.guest_id, guest_list.guest_id, guest_list.guest_fname, guest_list.guest_sname FROM images LEFT JOIN guest_list ON guest_list.guest_id=images.guest_id');
    include("../inc/settings.php");
?>

    <form action="scripts/gallery.scriptnew.php" id="gallery" method="POST">
        <div class="form-controls gallery-controls">
            <button class="btn-primary form-controls-btn" data-action="delete" id="delete-btn"><i class="fa-solid fa-trash"></i>Delete Selected Images </button>
            <button class="btn-primary" type="button" id="upload-show"><i class="fa-solid fa-upload"></i>Upload Images </button>
            <div class="form-input-wrapper">
                <label for="placement">Image Placement</label>
                <select name="placement" id="placement" data-action="placement">
                    <option value="">Select</option>
                    <option value="Home">Home Page</option>
                    <option value="Gallery">Gallery</option>
                </select>
            </div>
        </div>
        <div class="d-none  my-2" id="upload-card">
            <div class="form-input-wrapper gallery-card">
                <div class="close"><button class="btn-close" type="button" id="close-upload"><i class="fa-solid fa-xmark"></i></button></div>
                <label for="gallery_img">Upload Images</label>
                <p class="form-hint-small">This can be in a JPG, JPEG or PNG format</p>
                <!-- input -->
                <input type="file" name="gallery_img[]" id="gallery_img" accept="image/*" multiple>
                <div class="button-section"><button class="btn-primary my-2 form-controls-btn loading-btn" type="button" id="upload-btn" data-action="upload"><span id="loading-btn-text" class="loading-btn-text"><i class="fa-solid fa-upload"></i>Upload Image</span> <img id="loading-icon" class="loading-icon d-none" src="./assets/img/icons/loading.svg" alt=""></button></div>
            </div>
        </div>
        <p class="text-center my-2">To change a caption, tap or click on caption itself.</p>
        <div class="gallery-card table-wrapper">
            <table class="gallery-table">
                <tbody>
                    <tr>
                        <th><input type="checkbox" name="" id="check_all"></th>
                        <th class="image-details">Image</th>
                        <th>Caption</th>
                        <th>Image Placement</th>
                        <?php if ($guest_image_gallery == "On") : ?>
                            <th>Guest Contributor</th>
                        <?php endif; ?>
                    </tr>
                    <?php foreach ($gallery_query as $img) : ?>
                        <tr>
                            <td class="gallery-select"><input class="gallery-select" data-select="false" type="checkbox" name="image_id[]" id="" value="<?= $img['image_id']; ?>"></td>
                            <td class="gallery-thumb"><a href=""><img src="/admin/assets/img/gallery/<?= $img['image_filename']; ?>" alt=""><?= $img['image_filename']; ?></a></td>
                            <td class="caption" contenteditable="true" data-imgid="<?= $img['image_id']; ?>" data-action="edit_caption"><?= $img['image_description']; ?></td>
                            <td><?= $img['image_placement']; ?></td>
                            <?php if ($guest_image_gallery == "On") : ?>
                                <td><a href="guest?action=view&guest_id=<?= $img['guest_id']; ?>"><?= $img['guest_fname'] . ' ' . $img['guest_sname']; ?></a></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </form>
<?php endif; ?>
<?php if (isset($_GET['term'])) :
    include("../inc/settings.php");
    $term = $_GET['term'];
    if ($term == "guest") {
        $gallery_query = $db->query('SELECT images.image_id,  images.image_title, images.image_description, images.image_filename, images.image_upload_date, images.image_placement, images.guest_id, guest_list.guest_id, guest_list.guest_fname, guest_list.guest_sname FROM images LEFT JOIN guest_list ON guest_list.guest_id=images.guest_id WHERE images.guest_id >""');
    } if($term=="") {
        $gallery_query = $db->query('SELECT images.image_id,  images.image_title, images.image_description, images.image_filename, images.image_upload_date, images.image_placement, images.guest_id, guest_list.guest_id, guest_list.guest_fname, guest_list.guest_sname FROM images LEFT JOIN guest_list ON guest_list.guest_id=images.guest_id');
    } if($term=="ours"){
        $gallery_query = $db->query('SELECT images.image_id,  images.image_title, images.image_description, images.image_filename, images.image_upload_date, images.image_placement, images.guest_id, guest_list.guest_id, guest_list.guest_fname, guest_list.guest_sname FROM images LEFT JOIN guest_list ON guest_list.guest_id=images.guest_id WHERE images.guest_id IS NULL');
    }



?>

    <form action="scripts/gallery.scriptnew.php" id="gallery" method="POST">
        <div class="form-controls gallery-controls">
            <button class="btn-primary form-controls-btn" data-action="delete" id="delete-btn"><i class="fa-solid fa-trash"></i>Delete Selected Images </button>
            <button class="btn-primary" type="button" id="upload-show"><i class="fa-solid fa-upload"></i>Upload Images </button>
            <div class="form-input-wrapper">
                <label for="placement">Image Placement</label>
                <select name="placement" id="placement" data-action="placement">
                    <option value="">Select</option>
                    <option value="Home">Home Page</option>
                    <option value="Gallery">Gallery</option>
                </select>
            </div>
        </div>
        <div class="d-none  my-2" id="upload-card">
            <div class="form-input-wrapper gallery-card">
                <div class="close"><button class="btn-close" type="button" id="close-upload"><i class="fa-solid fa-xmark"></i></button></div>
                <label for="gallery_img">Upload Images</label>
                <p class="form-hint-small">This can be in a JPG, JPEG or PNG format</p>
                <!-- input -->
                <input type="file" name="gallery_img[]" id="gallery_img" accept="image/*" multiple>
                <div class="button-section"><button class="btn-primary my-2 form-controls-btn loading-btn" type="button" id="upload-btn" data-action="upload"><span id="loading-btn-text" class="loading-btn-text"><i class="fa-solid fa-upload"></i>Upload Image</span> <img id="loading-icon" class="loading-icon d-none" src="./assets/img/icons/loading.svg" alt=""></button></div>
            </div>
        </div>
        <p class="text-center my-2">To change a caption, tap or click on caption itself.</p>
        <div class="gallery-card table-wrapper">
            <table class="gallery-table">
                <tbody>
                    <tr>
                        <th><input type="checkbox" name="" id="check_all"></th>
                        <th class="image-details">Image</th>
                        <th>Caption</th>
                        <th>Image Placement</th>
                        <?php if ($guest_image_gallery == "On") : ?>
                            <th>Guest Contributor</th>
                        <?php endif; ?>
                    </tr>
                    <?php foreach ($gallery_query as $img) : ?>
                        <tr>
                            <td class="gallery-select"><input class="gallery-select" data-select="false" type="checkbox" name="image_id[]" id="" value="<?= $img['image_id']; ?>"></td>
                            <td class="gallery-thumb"><a href=""><img src="/admin/assets/img/gallery/<?= $img['image_filename']; ?>" alt=""><?= $img['image_filename']; ?></a></td>
                            <td class="caption" contenteditable="true" data-imgid="<?= $img['image_id']; ?>" data-action="edit_caption"><?= $img['image_description']; ?></td>
                            <td><?= $img['image_placement']; ?></td>
                            <?php if ($guest_image_gallery == "On") : ?>
                                <td><a href="guest?action=view&guest_id=<?= $img['guest_id']; ?>"><?= $img['guest_fname'] . ' ' . $img['guest_sname']; ?></a></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </form>
<?php endif; ?>