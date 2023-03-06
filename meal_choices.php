<?php
session_start();

include("./connect.php");
//handle deleting menu, only process if confirm is yes, re direct to menu page
if (isset($_GET['confirm']) && $_GET['confirm'] == "yes") {

    $delete_menu = "DELETE FROM menu WHERE menu_id=" . $_GET['menu_id'];
    if (mysqli_query($db, $delete_menu)) {
        header("Location: menu");
        exit();
    }
}
$location = $_SERVER['REQUEST_URI'];
$location = urlencode($_SERVER['REQUEST_URI']);
if (!$_SESSION['loggedin'] == TRUE) {
    // Redirect to the login page:

    header("Location: login.php?location=" . $location);
}

include("inc/head.inc.php");
include("inc/settings.php");
////////////////Find details of the cms being used, on every page\\\\\\\\\\\\\\\
//Variable for name of CMS
//wedding is the name of people
//business name
$cms_name = "";
$user_id = $_SESSION['user_id'];


//run checks to make sure a wedding has been set up correctly
if ($cms_type == "Wedding") {
    //look for the Wedding set up and load information
    //find Wedding details.
    $wedding = $db->prepare('SELECT * FROM wedding');

    $wedding->execute();
    $wedding->store_result();
    $wedding->bind_result($wedding_id, $wedding_name, $wedding_date, $wedding_time,  $wedding_email, $wedding_phone, $wedding_contact_name);
    $wedding->fetch();
    $wedding->close();
    //set cms name
    $cms_name = $wedding_name;
    //find user details for this business
    $business_users = $db->prepare('SELECT users.user_id, users.user_name, wedding_users.wedding_id, wedding_users.user_type FROM users NATURAL LEFT JOIN wedding_users WHERE users.user_id=' . $user_id);

    $business_users->execute();
    $business_users->bind_result($user_id, $user_name, $business_id, $user_type);
    $business_users->fetch();
    $business_users->close();
}


//////////////////////////////////////////////////////////////////Everything above this applies to each page\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
//select the orders first
$choices_query = $db->query('SELECT meal_choice_order.choice_order_id, meal_choice_order.guest_id, guest_list.guest_fname, guest_list.guest_id, guest_list.guest_sname FROM meal_choice_order LEFT JOIN guest_list ON guest_list.guest_id=meal_choice_order.guest_id');
?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->
<!-- Page Title -->
<title>Mi-Admin | Guest Meal Choices</title>
<!-- /Page Title -->
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
            <div class="breadcrumbs mb-2">
                <a href="index.php" class="breadcrumb">Home</a> /
                <?php
                if (isset($_GET['action'])) {
                    switch ($_GET['action']) {
                        case $_GET['action'] == "edit":
                            echo "<a href='menu'>Menu Builder</a> / Edit Menu";
                            break;
                        case $_GET['action'] == "delete":
                            echo "<a href='menu'>Menu Builder</a> / Delete Menu";
                            break;
                    }
                } else {
                    echo "Menu Builder";
                }

                ?>
            </div>
            <div class="main-cards">
                <?php if (empty($_GET)) : ?>
                    <h1><i class="fa-solid fa-utensils"></i> Guest Meal Choices</h1>
                    <p>This page will update as your guests let you know what their choices are from your menu.</p>
                    <p>Once all your guests have given you their choices, you will be able to download or print to a PDF and send to your venue.</p>
                <?php endif; ?>

                <?php if ($user_type == "Admin" || $user_type == "Developer") : ?>
                    <?php if ($meal_choices_status == "On") :
                        $choices_totals = $db->query('SELECT meal_choices.menu_item_id, menu_items.menu_item_id, menu_items.menu_item_name, COUNT(meal_choices.choice_id) AS numberOfChoices FROM meal_choices LEFT JOIN menu_items ON menu_items.menu_item_id=meal_choices.menu_item_id GROUP BY menu_item_name');

                    ?>
                        <div class="std-card form-controls my-2">
                            <a href="" class="btn-primary"><i class="fa-solid fa-file-csv"></i> Download Meal Choices</a>
                            <a href="" class="btn-primary"><i class="fa-solid fa-file-pdf"></i> Print Meal Choices</a>
                        </div>
                        <div class="std-card">
                            <h2 class="my-2">Meal Choice Totals</h2>
                            <div class="grid-row-3col">
                                <?php foreach ($choices_totals as $choice_total) : ?>
                                    <div class="choice-stats-card">
                                        <p><?= $choice_total['menu_item_name']; ?></p>
                                        <span><?= $choice_total['numberOfChoices']; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="std-card">
                            <h2>Meal Choices</h2>
                            <table class="meal-choices">
                                <tr>
                                    <th>Name</th>
                                    <th>Choices</th>
                                </tr>

                                <?php foreach ($choices_query as $guest) :
                                    $meal_choices = $db->query('SELECT meal_choices.menu_item_id, meal_choices.choice_order_id, menu_items.menu_item_name, menu_items.course_id, menu_courses.course_name, menu_courses.course_id FROM meal_choices LEFT JOIN menu_items ON menu_items.menu_item_id=meal_choices.menu_item_id LEFT JOIN menu_courses ON menu_courses.course_id=menu_items.course_id WHERE meal_choices.choice_order_id=' . $guest['choice_order_id'] . ' ORDER BY menu_courses.course_id');
                                ?>
                                    <tr>
                                        <td class="guest-name-col" rowspan="<?= $meal_choices->num_rows + 1; ?>"><a href="guest?action=view&guest_id=<?= $guest['guest_id']; ?>"><?= $guest['guest_fname'] . ' ' . $guest['guest_sname']; ?></a></td>
                                    </tr>

                                    <?php foreach ($meal_choices as $choice) : ?>
                                        <tr>
                                            <td><?= $choice['menu_item_name']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>

                                <?php endforeach; ?>
                            </table>
                        <?php else : ?>
                            <div class="std-card">
                                <h2>Guest Meal Choices</h2>
                                <p>This feature is not available to you. Please contact us to have this feature activated.</p>
                            </div>
                        <?php endif; ?>
                    <?php else : ?>
                        <p class="font-emphasis">You do not have the necessary Administrator rights to view this page.</p>
                    <?php endif; ?>
                        </div>
        </section>
    </main>

    <!-- /Main Body Of Page -->

    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->
    <script src="assets/js/meal_choices.js"></script>

</body>

</html>