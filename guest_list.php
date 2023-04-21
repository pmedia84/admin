<?php
session_start();
require("scripts/functions.php");
check_login();
include("connect.php");
include("inc/head.inc.php");
include("inc/settings.php");

//find wedding guest list
$guest_list_query = ('SELECT * FROM guest_list ORDER BY guest_sname');
$guest_list = $db->query($guest_list_query);
$guest_list_result = $guest_list->fetch_assoc();
//////////////////////////////////////////////////////////////////Everything above this applies to each page\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\


?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Guest List</title>
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
            <div class="breadcrumbs mb-2"><a href="index.php" class="breadcrumb">Home</a> / Guest List</div>
            <div class="main-cards">
                <?php if ($user->user_type() == "Admin" || $user->user_type() == "Developer") : ?>
                    <?php if ($cms->type() == "Wedding") : ?>
                        <h2><svg class="icon">
                                <use xlink:href="assets/img/icons/solid.svg#people-group"></use>
                            </svg> Your Guest List</h2>
                        <p>Keep this information up to date as you plan for big day. Your invites will be sent out from this information.</p>
                        <a href="guest.php?action=create" class="btn-primary">Add Guest <svg class="icon">
                                <use xlink:href="assets/img/icons/solid.svg#user-plus"></use>
                            </svg></a>
                        <div class="search-controls">
                            <form id="guest_search" action="./scripts/guest_list.script.php" method="POST">
                                <div class="form-input-wrapper">
                                    <label for="search">Search by guest name</label>
                                    <div class="search-input">
                                        <input type="text" id="search" name="search" placeholder="Search For A Guest ...">
                                        <button class="btn-primary form-controls-btn loading-btn" type="submit"><svg class="icon">
                                                <use xlink:href="assets/img/icons/solid.svg#magnifying-glass"></use>
                                            </svg></button>
                                    </div>
                                </div>
                            </form>

                        </div>

                        <div class="std-card d-none" id="guest_list">

                        </div>



                    <?php endif; ?>
                <?php else : ?>
                    <p class="font-emphasis">You do not have the necessary Administrator rights to view this page.</p>
                <?php endif; ?>
            </div>

        </section>


    </main>
    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->

</body>
<script>
    $(document).ready(function() {
        url = "scripts/guest_list.script.php?action=load_guest_list";
        $.ajax({ //load guest list
            type: "GET",
            url: url,
            encode: true,
            success: function(data, responseText) {
                $("#guest_list").html(data);
                $("#guest_list").fadeIn(500);
            }
        });
    })
</script>
<script>
    //script for searching for guests
    $("#guest_search").submit(function(event) {
        event.preventDefault();
        var formData = new FormData($("#guest_search").get(0));
        formData.append("action", "guest_search");
        $.ajax({ //start ajax post
            type: "POST",
            url: "scripts/guest_list.script.php",
            data: formData,
            contentType: false,
            processData: false,
            success: function(data, responseText) {
                $("#guest_list").html(data);
                $("#guest_list").fadeIn(500);
            }
        });

    });
    //script for searching for guests
    $("#guest_search").on('keyup', function(event) {
        event.preventDefault();
        var formData = new FormData($("#guest_search").get(0));
        formData.append("action", "guest_search");

        $.ajax({ //start ajax post
            type: "POST",
            url: "scripts/guest_list.script.php",
            data: formData,
            contentType: false,
            processData: false,
            success: function(data, responseText) {
                $("#guest_list").html(data);
                $("#guest_list").fadeIn(500);
            }
        });

    });
</script>

</html>