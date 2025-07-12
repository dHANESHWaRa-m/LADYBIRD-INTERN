<?php
// admin_header.php
// This file provides the consistent header for the admin dashboard.
// It assumes session_start() has already been called in the main admin_dashboard.php.

// Get the current section for active link highlighting
$current_section = $_GET['section'] ?? 'users';
?>
<section id="header">
    <a href="admin_dashboard.php"><img src="logo.png" class="logo" alt="Admin Logo"></a>
    <div>
        <ul id="navbar">
            <li>
                <a href="admin_dashboard.php?section=users" class="<?php echo ($current_section === 'users') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-users"></i> User Management
                </a>
            </li>
            <li>
                <a href="admin_dashboard.php?section=products" class="<?php echo ($current_section === 'products') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-box-open"></i> Product Management
                </a>
            </li>
            <li>
                <a href="logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
            </li>
            <!-- Add more admin-specific navigation links here if needed -->
        </ul>
    </div>
    <div id="mobile">
        <!-- Mobile menu icon/links for admin panel, if you need them -->
        <!-- For simplicity, often admin mobile menus are handled differently or are less complex -->
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i></a>
        <i id="bar" class="fas fa-outdent"></i>
    </div>
</section>