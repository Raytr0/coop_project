<div id="navbar" class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
	<div class="navbar-header">
		<ul class="nav navbar-nav flex-row">
			<li class="nav-item mr-auto"><a class="navbar-brand" href="../pages/index.php">
					<div class="brand-logo"></div>
					<h2 class="brand-text mb-0">School Vote</h2>
				</a></li>
			<!--<li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i><i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary" data-ticon="icon-disc"></i></a></li>-->
		</ul>
	</div>
	
	<div class="shadow-bottom"></div>
	<div class="main-menu-content">
		<ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
			<?php if ($user -> getAccountInfo("type_id") != 2): ?>
				<li class="nav-item" id="nav-dashboard"><a href="/pages/dashboard.php"><i class="feather icon-home"></i><span class="menu-title" data-i18n="dashboard">Dashboard</span></a>
				</li>
			<?php endif; ?>
			<li class="nav-item" id="nav-elections"><a href="election-list.php"><i class="feather icon-list"></i><span class="menu-title" data-i18n="elections">Elections</span></a>
			</li>
			<?php if ($user -> getAccountInfo("type_id") <= 2): ?>
				<li class="nav-item" id="nav-vote"><a href="/pages/vote-home.php"><i class="feather icon-check-square"></i><span class="menu-title" data-i18n="vote-home">Vote</span></a>
				</li>
			<?php endif; ?>
			<li class="nav-item" id="nav-profile"><a href="/pages/profile.php"><i class="feather icon-user"></i><span class="menu-title" data-i18n=pProfile">Profile</span></a>
			</li>
		</ul>
	</div>
</div>