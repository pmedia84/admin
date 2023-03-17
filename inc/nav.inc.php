<nav class="nav-bar">
    <div class="nav-container">
        <div class="close">
            <button class="nav-btn-close" id="nav-btn-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <ul class="nav-links">
            <?php if ($cms_type == "Business") : ?>
                <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "index")){echo"link-active";}?>" href="index.php">Home <i class="fa-solid fa-house"></i></a></li>
                <?php if ($price_list_status == "On") : ?>
                    <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "price_list")){echo"link-active";}?>" href="price_list.php">Price List <i class="fa-solid fa-tags"></i></a></li>
                <?php endif; ?>
                <?php if ($image_gallery_status == "On") : ?>
                    <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "gallery")){echo"link-active";}?>" href="gallery.php">Image Gallery <i class="fa-solid fa-image"></i></a></li>
                <?php endif; ?>
                <?php if ($news_status == "On") : ?>
                    <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "news")){echo"link-active";}?>" href="news.php">News <i class="fa-solid fa-newspaper"></i></a></li>
                <?php endif;?>

                <?php if ($user_type == "Admin" || $user_type == "Developer") : ?>
                    <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "settings")){echo"link-active";}?>" href="settings.php">Settings <i class="fa-solid fa-gear"></i></a></li>
                <?php endif; ?>
                <?php if ($user_type == "Admin" || $user_type == "Developer") : ?>
                    <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "reviews")){echo"link-active";}?>" href="reviews.php">Reviews <i class="fa-solid fa-comment-dots"></i></a></li>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($cms_type == "Wedding") : ?>
                <li><a class="nav-link" href="index.php">Home <i class="fa-solid fa-house"></i></a></li>
                <?php if ($guest_list_status == "On") : ?>
                    <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "guest")){echo"link-active";}?>" href="guest_list">Guest List <i class="fa-solid fa-people-group"></i></a></li>
                <?php endif; ?>
                <?php if ($user_type == "Admin" || $user_type == "Developer") : ?>
                    <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "event")){echo"link-active";}?>" href="events">Events <i class="fa-solid fa-calendar-day"></i></a></li>
                <?php endif; ?>
                <?php if ($menu_builder_status == "On") : ?>
                    <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "menu")){echo"link-active";}?>" href="menu">Menu Builder <i class="fa-solid fa-bowl-food"></i></a></li>
                <?php endif; ?>
                <?php if ($meal_choices_status == "On") : ?>
                    <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "meal_choices")){echo"link-active";}?>" href="meal_choices">Guest Meal Choices <i class="fa-solid fa-utensils"></i></a></li>
                <?php endif; ?>
                <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "our_story")){echo"link-active";}?>" href="our_story">Our Story <i class="fa-regular fa-heart"></i></a></li>
                <?php if ($invite_manager_status == "On") : ?>
                    <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "invit")){echo"link-active";}?>" href="invitations">Invitations <i class="fa-solid fa-champagne-glasses"></i></a></li>
                <?php endif; ?>
                <?php if ($guest_messaging_status == "On") : ?>
                    <li><a class="nav-link" href="messaging">Guest Messages <i class="fa-solid fa-message"></i></a></li>
                <?php endif; ?>
                <?php if ($gift_list_status == "On") : ?>
                    <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "gift")){echo"link-active";}?>" href="gift_list">Gift List <i class="fa-solid fa-gifts"></i></a></li>
                <?php endif; ?>
                <?php if ($image_gallery_status == "On") : ?>
                    <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "gallery")){echo"link-active";}?>" href="gallery">Image Gallery <i class="fa-solid fa-images"></i></a></li>
                <?php endif; ?>
                <?php if ($news_status == "On") : ?>
                    <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "news")){echo"link-active";}?>" href="news">News <i class="fa-solid fa-newspaper"></i></a></li>
                <?php endif; ?>
                <?php if ($user_type == "Admin" || $user_type == "Developer") : ?>
                    <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "wedding")){echo"link-active";}?>" href="wedding_settings">Website Settings <i class="fa-solid fa-laptop"></i></a></li>
                <?php endif; ?>
                
            <?php endif; ?>
            <?php if ($user_type == "Developer") : ?>
                <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "cms")){echo"link-active";}?>" href="cms_settings">CMS Settings <i class="fa-solid fa-gear"></i></a></li>
            <?php endif; ?>
            <li><a class="nav-link" href="logout">Logout<i class="fa-solid fa-right-from-bracket"></i></a></li>
        </ul>
    </div>
</nav>