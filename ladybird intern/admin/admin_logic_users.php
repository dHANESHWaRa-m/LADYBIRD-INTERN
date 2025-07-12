<?php
// admin_logic_users.php - Contains logic and HTML for User Management

// IMPORTANT: Do NOT redeclare $pdo, $message, $error_message, $session_start()
// These are already available from admin_dashboard.php (the parent file)

$editing_user = null; // To hold user data if we are in 'edit' mode

// --- Handle Form Submissions (Add, Update, Delete) for Users ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add_user':
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password']; // Raw password for hashing
            $user_type = filter_input(INPUT_POST, 'user_type', FILTER_SANITIZE_STRING);

            if (empty($username) || empty(trim($email)) || empty($password) || empty($user_type)) {
                $error_message = "All fields are required to add a user.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error_message = "Invalid email format.";
            } elseif (strlen($password) < 6) {
                $error_message = "Password must be at least 6 characters long.";
            } elseif (!in_array($user_type, ['user', 'admin'])) {
                $error_message = "Invalid user type selected.";
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                try {
                    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
                    $stmt_check->execute(['username' => $username, 'email' => $email]);
                    if ($stmt_check->fetchColumn() > 0) {
                        $error_message = "Username or Email already exists.";
                    } else {
                        $stmt_insert = $pdo->prepare("INSERT INTO users (username, email, password_hash, user_type) VALUES (:username, :email, :password_hash, :user_type)");
                        $stmt_insert->execute([
                            'username' => $username,
                            'email' => $email,
                            'password_hash' => $password_hash,
                            'user_type' => $user_type
                        ]);
                        $message = "User '{$username}' added successfully!";
                    }
                } catch (PDOException $e) {
                    error_log("Admin Add User Error: " . $e->getMessage());
                    $error_message = "Database error while adding user.";
                }
            }
            break;

        case 'update_user':
            $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password']; // May be empty if not changed
            $user_type = filter_input(INPUT_POST, 'user_type', FILTER_SANITIZE_STRING);

            if (empty($user_id) || empty($username) || empty(trim($email)) || empty($user_type)) {
                $error_message = "All fields (except password if not changing) are required to update a user.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error_message = "Invalid email format.";
            } elseif (!in_array($user_type, ['user', 'admin'])) {
                $error_message = "Invalid user type selected.";
            } else {
                try {
                    // Check for duplicate email/username (excluding current user)
                    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE (username = :username OR email = :email) AND id != :id");
                    $stmt_check->execute(['username' => $username, 'email' => $email, 'id' => $user_id]);
                    if ($stmt_check->fetchColumn() > 0) {
                        $error_message = "Username or Email already exists for another user.";
                    } else {
                        $sql_update = "UPDATE users SET username = :username, email = :email, user_type = :user_type";
                        $params = [
                            'username' => $username,
                            'email' => $email,
                            'user_type' => $user_type,
                            'id' => $user_id
                        ];

                        // Only update password if a new one is provided
                        if (!empty($password)) {
                            if (strlen($password) < 6) {
                                $error_message = "New password must be at least 6 characters long.";
                                break; // Exit switch if password is too short
                            }
                            $password_hash = password_hash($password, PASSWORD_DEFAULT);
                            $sql_update .= ", password_hash = :password_hash";
                            $params['password_hash'] = $password_hash;
                        }
                        $sql_update .= " WHERE id = :id";

                        $stmt_update = $pdo->prepare($sql_update);
                        $stmt_update->execute($params);
                        $message = "User '{$username}' updated successfully!";
                    }
                } catch (PDOException $e) {
                    error_log("Admin Update User Error: " . $e->getMessage());
                    $error_message = "Database error while updating user.";
                }
            }
            break;

        case 'delete_user':
            $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
            if (empty($user_id)) {
                $error_message = "No user ID provided for deletion.";
            } else {
                try {
                    // Prevent an admin from deleting themselves!
                    if ($user_id == $_SESSION['user_id']) {
                        $error_message = "You cannot delete your own admin account!";
                    } else {
                        $stmt_delete = $pdo->prepare("DELETE FROM users WHERE id = :id");
                        $stmt_delete->execute(['id' => $user_id]);
                        $message = "User (ID: {$user_id}) deleted successfully!";
                    }
                } catch (PDOException $e) {
                    error_log("Admin Delete User Error: " . $e->getMessage());
                    $error_message = "Database error while deleting user.";
                }
            }
            break;
    }
}

// --- Check for Edit Mode (GET request with user_id) ---
if (isset($_GET['edit_id']) && filter_var($_GET['edit_id'], FILTER_VALIDATE_INT)) {
    $edit_id = $_GET['edit_id'];
    try {
        $stmt = $pdo->prepare("SELECT id, username, email, user_type FROM users WHERE id = :id");
        $stmt->execute(['id' => $edit_id]);
        $editing_user = $stmt->fetch();
        if (!$editing_user) {
            $error_message = "User not found for editing.";
        }
    } catch (PDOException $e) {
        error_log("Admin Edit User Fetch Error: " . $e->getMessage());
        $error_message = "Database error fetching user for edit.";
    }
}

// --- Fetch all users for display ---
$users = [];
try {
    $stmt = $pdo->query("SELECT id, username, email, user_type FROM users ORDER BY id ASC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Admin Fetch All Users Error: " . $e->getMessage());
    $error_message = "Database error fetching user list.";
}

?>

<h2><?php echo $editing_user ? 'Edit User' : 'Add New User'; ?></h2>
<form class="form-crud" action="admin_dashboard.php?section=users" method="POST">
    <?php if ($editing_user): ?>
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($editing_user['id']); ?>">
        <input type="hidden" name="action" value="update_user">
    <?php else: ?>
        <input type="hidden" name="action" value="add_user">
    <?php endif; ?>

    <div>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($editing_user['username'] ?? ''); ?>" required>
    </div>
    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($editing_user['email'] ?? ''); ?>" required>
    </div>
    <div>
        <label for="password"><?php echo $editing_user ? 'New Password (leave blank to keep current):' : 'Password:'; ?></label>
        <input type="password" id="password" name="password" <?php echo $editing_user ? '' : 'required'; ?>>
    </div>
    <div>
        <label for="user_type">User Type:</label>
        <select id="user_type" name="user_type" required>
            <option value="user" <?php echo (($editing_user['user_type'] ?? '') === 'user') ? 'selected' : ''; ?>>User</option>
            <option value="admin" <?php echo (($editing_user['user_type'] ?? '') === 'admin') ? 'selected' : ''; ?>>Admin</option>
        </select>
    </div>
    <div class="flex-buttons">
        <button type="submit"><?php echo $editing_user ? 'Update User' : 'Add User'; ?></button>
        <?php if ($editing_user): ?>
            <a href="admin_dashboard.php?section=users" class="action-btn cancel-btn">Cancel Edit</a>
        <?php endif; ?>
    </div>
</form>

<h2>All Users</h2>
<?php if (!empty($users)): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['user_type']); ?></td>
                    <td class="action-links">
                        <a href="admin_dashboard.php?section=users&edit_id=<?php echo htmlspecialchars($user['id']); ?>">Edit</a> |
                        <form action="admin_dashboard.php?section=users" method="POST" style="display:inline-block;">
                            <input type="hidden" name="action" value="delete_user">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this user?');" class="delete-btn" style="background: none; border: none; padding: 0; font: inherit; cursor: pointer; text-decoration: underline;">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No users found in the database.</p>
<?php endif; ?>