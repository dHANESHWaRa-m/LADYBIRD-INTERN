<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ecommerce</title>
    <link rel="stylesheet" href="../assests/css/homepage.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
</head>
<body>

    <header id="admin-header">
        <a href="#" class="admin-logo"><img src="../img/logo.png" alt="Admin Logo"></a>
        <div class="admin-header-right">
            <span>Welcome, Admin!</span>
            <a href="logout.php" class="button normal">Logout</a>
        </div>
    </header>

    <div id="admin-wrapper">
        <aside id="admin-sidebar">
            <nav>
                <ul>
                    <li><a class="active" href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="manage_products.php"><i class="fas fa-boxes"></i> Manage Products</a></li>
                    <li><a href="manage_orders.php"><i class="fas fa-shopping-cart"></i> Manage Orders</a></li>
                    <li><a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a></li>
                    <li><a href="settings.php"><i class="fas fa-cogs"></i> Settings</a></li>
                    </ul>
            </nav>
        </aside>

        <main id="admin-main-content" class="section-p1">
            <h2>Dashboard Overview</h2>
            <p>Welcome to your admin panel. Here you can manage your e-commerce website.</p>

            <section class="admin-stats-cards">
                <div class="admin-card">
                    <i class="fas fa-dollar-sign"></i>
                    <h4>Total Sales</h4>
                    <p>$1,234,567</p>
                </div>
                <div class="admin-card">
                    <i class="fas fa-shopping-bag"></i>
                    <h4>Total Orders</h4>
                    <p>5,432</p>
                </div>
                <div class="admin-card">
                    <i class="fas fa-users"></i>
                    <h4>New Customers</h4>
                    <p>128</p>
                </div>
                <div class="admin-card">
                    <i class="fas fa-box-open"></i>
                    <h4>Products in Stock</h4>
                    <p>987</p>
                </div>
            </section>

            <section class="admin-recent-orders section-m1">
                <h3>Recent Orders</h3>
                <table>
                    <thead>
                        <tr>
                            <td>Order ID</td>
                            <td>Customer</td>
                            <td>Items</td>
                            <td>Total</td>
                            <td>Status</td>
                            <td>Actions</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#ORD001</td>
                            <td>John Doe</td>
                            <td>3</td>
                            <td>$150.00</td>
                            <td><span class="status processing">Processing</span></td>
                            <td><button class="button normal small">View</button></td>
                        </tr>
                        <tr>
                            <td>#ORD002</td>
                            <td>Jane Smith</td>
                            <td>1</td>
                            <td>$45.00</td>
                            <td><span class="status completed">Completed</span></td>
                            <td><button class="button normal small">View</button></td>
                        </tr>
                        <tr>
                            <td>#ORD003</td>
                            <td>Bob Johnson</td>
                            <td>2</td>
                            <td>$99.50</td>
                            <td><span class="status pending">Pending</span></td>
                            <td><button class="button normal small">View</button></td>
                        </tr>
                        </tbody>
                </table>
            </section>

            </main>
    </div>

    <footer id="admin-footer">
        <p>&copy; 2025 Your Ecommerce Admin. All rights reserved.</p>
    </footer>

    <script src="../assets/js/script.js"></script> </body>
</html>