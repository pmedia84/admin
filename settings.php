<?php
session_start();
require("scripts/functions.php");
check_login();
include("connect.php");
include("inc/settings.php");
$user = new User();
if ($cms->type() == "Business") {
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
    $business_users = $db->prepare('SELECT users.user_id, users.user_name, business_users.business_id, business_users.user_type FROM users NATURAL LEFT JOIN business_users WHERE users.user_id=' . $user->user_id());

    $business_users->execute();
    $business_users->bind_result($user_id, $user_name, $business_id, $user_type);
    $business_users->fetch();
    $business_users->close();
    //find business address details.
    $business = $db->prepare('SELECT * FROM addresses WHERE address_id =' . $address_id);

    $business->execute();
    $business->store_result();
    $business->bind_result($address_id, $address_house, $address_road, $address_town, $address_county, $address_pc);
    $business->fetch();
    $business->close();


    //find social media info
    $socials_query = ('SELECT business_socials.business_socials_id, business_socials.socials_type_id, business_socials.business_socials_url, business_socials.business_id, business_socials_types.socials_type_id, business_socials_types.socials_type_name   FROM business_socials  NATURAL LEFT JOIN business_socials_types WHERE  business_socials.business_id =' . $business_id);
    $socials = $db->query($socials_query);
    $social_result = $socials->fetch_assoc();
}
//load reviews API

$reviews_api_q = $db->query("SELECT * FROM reviews_api");


    $reviews_api_r = mysqli_fetch_assoc($reviews_api_q);


$db->close();
//page meta variables
$meta_description = "Parrot Media - Client Admin Area";
$meta_page_title = "Mi-Admin | Settings";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include("./inc/Page_meta.php");?>
</head>

<body>
    <!-- Main Body Of Page -->
    <main class="main col-2">
        <!-- Header Section -->
        <?php include("inc/header.inc.php"); ?>
        <!-- Nav Bar -->
        <?php include("./inc/nav.inc.php"); ?>
        <!-- /nav bar -->
        <section class="body">
            <div class="breadcrumbs"><a href="index.php" class="breadcrumb">Home</a> / Settings</div>
            <div class="main-cards">
                <?php if ($user->user_type == "Admin" || $user->user_type == "Developer") : ?>
                    <h1>Settings</h1>
                    <div class="std-card">
                        <h2>Business Details</h2>
                        <p><strong>Business Name:</strong> <?= $business_name; ?></p>
                        <p><strong>Email Address:</strong> <?= $business_email; ?></p>
                        <p><strong>Primary Contact No.:</strong> <?= $business_phone; ?></p>
                        <p><strong>Business Contact Name.:</strong> <?= $business_contact_name; ?></p>
                        <a href="edit_businessdetails.php" class="my-2">Edit Business Details</a>
                    </div>
                    <div class="std-card">
                        <h2>Social Media Details</h2>
                        <p>These are your social media details, make sure these links are correct, clients will follow these links from your website to your social media pages.</p>
                        <?php
                        foreach ($socials as $social) : ?>
                            <p><strong>Name:</strong> <?= $social['socials_type_name']; ?></p>
                            <p><strong>URL:</strong> <?= $social['business_socials_url']; ?></p>
                        <?php endforeach; ?>
                        <a class="my-2" href="edit_socialmedia.php">Edit Social Media Details</a>
                    </div>
                    <div class="std-card">
                        <h2>Primary Business Address</h2>
                        <p>Make sure this is up to date, this address is displayed on your contact page.</p>
                        <p><?= $address_house ?></p>
                        <p><?= $address_road ?></p>
                        <p><?= $address_town ?></p>
                        <p><?= $address_county ?></p>
                        <p><?= $address_pc ?></p>
                        <a class="my-2" href="edit_address.php">Edit Address</a>
                    </div>
                    <div class="std-card">
                        <h2>Google Reviews API</h2>
                        <p>Your reviews will load based on these details</p>
                        
                        <form action="settings.php" method="post" id="reviews_api">
                            <div class="form-input-wrapper">
                                <label for="place_id">Place ID</label>
                                <div class="input-response"><input  type="text" name="place_id" id="place_id" value="<?php if($reviews_api_q->num_rows>0){echo$reviews_api_r['place_id'];}?>"><span class="input-response__check"><svg class="icon feather-icon"><use xlink:href="assets/img/icons/feather.svg#check"></use></svg></span></div>
                            </div>
                            <div class="form-input-wrapper">
                                <label for="api_key">API Key</label>
                                <div class="input-response"><input  type="text" name="api_key" id="api_key" value="<?php if($reviews_api_q->num_rows>0){echo$reviews_api_r['api_key'];}?>"><span class="input-response__check"><svg class="icon feather-icon"><use xlink:href="assets/img/icons/feather.svg#check"></use></svg></span></div>
                            </div>
                            <button class="btn-primary btn-secondary my-2">Update API</button>
                        </form>
                    </div>
                    <div class="std-card">
                        <h2>Google Maps API</h2>
                        <p>Your map will load on your website based on these details</p>
                        
                        <form action="settings.php" method="post">
                            <div class="form-input-wrapper">
                                <label for="place_id">Place ID</label>
                                <input type="text" name="place_id" id="place_id" >
                            </div>
                            <div class="form-input-wrapper">
                                <label for="api_key">API Key</label>
                                <input type="text" name="api_key" id="api_key">
                            </div>
                            <button class="btn-primary btn-secondary my-2">Update API</button>
                        </form>
                    </div>
                <?php else : ?>
                    <p class="font-emphasis">You do not have the necessary Administrator rights to view this page.</p>
                <?php endif; ?>
            </div>
        </section>
        <div class="d-none response-card-wrapper" id="response-card-wrapper">
            <div class="response-card">
                <div class="response-card-icon">
                <svg class="icon feather-icon"><use xlink:href="assets/img/icons/feather.svg#info"></use></svg>
                </div>
                <div class="response-card-body">
                    <p id="response-card-text"></p>
                </div>
            </div>
        </div>
        <!-- Footer -->
        <?php include("./inc/footer.inc.php"); ?>
        <!-- /Footer -->
    </main>


</body>

</html>