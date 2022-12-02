<?php


include("connect.php");
//look for a business setup in the db, if not then direct to the setup page
//find news articles
$business_query = ('SELECT business_id FROM business');
$business = $db->query($business_query);
if($business -> num_rows ==0){
    header('Location: setup.php');
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
            <h1>Please Login</h1>
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
    <!-- Quote request form script -->

    <!-- /Quote request form script -->
    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->
    <script>
        $("#login").submit(function(event) {
            event.preventDefault();
            const data = new FormData(event.target); //declare form data
            const values = Object.fromEntries(data.entries()); //select data
            console.log(values);
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/auth.php",
                data: values,
                encode: true,
                success: function(data, responseText) {
                    $("#response").html(data);
                    $("#response").slideDown(400);
                    if (data === 'correct') {
                        window.location.replace('index.php');
                    }

                }
            });
        })
    </script>
</body>

</html>