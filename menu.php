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
if ($cms_type == "Business") {
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

//load menu
if (empty($_GET)) {
    $menu_query = $db->query('SELECT menu.menu_name, menu.menu_id, menu.event_id, wedding_events.event_id, wedding_events.event_name FROM menu LEFT JOIN wedding_events ON wedding_events.event_id=menu.event_id');
}
if (isset($_GET['action']) && $_GET['action'] == "edit") {
    $menu_query = $db->query('SELECT menu.menu_name, menu.menu_id, menu.event_id, wedding_events.event_id, wedding_events.event_name FROM menu LEFT JOIN wedding_events ON wedding_events.event_id=menu.event_id WHERE menu.menu_id=' . $_GET['menu_id']);
    $menu_result = mysqli_fetch_assoc($menu_query);
    $menu_courses = $db->query('SELECT course_name, course_id FROM menu_courses');
    $course_res = mysqli_fetch_assoc($menu_courses);
}
if (isset($_GET['action']) && $_GET['action'] == "delete") {
    $menu_query = $db->query('SELECT menu.menu_name, menu.menu_id, menu.event_id, wedding_events.event_id, wedding_events.event_name FROM menu LEFT JOIN wedding_events ON wedding_events.event_id=menu.event_id WHERE menu.menu_id=' . $_GET['menu_id']);
    $menu_result = mysqli_fetch_assoc($menu_query);
}
?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- Page Title -->
<title>Mi-Admin | Menu Builder</title>
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
                    <h1><i class="fa-solid fa-bowl-food"></i> Menu Builder</h1>
                    <p>Here you can create a menu for your events, this will allow guests to see what food is available or where they can make choices.</p>
                <?php endif; ?>
                <?php if (isset($_GET['action']) && $_GET['action'] == "edit" && $menu_builder_status == "On") : ?>
                    <h1><i class="fa-solid fa-bowl-food"></i> Edit Menu for your <?= $menu_result['event_name']; ?></h1>
                <?php endif; ?>
                <?php if (isset($_GET['action']) && $_GET['action'] == "delete" && $menu_builder_status == "On") : ?>
                    <h1><i class="fa-solid fa-bowl-food"></i> Delete Menu for your <?= $menu_result['event_name']; ?></h1>
                <?php endif; ?>
                <?php if ($user_type == "Admin" || $user_type == "Developer") : ?>
                    <?php if ($menu_builder_status == "On") : ?>
                        <div class="menu-body" id="menu-body">
                            <?php if (empty($_GET)) :
                                $event_query = $db->query('SELECT * FROM wedding_events');
                            ?>
                                <div class="std-card">
                                    <div class="form-controls my-2">
                                        <button class="btn-primary" type="button" id="add-menu" data-action="create_menu"><i class="fa-solid fa-utensils"></i> Create Menu</button>
                                    </div>
                                    <?php if ($menu_query->num_rows > 0) : ?>
                                        <?php foreach ($menu_query as $menu) :
                                            $menu_query = $db->query('SELECT menu.menu_name, menu.menu_id, menu.event_id, wedding_events.event_id, wedding_events.event_name FROM menu LEFT JOIN wedding_events ON wedding_events.event_id=menu.event_id WHERE menu.menu_id=' . $menu['menu_id']);
                                            $menu_result = mysqli_fetch_assoc($menu_query);
                                            $menu_courses = $db->query('SELECT course_name, course_id FROM menu_courses');
                                            $course_res = mysqli_fetch_assoc($menu_courses); ?>

                                            <?php if ($menu_query->num_rows > 0) : ?>
                                                <div class="menu my-3" id="menus">
                                                    <h2><?= $menu_result['menu_name']; ?></h2>
                                                    <p>For your</p>
                                                    <p><?= $menu['event_name']; ?></p>
                                                    <hr>
                                                    <?php
                                                    if ($menu_courses->num_rows > 0) :
                                                        foreach ($menu_courses as $course) :
                                                            $menu_item = $db->query('SELECT menu_item_id, menu_item_name, menu_item_desc, course_id, menu_id FROM menu_items WHERE course_id=' . $course['course_id'] . ' AND menu_id=' . $menu['menu_id']); ?>
                                                            <h3><?= $course['course_name']; ?></h3>
                                                            <?php if ($menu_item->num_rows > 0) :
                                                                foreach ($menu_item as $item) :  ?>
                                                                    <div class="menu-item my-2">
                                                                        <div class="menu-item-body">
                                                                            <h4 class="menu-item-name"><?= $item['menu_item_name']; ?></h4>
                                                                            <p class="menu-item-desc"><?= $item['menu_item_desc']; ?></p>
                                                                        </div>
                                                                    </div>
                                                <?php endforeach;
                                                            endif;
                                                            echo "<hr>";
                                                        endforeach;
                                                    endif;
                                                endif;
                                                ?>
                                                <div class="card-actions">
                                                    <a class="btn-primary" href="menu?action=edit&menu_id=<?= $menu['menu_id']; ?>"><i class="fa-solid fa-pen-to-square"></i> Edit Menu</a>
                                                    <a href="menu.php?action=delete&confirm=no&menu_id=<?= $menu['menu_id']; ?>" class="btn-primary btn-secondary"><i class="fa-solid fa-trash"></i> Delete Menu</a>
                                                </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php $db->close();
                                    endif; ?>
                                </div>
                                <div class="modal" id="menu-modal">
                                    <div class="modal-body">
                                        <div class="modal-content">
                                            <h2 class="text-center">Create a New Menu</h2>
                                            <?php if ($event_query->num_rows > 0) : ?>
                                                <?php if ($menu_courses->num_rows > 0) : ?>
                                                    <form class="my-2" action="menu.script.php" method="POST" id="create-menu" data-action="new_menu">
                                                        <div id="dish-creator">
                                                            <div class="form-input-wrapper">
                                                                <label for="menu_name">Menu Name</label>
                                                                <input type="text" name="menu_name" id="menu_name" placeholder="Wedding Breakfast..." required>
                                                            </div>
                                                            <div class="form-input-wrapper">
                                                                <label for="event_id">Select Event This Menu Is For</label>
                                                                <select name="event_id" id="event_id" required>
                                                                    <option value="">Select Event</option>
                                                                    <?php foreach ($event_query as $event) : ?>
                                                                        <option value="<?= $event['event_id']; ?>"><?= $event['event_name']; ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="button-section my-3 modal-btns">
                                                            <button class="btn-primary" id="save-menu" type="submit">Save Menu</button>
                                                            <button class="btn-primary btn-secondary" type="button" id="close-menu-modal">Close</button>
                                                        </div>
                                                        <div class="d-none" id="response">
                                                        </div>
                                                    </form>
                                                <?php else : ?>
                                                    <p><strong>Before you continue, you need to set up courses for your menu.</strong></p>
                                                    <div class="button-section my-3">
                                                        <button class="btn-primary btn-secondary" id="close-modal" type="button">Close</button>
                                                    </div>
                                                <?php endif; ?>
                                            <?php else : ?>
                                                <p>You have no events set up, please create an event first</p>
                                                <a href="event.php?action=create" class="btn-primary my-3">Create Events</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($_GET['action']) && $_GET['action'] == "edit") : ?>

                                <div class="form-controls my-2">
                                    <button class="btn-primary" type="button" data-menu_id="<?= $_GET['menu_id']; ?>" data-action="add_dish" id="add-dish"><i class="fa-solid fa-utensils"></i> Add Dish</button>
                                    <a href="menu?action=delete&confirm=no&menu_id=<?= $_GET['menu_id']; ?>" class="btn-primary btn-secondary" type="button" data-menu_id="<?= $_GET['menu_id']; ?>" data-action="delete_menu"><i class="fa-solid fa-trash"></i> Delete Menu</a>
                                    <button class="btn-primary btn-secondary" id="edit-courses" type="button" data-menu_id="<?= $_GET['menu_id']; ?>"><i class="fa-solid fa-pen-to-square"></i> Edit Courses</button>
                                    <a href="menu" class="btn-primary btn-secondary"><i class="fa-solid fa-xmark"></i> Cancel Editing Menu</a>
                                </div>
                                <div class="std-card" id="menu">
                                    <?php if ($menu_query->num_rows > 0) : ?>
                                        <p class="text-center">To edit the menu name: Click or tap on the menu name itself.</p>
                                        <div class="menu my-3">
                                            <h2 class="menu_name_edit" contenteditable="true" data-menu_id="<?=$menu_result['menu_id'];?>" data-action="edit_menu_name"><?= $menu_result['menu_name']; ?></h2>
                                            <hr>
                                            <?php
                                            if ($menu_courses->num_rows > 0) :
                                                foreach ($menu_courses as $course) :
                                                    $menu_item = $db->query('SELECT menu_item_id, menu_item_name, menu_item_desc, course_id, menu_id FROM menu_items WHERE course_id=' . $course['course_id'] . ' AND menu_id=' . $_GET['menu_id']); ?>
                                                    <h3><?= $course['course_name']; ?></h3>
                                                    <?php if ($menu_item->num_rows > 0) :
                                                        foreach ($menu_item as $item) :  ?>
                                                            <div class="menu-item my-2">
                                                                <div class="menu-item-body">
                                                                    <h4 class="menu-item-name"><?= $item['menu_item_name']; ?></h4>
                                                                    <p class="menu-item-desc"><?= $item['menu_item_desc']; ?></p>
                                                                </div>
                                                                <div class="menu-item-actions">
                                                                    <button class="btn-primary btn-secondary btn-delete delete-dish" type="button" data-dish_id="<?= $item['menu_item_id']; ?>" data-menu_id="<?= $_GET['menu_id']; ?>" data-action="delete_dish"><i class="fa-solid fa-xmark"></i></button>
                                                                    <button class="btn-primary btn-secondary edit-dish" data-dish_id="<?= $item['menu_item_id']; ?>" data-menu_id="<?= $_GET['menu_id']; ?>"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                                                                </div>

                                                            </div>
                                            <?php endforeach;
                                                    endif;
                                                    echo "<hr>";
                                                endforeach;
                                            endif;
                                            $db->close(); ?>
                                        </div>
                                </div>
                                <div class="modal" id="course-modal">
                                    <div class="modal-body">
                                        <div class="modal-content">
                                            <h2 class="text-center">Edit Menu Courses</h2>
                                            <form class="my-2" action="menu.script.php" method="POST" id="courses-editor">
                                                <div id="course-editor">
                                                    <?php if ($menu_courses->num_rows > 0) :
                                                        $index = 0;
                                                    ?>
                                                        <?php foreach ($menu_courses as $course) : ?>
                                                            <div class="form-input-wrapper">
                                                                <label for="course_name">Course Name</label>
                                                                <input type="hidden" name="course[<?= $index; ?>][course_id]" value="<?= $course['course_id']; ?>">
                                                                <div class="form-input-row"><input type="text" name="course[<?= $index; ?>][course_name]" value="<?= $course['course_name']; ?>"><button class="btn-primary btn-secondary btn-delete" type="button" data-course_id="<?= $course['course_id']; ?>" data-action="delete"><i class="fa-solid fa-xmark"></i></button></div>
                                                            </div>
                                                        <?php $index++;
                                                        endforeach; ?>
                                                        <div id="form-row"></div>
                                                        <button class="btn-primary btn-secondary my-2" id="add-course" type="button"><i class="fa-solid fa-plus"></i> Add Course</button>
                                                        <p><strong>Note:</strong> Removing a course will also remove any dishes associated with it.</p>
                                                        <div class="button-section my-3 modal-btns">
                                                            <button class="btn-primary" type="button" id="courses-save" data-action="save">Save Changes</button>
                                                            <button class="btn-primary btn-secondary" id="close-course-modal" type="button">Cancel</button>
                                                        </div>

                                                    <?php else : ?>
                                                        <?php $index = 0; ?>
                                                        <div id="form-row"></div>
                                                        <button class="btn-primary btn-secondary my-2" id="add-course" type="button"><i class="fa-solid fa-plus"></i> Add Course</button>
                                                        <div class="button-section my-3 modal-btns">
                                                            <button class="btn-primary" type="button" id="courses-save" data-action="save">Save Changes</button>
                                                            <button class="btn-primary btn-secondary" id="close-course-modal" type="button">Cancel</button>
                                                        </div>
                                                        <p><strong>Note:</strong> Removing a course will also remove any dishes associated with it.</p>
                                                        <?php $index++; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal" id="dish-modal">
                                    <div class="modal-body">
                                        <div class="modal-content">
                                            <h2 class="text-center">Add Dish</h2>
                                            <?php if ($menu_courses->num_rows > 0) : ?>
                                                <form class="my-2" action="menu.script.php" method="POST" id="create-dish">
                                                    <div id="dish-creator">
                                                        <div class="form-input-wrapper">
                                                            <label for="menu_item_name">Dish Name</label>
                                                            <input type="text" name="menu_item_name" id="menu_item_name" placeholder="Slow Roast Beef..." required>
                                                        </div>
                                                        <div class="form-input-wrapper">
                                                            <label for="menu_item_desc">Dish Description</label>
                                                            <input type="text" name="menu_item_desc" id="menu_item_desc" placeholder="A description to entice your guests" required>
                                                        </div>
                                                        <div class="form-input-wrapper">
                                                            <label for="course_id">Select Course</label>
                                                            <select name="course_id" id="course_id" required>
                                                                <option value="">Select Course</option>
                                                                <?php foreach ($menu_courses as $course) : ?>
                                                                    <option value="<?= $course['course_id']; ?>"><?= $course['course_name']; ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="button-section my-3 modal-btns">
                                                        <button class="btn-primary" id="save-dish" data-action="save_dish" data-menu_id="<?= $_GET['menu_id']; ?>" type="submit">Save Dish</button>
                                                        <button class="btn-primary btn-secondary" type="button" id="close-modal">Close</button>
                                                    </div>
                                                    <div class="d-none" id="response">
                                                    </div>
                                                </form>
                                            <?php else : ?>
                                                <p><strong>Before you continue, you need to set up courses for your menu.</strong></p>
                                                <div class="button-section">
                                                    <button class="btn-primary btn-secondary" id="close-modal" type="button">Close</button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal" id="edit-dish-modal">

                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['action']) && $_GET['action'] == "delete") : ?>
                        <?php if ($_GET['confirm'] == "no") : ?>
                            <div class="std-card">
                                <?php if ($menu_query->num_rows > 0) : ?>
                                    <div class="menu my-3">
                                        <h2>Delete Your <?= $menu_result['menu_name']; ?> Menu?</h2>
                                        <p>For</p>
                                        <p><?= $menu_result['event_name']; ?></p>
                                        <p><strong>Note:</strong> This is not reversible!</p>
                                        <div class="card-actions">
                                            <a href="menu.php?action=delete&confirm=yes&menu_id=<?= $_GET['menu_id']; ?>" class="btn-primary btn-delete">Confirm</a>
                                            <a href="menu.php" class="btn-primary btn-secondary">Cancel</a>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <h1>Error</h1>
                                    <p>Request error</p>
                                <?php endif; ?>
                            <?php endif; ?>


                            </div>
                        <?php endif; ?>
            </div>
        <?php else : ?>
            <div class="std-card">
                <h2>Menu Builder</h2>
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

    <script src="assets/js/menu.js"></script>
    <?php if (isset($_GET['action']) && $_GET['action'] == "edit") : ?>
        <script>
            let index = <?= $index; ?>;
            $("#course-editor").on("click", "#add-course", function() {

                let input = "<div class='appended d-none'><div class='form-input-wrapper'><label for='course_name'>Course Name</label><input type='hidden' name='course[" + index + "][course_id]' value=''><div class='form-input-row'><input type='text' name='course[" + index + "][course_name]'><button class='btn-primary btn-secondary btn-delete' type='button'><i class='fa-solid fa-xmark'></i></button></div></div>";
                $("#form-row").append(input);
                $(".appended").slideDown(400);
                index++;
            })
        </script>
    <?php endif; ?>
</body>

</html>