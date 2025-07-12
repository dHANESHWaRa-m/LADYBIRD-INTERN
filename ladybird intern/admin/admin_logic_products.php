<?php
// admin_logic_products.php - Contains logic and HTML for Product Management

// IMPORTANT: Do NOT redeclare $pdo, $message, $error_message, $session_start()
// These are already available from admin_dashboard.php (the parent file)

$editing_product = null; // To hold product data if we are in 'edit' mode

// --- Handle Form Submissions (Add, Update, Delete) for Products ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add_product':
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
            $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
            $image_url = filter_input(INPUT_POST, 'image_url', FILTER_SANITIZE_URL); // Or handle file upload
            $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
            $stock_quantity = filter_input(INPUT_POST, 'stock_quantity', FILTER_VALIDATE_INT);
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            $color_options = filter_input(INPUT_POST, 'color_options', FILTER_SANITIZE_STRING);

            if (empty($name) || $price === false || $stock_quantity === false || $price < 0 || $stock_quantity < 0) {
                $error_message = "All product fields are required and must be valid numbers.";
            } else {
                try {
                    $stmt_insert = $pdo->prepare("INSERT INTO products (name, description, price, image_url, category, stock_quantity, is_featured, color_options) VALUES (:name, :description, :price, :image_url, :category, :stock_quantity, :is_featured, :color_options)");
                    $stmt_insert->execute([
                        'name' => $name,
                        'description' => $description,
                        'price' => $price,
                        'image_url' => $image_url,
                        'category' => $category,
                        'stock_quantity' => $stock_quantity,
                        'is_featured' => $is_featured,
                        'color_options' => $color_options
                    ]);
                    $message = "Product '{$name}' added successfully!";
                } catch (PDOException $e) {
                    error_log("Admin Add Product Error: " . $e->getMessage());
                    $error_message = "Database error while adding product.";
                }
            }
            break;

        case 'update_product':
            $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
            $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
            $image_url = filter_input(INPUT_POST, 'image_url', FILTER_SANITIZE_URL); // Or handle file upload
            $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
            $stock_quantity = filter_input(INPUT_POST, 'stock_quantity', FILTER_VALIDATE_INT);
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            $color_options = filter_input(INPUT_POST, 'color_options', FILTER_SANITIZE_STRING);


            if (empty($product_id) || empty($name) || $price === false || $stock_quantity === false || $price < 0 || $stock_quantity < 0) {
                $error_message = "All product fields are required and must be valid numbers for update.";
            } else {
                try {
                    $sql_update = "UPDATE products SET name = :name, description = :description, price = :price, image_url = :image_url, category = :category, stock_quantity = :stock_quantity, is_featured = :is_featured, color_options = :color_options WHERE id = :id";
                    $stmt_update = $pdo->prepare($sql_update);
                    $stmt_update->execute([
                        'name' => $name,
                        'description' => $description,
                        'price' => $price,
                        'image_url' => $image_url,
                        'category' => $category,
                        'stock_quantity' => $stock_quantity,
                        'is_featured' => $is_featured,
                        'color_options' => $color_options,
                        'id' => $product_id
                    ]);
                    $message = "Product '{$name}' updated successfully!";
                } catch (PDOException $e) {
                    error_log("Admin Update Product Error: " . $e->getMessage());
                    $error_message = "Database error while updating product.";
                }
            }
            break;

        case 'delete_product':
            $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
            if (empty($product_id)) {
                $error_message = "No product ID provided for deletion.";
            } else {
                try {
                    $stmt_delete = $pdo->prepare("DELETE FROM products WHERE id = :id");
                    $stmt_delete->execute(['id' => $product_id]);
                    $message = "Product (ID: {$product_id}) deleted successfully!";
                } catch (PDOException $e) {
                    error_log("Admin Delete Product Error: " . $e->getMessage());
                    $error_message = "Database error while deleting product.";
                }
            }
            break;
    }
}

// --- Check for Edit Mode (GET request with product_id) ---
if (isset($_GET['edit_id']) && filter_var($_GET['edit_id'], FILTER_VALIDATE_INT)) {
    $edit_id = $_GET['edit_id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id"); // Fetch all columns for edit form
        $stmt->execute(['id' => $edit_id]);
        $editing_product = $stmt->fetch();
        if (!$editing_product) {
            $error_message = "Product not found for editing.";
        }
    } catch (PDOException $e) {
        error_log("Admin Edit Product Fetch Error: " . $e->getMessage());
        $error_message = "Database error fetching product for edit.";
    }
}

// --- Fetch all products for display ---
$products = [];
try {
    $stmt = $pdo->query("SELECT id, name, price, image_url, category, stock_quantity, is_featured, color_options FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Admin Fetch All Products Error: " . $e->getMessage());
    $error_message = "Database error fetching product list.";
}

?>

<h2><?php echo $editing_product ? 'Edit Product' : 'Add New Product'; ?></h2>
<form class="form-crud" action="admin_dashboard.php?section=products" method="POST">
    <?php if ($editing_product): ?>
        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($editing_product['id']); ?>">
        <input type="hidden" name="action" value="update_product">
    <?php else: ?>
        <input type="hidden" name="action" value="add_product">
    <?php endif; ?>

    <div>
        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="name" value="<?php echo htmlspecialchars($editing_product['name'] ?? ''); ?>" required>
    </div>
    <div>
        <label for="product_description">Description:</label>
        <textarea id="product_description" name="description" rows="4"><?php echo htmlspecialchars($editing_product['description'] ?? ''); ?></textarea>
    </div>
    <div>
        <label for="product_price">Price:</label>
        <input type="number" id="product_price" name="price" step="0.01" value="<?php echo htmlspecialchars($editing_product['price'] ?? ''); ?>" required min="0">
    </div>
    <div>
        <label for="product_image_url">Image URL:</label>
        <input type="text" id="product_image_url" name="image_url" value="<?php echo htmlspecialchars($editing_product['image_url'] ?? ''); ?>">
        <small>E.g., `img/products/my_product.jpg`</small>
    </div>
    <div>
        <label for="product_category">Category:</label>
        <input type="text" id="product_category" name="category" value="<?php echo htmlspecialchars($editing_product['category'] ?? ''); ?>">
    </div>
    <div>
        <label for="product_stock">Stock Quantity:</label>
        <input type="number" id="product_stock" name="stock_quantity" value="<?php echo htmlspecialchars($editing_product['stock_quantity'] ?? ''); ?>" required min="0">
    </div>
    <div>
        <label for="is_featured">Featured Product:</label>
        <input type="checkbox" id="is_featured" name="is_featured" value="1" <?php echo (($editing_product['is_featured'] ?? 0) == 1) ? 'checked' : ''; ?>>
    </div>
    <div>
        <label for="color_options">Color Options (comma-separated):</label>
        <input type="text" id="color_options" name="color_options" value="<?php echo htmlspecialchars($editing_product['color_options'] ?? ''); ?>">
        <small>E.g., `red,blue,green`</small>
    </div>
    <div class="flex-buttons">
        <button type="submit"><?php echo $editing_product ? 'Update Product' : 'Add Product'; ?></button>
        <?php if ($editing_product): ?>
            <a href="admin_dashboard.php?section=products" class="action-btn cancel-btn">Cancel Edit</a>
        <?php endif; ?>
    </div>
</form>

<h2>All Products</h2>
<?php if (!empty($products)): ?>
    <table class="admin-table-products">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Featured</th>
                <th>Colors</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['id']); ?></td>
                    <td>
                        <?php if (!empty($product['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($product['stock_quantity']); ?></td>
                    <td><?php echo $product['is_featured'] ? 'Yes' : 'No'; ?></td>
                    <td><?php echo htmlspecialchars($product['color_options']); ?></td>
                    <td class="action-links">
                        <a href="admin_dashboard.php?section=products&edit_id=<?php echo htmlspecialchars($product['id']); ?>">Edit</a> |
                        <form action="admin_dashboard.php?section=products" method="POST" style="display:inline-block;">
                            <input type="hidden" name="action" value="delete_product">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this product?');" class="delete-btn" style="background: none; border: none; padding: 0; font: inherit; cursor: pointer; text-decoration: underline;">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No products found in the database. Add some!</p>
<?php endif; ?>