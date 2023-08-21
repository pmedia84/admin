<?php
session_start();
require("scripts/functions.php");
check_login();
$user = new User();
include("inc/settings.php");
include("./connect.php");

$user_id = $user->user_id();
if (isset($_POST['action']) && $_POST['action'] == "update") {
    $user->update();
}

//page meta variables
$meta_description = "Parrot Media - Client Admin Area";
$meta_page_title = "Mi-Admin | Profile - " . $user->name();
//load user details
$q = $db->query("SELECT *FROM users WHERE user_id=" . $user->user_id() . "");
$r = mysqli_fetch_assoc($q);


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
        <?php include("./inc/header.inc.php"); ?>
        <!-- Nav Bar -->
        <?php include("./inc/nav.inc.php"); ?>
        <!-- /nav bar -->
        <section class="body">
            <div class="breadcrumbs mb-2"><a href="index.php" class="breadcrumb">Home</a> / Users</div>
            <?php if (isset($_GET['confirm']) && $_GET['confirm'] == "email" && $user->em_status() == "TEMP") : $user->verify_email(); ?>
                <div class="success-card my-2">
                    <h2>Success!</h2>
                    <p>Your email address has now been verified, you can now login with your new email address.</p>
                </div>
            <?php endif; ?>
            <div class="main-cards">
                <h1>Edit Profile</h1>
                <h2><?= $r['user_name']; ?></h2>
                <div class="grid-row-2col">
                    <div class="grid-col">
                        <form action="profile.php" method="POST">
                            <input type="hidden" name="action" value="update">
                            <h3>Personal information</h3>
                            <div class="form-input-wrapper">
                                <label for=""><strong>Name</strong></label>
                                <input type="text" name="user_name" id="user_name" value="<?= $r['user_name']; ?>">
                            </div>
                            <div class="form-input-wrapper">
                                <label for=""><strong>Email</strong></label>
                                <input type="text" name="user_email" id="user_email" value="<?= $r['user_email']; ?>" required>
                                <p>If you change this, an email will be sent to your new address to confirm it. The new address will not become active until confirmed.</p>
                            </div>
                            <button class="btn-primary my-2">Save Changes</button>
                        </form>
                    </div>
                    <div class="grid-col">
                        <h3>Account Management</h3>
                        <h4>Change Password</h4>
                        <button class="btn-primary btn-secondary my-2" id="new-pw-form">Set Password</button>
                        <div class="pw-form d-none" id="pw-form">
                            <form action="profile.php" method="post">
                                <input type="hidden" name="action" value="pw">
                                <div class="form-input-wrapper">
                                    <label for="pw">Password</label>
                                    <input type="password" name="pw" id="pw" autocomplete="password" placeholder="New password">
                                    <p>You will be asked to confirm this by email</p>
                                </div>
                                <button class="btn-primary">Save Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Footer -->
        <?php include("./inc/footer.inc.php"); ?>
        <!-- /Footer -->
    </main>

    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>

</html>