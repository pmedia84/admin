<?php


include("connect.php");
//look for a business setup in the db, if not then direct to the setup page
$business_query = ('SELECT business_id FROM business');
$business = $db->query($business_query);
if($business -> num_rows ==0){
    header('Location: setup.php?action=setup_business');
    exit();
}

//check if there are users for the business
$users_query = ('SELECT user_id, business_id FROM users');
$users = $db->query($users_query);
if($users -> num_rows ==0){
    header('Location: setup.php?action=check_users');
    exit();
}
session_start();

?>
<?php include("./inc/header.inc.php"); ?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Dashboard</title>
<!-- /Page Title -->
</head>

<body>


    <!-- Main Body Of Page -->



    <main class="main login-main">
    <div class="header">

<div class="header-actions login-header">
    <img src="assets/img/logo.png" alt="">
</div>
</div>
        <div class="login-wrapper">
            <h1>Login</h1>
            <form class="form-card" id="login" action="scripts/auth.php" method="post">
                <div class="form-input-wrapper">
                    <label for="user_email">eMail Address:</label>
                    <!-- input -->
                    <input  type="text" name="user_email" id="user_email" placeholder="Enter Email Address" autocomplete="email" required="" maxlength="45">
                </div>

                <div class="form-input-wrapper">
                    <label for="password">Password:</label>
                    <!-- input -->
                    <input class="text-input input" type="password" name="password" id="password" placeholder="Your Password*" autocomplete="current-password" required="" maxlength="45">
                </div>

                <div class="button-section my-3">
                    <button class="btn-primary" type="submit">Login</button>
                </div>
                <div id="response" class="d-none">
                </div>
            </form>
        </div>







    </main>
    <!-- /Main Body Of Page -->

    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->
    <script>
        $("#login").submit(function(event) {
            event.preventDefault();
            var formData = new FormData($("#login").get(0));
            var user_email = $("#user_email").val();
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/auth.php",
                data: formData,
                contentType: false,
                processData: false,
                success: function(data, responseText) {
                    $("#response").html(data);
                    $("#response").slideDown(400);
                    if (data === 'correct') {
                        window.location.replace('index.php');
                    }
                    if (data === 'TEMP') {
                        window.location.replace('resetpw.php?action=temp&user_email='+user_email);
                    }

                }
            });
        })
    </script>
</body>

</html>