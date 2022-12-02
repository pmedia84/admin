<nav class="nav-bar">
    <div class="nav-container">
        <div class="close">
            <button class="btn btn-close" id="nav-btn-close"></button>
        </div>
        <ul class="nav-links">
            <li ><a class="nav-link" href="index.php">Home <i class="fa-solid fa-house"></i></a></li>
            <li ><a class="nav-link" href="services.php">Services <i class="fa-solid fa-tags"></i></a></li>
            <li ><a class="nav-link" href="gallery.php">Image Gallery <i class="fa-solid fa-image"></i></a></li>
            <li ><a class="nav-link" href="news.php">News <i class="fa-solid fa-newspaper"></i></a></li>
            <?php if($user_type =="Admin"):?>
            <li ><a class="nav-link" href="users.php">Users <i class="fa-solid fa-users"></i></a></li>
            <?php endif;?>
            <?php if($user_type =="Admin"):?>
            <li ><a class="nav-link" href="settings.php">Settings <i class="fa-solid fa-gear"></i></a></li>
            <?php endif;?>
            <?php if($user_type =="Admin"):?>
            <li ><a class="nav-link" href="reviews.php">Reviews <i class="fa-solid fa-comment-dots"></i></a></li>
            <?php endif;?>
            <?php if($user_type =="Dev"):?>
            <li ><a class="nav-link" href="users.php"> <i class="fa-solid fa-users"></i></a></li>
            <?php endif;?>
            <!-- <li ><a class="nav-link" href="support.php">Support <img src="assets/img/icons/headset.svg" alt=""></a></li> -->
        </ul>
    </div>
</nav>