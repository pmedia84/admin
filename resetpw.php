<?php

session_start();

?>
<?php include("./inc/header.inc.php"); 
include("./connect.php");?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Reset Password</title>
<!-- /Page Title -->
</head>

<body>


    <!-- Main Body Of Page -->



    <main class="login-main">
    <div class="header">

<div class="header-actions login-header">
    <img src="assets/img/logo.png" alt="">
</div>
</div>
        <div class="login-wrapper">
            
           <?php 
            if(empty($_GET['user_id'])):
           ?>
           <h1>Reset Password</h1>
            <p class="font-emphasis mb-3">Need to reset your password? Enter your email address below and we will email you a password reset link.</p>
            <form class="form-card" id="requestpwreset" action="scripts/resetpw-script.php" method="post">
                <div class="form-input-wrapper">
                    <label for="user_email">eMail Address:</label>
                    <!-- input -->
                    <input  type="text" name="user_email" id="user_email" placeholder="Enter Email Address" autocomplete="email" required="" maxlength="45">
                </div>
                <div class="button-section my-3">
                    <button class="btn-primary" type="submit">Request Link</button>
                </div>
                <div id="response" class="d-none">
                </div>
            </form>
            
            <?php else:
                
                //find username and email address to display on screen.
                $user = $db->prepare('SELECT user_id, user_email, user_name FROM users WHERE user_id = ?');
                $user->bind_param('s', $_GET['user_id']);
                $user->execute();
                $user->store_result();
                $user->bind_result($user_id, $email, $name);
                $user->fetch();
                $user->close();
                ?>
                 
                <h1>Reset Password</h1>
                <p class="font-emphasis">You can now change your password:</p>
                <p class="font-emphasis"><strong>Name: </strong><?=$name;?></p>
                <p class="font-emphasis mb-3"><strong>Email address: </strong><?=$email;?></p>
            <form class="form-card" id="resetpw" action="scripts/resetpw-script.php" method="post">
                <div class="form-input-wrapper">
                    <label for="new_pw">New Password</label>
                    <!-- input -->
                    <input  type="password" name="new_pw" id="new_pw" placeholder="Enter New Password" autocomplete="password" required="" maxlength="45">
                </div>
                <div class="form-input-wrapper">
                    
                    <label for="new_pw2">Re Enter New Password</label>
                    <!-- input -->
                    <input  type="password" name="new_pw2" id="new_pw2" placeholder="Enter New Password" autocomplete="password" required="" maxlength="45">
                </div>
                <div class="button-section my-3">
                    <button class="btn-primary" type="submit">Reset Password</button>
                </div>
                <div id="response" class="d-none">
                </div>
            </form>
            <?php endif; ?>
        </div>
    </main>
    <!-- /Main Body Of Page -->
    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->
    <script>
        //script for requesting password reset
        $("#requestpwreset").submit(function(event) {
            event.preventDefault();
            //declare form variables and collect GET request information

            
            action = 'requestreset';
            //collect form data and GET request information to pass to back end script
            var formdata= {
                action,
                user_email:$("#user_email").val()
            }
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/resetpw-script.php",
                data: formdata,
                encode: true,
                success: function(data, responseText) {
                    $("#response").html(data);
                    $("#response").slideDown(400);
                    if (data === 'correct') {
                        console.log("success");
                    }

                }
            });
        });     
    </script>
    <script>
                //script for password reset
                $("#resetpw").submit(function(event) {
            event.preventDefault();
            //declare form variables and collect GET request information
            key = '<?php echo $_GET['key'];?>';
            user_id = '<?php echo $_GET['user_id'];?>';
            action = '<?php echo $_GET['action'];?>';
            //collect form data and GET request information to pass to back end script
            var formdata= {
                key,
                user_id,
                action,
                pw1:$("#new_pw").val(),
                pw2:$("#new_pw2").val(),
            }
            //send as an AJAX POST
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/resetpw-script.php",
                data: formdata ,
                encode: true,
                success: function(data, responseText) {
                    $("#response").html(data);
                    $("#response").slideDown(400);
                }
            });
        });
    </script>
</body>
</html>