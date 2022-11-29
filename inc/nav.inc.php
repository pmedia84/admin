<nav class="nav-bar">
    <div class="nav-container">
        <div class="close">
            <button class="btn btn-close" id="nav-btn-close"></button>
        </div>
        <ul class="nav-links">
            <li ><a class="nav-link" href="index.php">Home <img src="assets/img/icons/home.svg" alt=""></a></li>
            <li ><a class="nav-link" href="">Services <img src="assets/img/icons/tags.svg" alt=""></a></li>
            <li ><a class="nav-link" href="gallery.php">Image Gallery <img src="assets/img/icons/image.svg" alt=""></a></li>
            <li ><a class="nav-link" href="news.php">News <img src="assets/img/icons/newspaper.svg" alt=""></a></li>
            <?php if($user_type =="Admin"):?>
            <li ><a class="nav-link" href="users.php">Users <img src="assets/img/icons/users.svg" alt=""></a></li>
            <?php endif;?>
            <?php if($user_type =="Admin"):?>
            <li ><a class="nav-link" href="settings.php">Settings <img src="assets/img/icons/settings.svg" alt=""></a></li>
            <?php endif;?>
            <?php if($user_type =="Admin"):?>
            <li ><a class="nav-link" href="reviews.php">Reviews <img src="assets/img/icons/reviews.svg" alt=""></a></li>
            <?php endif;?>
            <!-- <li ><a class="nav-link" href="support.php">Support <img src="assets/img/icons/headset.svg" alt=""></a></li> -->
        </ul>
    </div>
</nav>