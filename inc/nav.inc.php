<nav class="nav-bar" data-state="closed">
    <div class="nav-links-header">
        <button class="btn-close" aria-label="Close Menu" id="nav-close">
            <svg class="icon feather-icon">
                <use xlink:href="./assets/img/icons/feather.svg#x"></use>
            </svg>
        </button>
    </div>
    <div class="nav-container">
        <ul class="nav-links">
            <?php if ($cms->type() == "Business") : ?>
                <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "index")){echo"link-active";}?>" href="/admin"><svg class="icon"><use xlink:href="assets/img/icons/solid.svg#house"></use></svg>Home</a></li>
                <?php if ($price_list->status()=="On") : ?>
                    <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "price_list")){echo"link-active";}?>" href="price_list"><svg class="icon"><use xlink:href="assets/img/icons/solid.svg#tags"></use></svg>Price List</a></li>
                    <?php endif; ?>

                <?php if ($news_m->status() == "On") : ?>
                    <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "news")){echo"link-active";}?>" href="news"><svg class="icon"><use xlink:href="assets/img/icons/solid.svg#newspaper"></use></svg>News </a></li>
                    <?php endif;?>
                    <?php if ($forms->status() == "On") : ?>
                        <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "forms") || str_contains($_SERVER['REQUEST_URI'], "form")){echo"link-active";}?>" href="forms"><svg class="icon"><use xlink:href="assets/img/icons/solid.svg#clipboard-user"></use></svg>Forms </a></li>
                        <?php endif;?>
                        
                       
                            <?php if ($user->user_type() == "Admin" || $user->user_type() == "Developer") : ?>
                                <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "reviews")){echo"link-active";}?>" href="reviews"><svg class="icon"><use xlink:href="assets/img/icons/solid.svg#comment-dots"></use></svg> Reviews  </a></li>
                                <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($user->user_type() == "Admin" || $user->user_type() == "Developer") : ?>
                            <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "settings")){echo"link-active";}?>" href="settings"><svg class="icon"><use xlink:href="assets/img/icons/solid.svg#gear"></use></svg> Settings </a></li>
                            <?php endif; ?>
                                <?php if ($user->user_type() == "Developer") : ?>
                                    <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "cms")){echo"link-active";}?>" href="cms_settings"><svg class="icon"><use xlink:href="assets/img/icons/solid.svg#gear"></use></svg> CMS Settings </a></li>
                                    <?php endif; ?>
                                </ul>
                                <div class="user">
                                    <div class="user__name">
                                        <span class="user__avatar">
                                            <svg class="icon feather-icon">
                                                <use xlink:href="assets/img/icons/feather.svg#user"></use>
                                            </svg>
                                        </span>
                                        <span class="user__name_text"><?= $user->name();?></span>
                                        <button class="btn-primary btn-expand">
                                            <svg class="icon feather-icon">
                                                <use xlink:href="assets/img/icons/feather.svg#chevron-down"></use>
                                            </svg>
                                        </button>
                                        
                                    </div>
                                    <div class="user__actions d-none my-2">
                                        <div class="user__actions_links">
                                            <a href="profile">
                                                <svg class="icon feather-icon">
                                                <use xlink:href="assets/img/icons/feather.svg#user"></use>
                                            </svg>
                                             Edit Profile</a>
                                            <a href="logout">
                                                <svg class="icon feather-icon">
                                                <use xlink:href="assets/img/icons/feather.svg#log-out"></use>
                                            </svg>
                                            Logout</a>
                                        </div>
                                    </div>
                                </div>
    </div>
</nav>