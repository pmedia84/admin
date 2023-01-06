<?php
session_start();
if (!$_SESSION['loggedin'] == true) {
    // Redirect to the login page:
    header('Location: login.php');
}
include("connect.php");
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
    echo $business_id;
}

//run checks to make sure a wedding has been set up correctly
if ($cms_type == "Wedding") {
    //look for the Wedding set up and load information
    //find Wedding details.
    $wedding = $db->prepare('SELECT * FROM wedding');

    $wedding->execute();
    $wedding->store_result();
    $wedding->bind_result($wedding_id, $wedding_name, $wedding_email, $wedding_phone, $wedding_contact_name);
    $wedding->fetch();
    $wedding->close();
    //set cms name
    $cms_name = $wedding_name;
    //find user details for this business
    $wedding_users = $db->prepare('SELECT users.user_id, users.user_name, wedding_users.wedding_id, wedding_users.user_type FROM users NATURAL LEFT JOIN wedding_users WHERE users.user_id=' . $user_id);

    $wedding_users->execute();
    $wedding_users->bind_result($user_id, $user_name, $wedding_id, $user_type);
    $wedding_users->fetch();
    $wedding_users->close();

    //find wedding events details
    $wedding_events_query = ('SELECT * FROM wedding_events ORDER BY event_time');
    $wedding_events = $db->query($wedding_events_query);
    $wedding_events_result = $wedding_events->fetch_assoc();
}

//////////////////////////////////////////////////////////////////Everything above this applies to each page\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
//image variable
$event_id = $_GET['event_id'];
//find image details

$event = $db->prepare('SELECT * FROM wedding_events WHERE event_id =' . $event_id);

$event->execute();
$event->store_result();


?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Create News Article</title>
<!-- /Page Title -->
</head>


<body>
    <!-- Main Body Of Page -->
    <main class="main col-2">
        <!-- Header Section -->
        <?php include("inc/header.inc.php");?>
        <!-- Nav Bar -->
        <?php include("./inc/nav.inc.php"); ?>
        <!-- /nav bar -->
        <section class="body">
            <div class="breadcrumbs mb-2">
                <a href="index.php" class="breadcrumb">Home</a> /
                <a href="events.php" class="breadcrumb">Events</a>
                <?php if ($_GET['action'] == "edit") : ?>
                    / Edit Event
                <?php endif; ?>
                <?php if ($_GET['action'] == "delete") : ?>
                    / Delete Event
                <?php endif; ?>
                <?php if ($_GET['action'] == "view") : ?>
                    / View Event
                <?php endif; ?>
            </div>
            <div class="main-cards">
                <?php if ($_GET['action'] == "edit") : ?>
                    <h1>Edit Event</h1>
                <?php endif; ?>
                <?php if ($_GET['action'] == "view") : ?>
                    <h1>View Event</h1>
                <?php endif; ?>
                <?php if ($_GET['action'] == "delete") : ?>
                    <h1>Delete Event</h1>
                <?php endif; ?>

                <?php if ($_GET['action'] == "edit") : ?>
                    <p class="font-emphasis">This page is best viewed on a large screen</p>
                <?php else : ?>
                <?php endif; ?>
                <?php if ($user_type == "Admin" ||$user_type=="Developer") : //detect if user is an admin or not 
                ?>
                    <?php if ($_GET['action'] == "delete") : //if action is delete, detect if the confirm is yes or no
                    ?>
                        <?php if ($_GET['confirm'] == "yes") : //if yes then delete the article
                        ?>
                            <?php if (($image->num_rows) > 0) :
                                $image->bind_result($image_id, $image_title, $image_description, $image_filename, $image_upload_date, $image_placement);
                                $image->fetch();
                                // connect to db and delete the record
                                $delete_image = "DELETE FROM images WHERE image_id=" . $image_id;
                                //delete image on server
                                $file =  "assets/img/gallery/" . $image_filename;

                                if (fopen($file, "w")) {
                                    unlink($file);
                                };
                                if (mysqli_query($db, $delete_image)) {
                                    echo '<div class="std-card"><div class="form-response error"><p>' . $image_title . ' Has Been Deleted</p></div></div>';
                                } else {
                                    echo '<div class="form-response error"><p>Error deleting image, please try again.</p></div>';
                                }
                            ?>
                            <?php else : ?>
                                <div class="std-card">
                                    <h2>Error</h2>
                                    <p>There has been an error, please return to the last page and try again.</p>
                                </div>
                            <?php endif; ?>
                        <?php else : //if not then display the message to confirm the user wants to delete the news article
                        ?>
                            <?php if (($image->num_rows) > 0) :
                                $image->bind_result($image_id, $image_title, $image_description, $image_filename, $image_upload_date, $image_placement);
                                $image->fetch();



                            ?>
                                <div class="std-card">
                                    <h2 class="text-alert">Delete: <?= $image_title; ?></h2>
                                    <p><?= $image_filename; ?></p>
                                    <img src="./assets/img/gallery/<?= $image_filename; ?>" alt="" class="delete-thumb my-3">
                                    <p>Are you sure you want to delete this image?</p>
                                    <p><strong>This Cannot Be Reversed</strong></p>
                                    <div class="button-section">
                                        <a class="btn-primary btn-delete my-2" href="image.php?action=delete&confirm=yes&image_id=<?= $image_id; ?>"><i class="fa-solid fa-trash"></i>Delete Image</a>
                                        <a class="btn-primary btn-secondary my-2" href="image.php?action=view&image_id=<?= $image_id; ?>"><i class="fa-solid fa-ban"></i>Cancel</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>



                    <?php endif; ?>

                    <?php if ($_GET['action'] == "edit") : ?>
                        <?php if (($image->num_rows) > 0) :
                            $image->bind_result($image_id, $image_title, $image_description, $image_filename, $image_upload_date, $image_placement);
                            $image->fetch();

                        ?>
                            <div class="std-card">
                                <form class="form-card" id="edit_image" action="scripts/gallery.script.php" method="POST" enctype="multipart/form-data">
                                    <div class="form-input-wrapper">

                                        <p><strong>File Name:</strong> <?= $image_filename; ?></p>
                                        <label for="image_title"><strong>Image Title</strong></label>
                                        <!-- input -->
                                        <input class="text-input input" type="text" name="image_title" id="image_title" placeholder="Image Title" required="" maxlength="45" value="<?= $image_title; ?>">
                                    </div>
                                    <div class="form-input-wrapper my-2">
                                        <img src="./assets/img/gallery/<?= $image_filename ?>" alt="">
                                    </div>
                                    <div class="form-input-wrapper my-2">
                                        <label for="image_description"><strong>Image Description</strong></label>
                                        <p class="form-hint-small">This is not essential, but can be useful.</p>
                                        <input class="text-input input" type="text" id="image_description" name="image_description" placeholder="Image Description" value="<?= $image_description; ?>">
                                    </div>
                                    <div class="my-2">

                                        <h2>Image Placement</h2>
                                        <p class="form-hint-small my-2">This determines where you image will be displayed on your website. You can use the same image in different locations if you wish.</p>
                                        <label class="checkbox-form-control" for="home">
                                            <input type="checkbox" id="home" name="img_placement[]" value="Home" <?php if (str_contains($image_placement, "Home")) : ?>Checked <?php endif; ?> />
                                            Home Screen
                                        </label>


                                        <label class="checkbox-form-control" for="gallery">
                                            <input type="checkbox" id="gallery" name="img_placement[]" value="Gallery" <?php
                                                                                                                        if (str_contains($image_placement, "Gallery")) :
                                                                                                                        ?>Checked <?php endif; ?> />
                                            Photo Gallery
                                        </label>



                                    </div>
                                    <div class="button-section my-3">
                                        <button class="btn-primary form-controls-btn" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save Changes </button>
                                    </div>
                                    <div id="response" class="d-none">
                                        <p>Article Saved <img src="./assets/img/icons/check.svg" alt=""></p>
                                    </div>
                                </form>
                            </div>
                        <?php else : ?>
                            <div class="std-card">
                                <h2>Error</h2>
                                <p>There has been an error, please return to the last page and try again.</p>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($_GET['action'] == "view") : ?>
                        <?php if (($event->num_rows) > 0) :
                            $event->bind_result($event_id, $event_name, $event_location, $event_address, $event_date, $event_time, $event_notes, $event_capacity);
                            $event->fetch();
                            $event_time = strtotime($event_time);
                            $time = date('H:ia', $event_time);
                            $event_date = strtotime($event_date);
                            $date = date('D d M Y', $event_date);
                        ?>
                            <div class="event-card">
                                <h2 class="event-card-title mb-3"> <?= $event_name; ?></h2>
                                <div class="event-card-details my-3">
                                    <div class="event-card-item">
                                        <h4>Location</h4>
                                        <p><?= $event_location; ?></p>
                                    </div>
                                    <div class="event-card-item">
                                        <h4>Date</h4>
                                        <p><?= $date; ?></p>
                                    </div>
                                    <div class="event-card-item">
                                        <h4>Time</h4>
                                        <p><?= $time; ?></p>
                                    </div>
                                    <div class="event-card-item">
                                        <h4>Venue Capacity</h4>
                                        <p><?= $event_capacity; ?></p>
                                    </div>
                                </div>
                                <h4>Address</h4>
                                <address class="my-2"><?= $event_address; ?></address>
                                <?php
                                echo '<iframe frameborder="0" width="100%" height="250px" src="https://maps.google.com/maps?f=q&source=s_q&hl=en&geocode=&q=' . str_replace(",", "", str_replace(" ", "+", $event_address)) . '&z=14&output=embed"></iframe>'; ?>

                                <h4>Event Notes</h4>
                                <p><?=$event_notes;?></p>

                                </p>

                                <div class="event-card-guestlist">
                                    <?php 
                                    //load all invites details
                                    $guest_allocated_query = ('SELECT invite_id FROM invitations  WHERE event_id='.$event_id);
                                    $invites = $db->query($guest_allocated_query);
                                    $guests_allocated = $invites->num_rows;
                                    //find additional invites
                                    $extra_invites_query = ('SELECT guest_list.guest_id, SUM(guest_list.guest_extra_invites) AS extra_inv, invitations.guest_id FROM guest_list NATURAL LEFT JOIN invitations WHERE guest_id=invitations.guest_id');
                                    $extra_invites = $db->query($extra_invites_query);
                                    $extra_inv = $extra_invites->fetch_array();
                                    $total_inv = $extra_inv['extra_inv'];
                                    //
                                    $invites_sent = ('SELECT invite_id FROM invitations  WHERE event_id='.$event_id.' AND invite_status="Sent"');
                                    $invites = $db->query($invites_sent);
                                    $invites_sent = $invites->num_rows;
                                    ?>
                                    <h4>Invite Details</h4>
                                    <p>Note that the figures below also include guests that can bring others with them.</p>
                                    <div class="event-card-invites">
                                        <div class="event-card-invites-textbox">
                                            <p>Invites Available </p><span><?=$event_capacity - $total_inv -$guests_allocated;?></span>
                                        </div>
                                        <div class="event-card-invites-textbox">
                                            <?php  
                                            ?>
                                            <p>Invites Sent </p><span><?=$invites_sent;?></span>
                                        </div>

                                        <div class="event-card-invites-textbox">
                                            <p>Guests Allocated </p><span><?=$total_inv;?></span>
                                        </div>
                                    </div>

                                    <h4>Guest List</h4>
                                    <table class="event-card-guestlist-table ">
                                        <?php
                                        $guest_list_query = ('SELECT guest_list.guest_id, guest_list.guest_fname, guest_list.guest_sname, guest_list.guest_extra_invites, invitations.event_id, invitations.guest_id, invitations.invite_status, invitations.invite_rsvp_status FROM guest_list NATURAL LEFT JOIN invitations WHERE guest_list.guest_id = invitations.guest_id AND event_id='.$event_id);
                                        $guest_list = $db->query($guest_list_query);
                                        ?>
  
                                        <tr>
                                            <th>Name</th>
                                            <th>Invited</th>
                                            <th>RSVP Status</th>
                                        </tr>
                                        <?php foreach($guest_list as $guest):
                                                    if($guest['guest_extra_invites']>=1){
                                                        $plus= "+".$guest['guest_extra_invites'];
                                                    }else{
                                                        $plus="";
                                                    }    
                                        ?>
                                            <tr>
                                                <td><a href="guest.php?action=view&guest_id=<?=$guest['guest_id'];?>"><?=$guest['guest_fname']." ".$guest['guest_sname'].' '.$plus;?></a></td>
                                                <td><?=$guest['invite_status'];?></td>
                                                <td><?=$guest['invite_status'];?></td>
                                            </tr>
                                        <?php endforeach;?> 


                                    </table>
                                    <a class="btn-primary" href="event.php?action=assign">Assign Guests</a>
                                </div>
                                <div class="card-actions">
                                    <a class="my-2" href="event.php?action=edit&event_id=<?= $event_id; ?>"><i class="fa-solid fa-pen-to-square"></i> Edit Event </a>
                                    <a class="my-2" href="event.php?action=delete&event_id=<?= $event_id; ?>"><i class="fa-solid fa-trash"></i> Delete Event</a>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="std-card">
                                <h2>Error</h2>
                                <p>There has been an error, please return to the last page and try again.</p>
                            </div>
                        <?php endif; ?>
            </div>

            </div>



            </div>
        <?php endif; ?>

    <?php else : ?>
        <p class="font-emphasis">You do not have the necessary Administrator rights to view this page.</p>
    <?php endif; ?>
    </div>

        </section>


    </main>

    <!-- /Main Body Of Page -->
    <!-- Quote request form script -->

    <!-- /Quote request form script -->
    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->
    <script>
        $(".nav-btn").click(function() {
            $(".nav-bar").fadeToggle(500);
        });
        $(".btn-close").click(function() {
            $(".nav-bar").fadeOut(500);
        })
    </script>
    <script>
        //script for editing a news article
        $("#edit_image").submit(function(event) {

            event.preventDefault();
            //declare form variables and collect GET request information
            image_id = '<?php echo $image_id; ?>';
            var formData = new FormData($("#edit_image").get(0));
            formData.append("action", "edit");
            formData.append("image_id", image_id);
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/gallery.script.php",
                data: formData,
                contentType: false,
                processData: false,
                success: function(data, responseText) {
                    $("#response").html(data);
                    $("#response").slideDown(400);
                    window.location.replace('image.php?action=view&image_id=' + image_id);
                }
            });

        });
    </script>
</body>

</html>