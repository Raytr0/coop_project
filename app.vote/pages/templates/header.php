<?php

//require_once "../../assets/php/database.php";
include_once "../../assets/php/account_class.php";
include_once "../../assets/php/redirect.php";
?>

<nav class="header-navbar navbar-expand-lg navbar navbar-with-menu floating-nav navbar-light navbar-shadow">
    <div class="navbar-wrapper">
        <div class="navbar-container content">
            <div class="navbar-collapse" id="navbar-mobile">
                <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                    <ul class="nav navbar-nav">
                        <li class="nav-item mobile-menu d-xl-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ficon feather icon-menu"></i></a></li>
                    </ul>
                    <!--<li class="nav-item mobile-menu d-xl-none mr-auto"><a href="#" class="nav-link nav-menu-main menu-toggle hidden-xs"><i class="ficon feather icon-menu"></i></a></li>   -->
                    <!-- <ul class="nav navbar-nav bookmark-icons">-->
                    <!--    <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-todo.html" data-toggle="tooltip" data-placement="top" title="Todo"><i class="ficon feather icon-check-square"></i></a></li>-->
                    <!--    <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-chat.html" data-toggle="tooltip" data-placement="top" title="Chat"><i class="ficon feather icon-message-square"></i></a></li>-->
                    <!--    <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-email.html" data-toggle="tooltip" data-placement="top" title="Email"><i class="ficon feather icon-mail"></i></a></li>-->
                    <!--    <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-calender.html" data-toggle="tooltip" data-placement="top" title="Calendar"><i class="ficon feather icon-calendar"></i></a></li>-->
                    <!--</ul>-->
                    <!-- <ul class="nav navbar-nav">-->
                    <!--    <li class="nav-item d-none d-lg-block"><a class="nav-link bookmark-star"><i class="ficon feather icon-star warning"></i></a>-->
                    <!--        <div class="bookmark-input search-input">-->
                    <!--            <div class="bookmark-input-icon"><i class="feather icon-search primary"></i></div>-->
                    <!--            <input class="form-control input" type="text" placeholder="Explore Vuexy..." tabindex="0" data-search="template-list">-->
                    <!--            <ul class="search-list"></ul>-->
                    <!--        </div>-->
                    <!--        <select class="bookmark-select">-->
                    <!--           <option>Chat </option> -->
                    <!--           <option>E-mail </option> -->
                    <!--           <option>To-do</option> -->
                    <!--           <option>Calendar</option>-->
                    <!--        </select> -->
                         <!--</li>-->
                    <!--</ul>-->
                </div>
                <ul class="nav navbar-nav float-right">
                     <li class="dropdown dropdown-language nav-item"><a class="dropdown-toggle nav-link" id="dropdown-flag" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="flag-icon flag-icon-us"></i><span class="selected-language">English</span></a>
                        <div class="dropdown-menu" aria-labelledby="dropdown-flag"><a class="dropdown-item" href="#" data-language="en"><i class="flag-icon flag-icon-us"></i> English</a><a class="dropdown-item" href="#" data-language="fr"><i class="flag-icon flag-icon-fr"></i> French</a><a class="dropdown-item" href="#" data-language="de"><i class="flag-icon flag-icon-de"></i> German</a><a class="dropdown-item" href="#" data-language="pt"><i class="flag-icon flag-icon-pt"></i> Portuguese</a></div>
                    </li>
                     <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-expand"><i class="ficon feather icon-maximize"></i></a></li> 
                     <li class="nav-item nav-search"><a class="nav-link nav-link-search"><i class="ficon feather icon-search"></i></a>
                        <div class="search-input">
                            <div class="search-input-icon"><i class="feather icon-search primary"></i></div>
                            <input class="input" type="text" placeholder="Explore Vuexy..." tabindex="-1" data-search="template-list">
                            <div class="search-input-close"><i class="feather icon-x"></i></div>
                            <ul class="search-list"></ul>
                        </div>
                    </li>
                    <li class="dropdown dropdown-notification nav-item" id="notification-dropdown"><a class="nav-link nav-link-label" href="#" data-toggle="dropdown" aria-expanded="false"><i class="ficon feather icon-bell"></i><span class="badge badge-pill badge-primary badge-up" id="num-notifications"></span></a>
                        <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right notification-menu" id="notification-menu">
                            <li class="scrollable-container media-list" id="notifications">
                                <div class="media d-flex align-items-start no-border notification">
                                    <div class="media-left"><i class="feather icon-plus-square font-medium-5 primary"></i></div>
                                    <div class="media-body">
                                        <h6 class="primary media-heading">You have a new order!</h6><small class="notification-text"> Are you going to meet me tonight?</small>
                                    </div><small>
                                        <time class="media-meta" datetime="2015-06-11T18:29:20+08:00">9 hours ago</time></small>
                                </div>
                                <div class="media d-flex align-items-start no-border notification">
                                    <div class="media-left"><i class="feather icon-check-square font-medium-5 primary"></i></div>
                                    <div class="media-body">
                                        <h6 class="primary media-heading">Grade 12 Rep Election</h6><small class="notification-text">From Apr 19 to Apr 30</small>
                                    </div><small>
                                        <img width="15px" height="15px" src="/assets/svg/close.svg" alt="close" class="close-svg">
                                </div>
                            </li>
                            <li class="dropdown-menu-footer"><a class="dropdown-item p-1 text-center close-notification">Clear Notifications</a></li>
                        </ul>
                    </li>
                    <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                            <div class="user-nav d-sm-flex d-none">
                                <span class="user-name text-bold-600">
                                <?php echo $user_info["first_name"] . " " . $user_info["last_name"] ?>
                                </span>
                            </div>
                                <span>
                                    <img class="round" src="<?php  echo $profile_picture ?>" alt="avatar" height="40" width="40">
                                    </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                             <a class="dropdown-item" href="profile.php?function=edit">
                            <i class="feather icon-user"></i> Edit Profile</a>
                            <a class="dropdown-item" href="../assets/html/app-email.html"><i class="feather icon-mail"></i> My Inbox</a>
                            <a class="dropdown-item" href="../assets/html/app-todo.html"><i class="feather icon-check-square"></i> Task</a>
                            <a class="dropdown-item" href="../assets/html/app-chat.html"><i class="feather icon-message-square"></i> Chats</a>
                            <div class="dropdown-divider"></div> 
                            <a class="dropdown-item" href="../../get/logout.php"><i class="feather icon-power"></i> Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<script src="../../assets/js/notification.js"></script>