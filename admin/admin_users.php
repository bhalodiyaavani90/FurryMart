<?php
session_start();
require_once('../db.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include('includes/header.php');
?>

<style>
    .table-container { background: white; padding: 20px; border-radius: 12px; box-shadow: var(--shadow-md); margin-top: 20px; overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; min-width: 800px; }
    th { text-align: left; padding: 12px; background: #f1f5f9; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; }
    td { padding: 12px; border-bottom: 1px solid #e2e8f0; font-size: 0.9rem; }
    
    .btn-action { text-decoration: none; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; margin-right: 5px; display: inline-block; }
    .btn-edit { background: #e0f2fe; color: #0369a1; }
    .btn-delete { background: #fee2e2; color: #991b1b; }
    .btn-add { background: var(--primary-color); color: white; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; transition: 0.3s; }
    .btn-add:hover { opacity: 0.9; transform: translateY(-2px); }
</style>

<main class="dashboard-content">
    <div class="card" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h2>User Management</h2>
            <p style="color: var(--text-muted);">Managing users in FURRYMART database.</p>
        </div>
        <a href="add_user.php" class="btn-add">+ Add New User</a>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM users ORDER BY id DESC";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Concatenating First, Middle, and Last Name
                        $fullName = trim($row['first_name'] . " " . $row['middle_name'] . " " . $row['last_name']);
                        
                        echo "<tr>
                            <td>#{$row['id']}</td>
                            <td><strong>{$fullName}</strong></td>
                            <td>{$row['email']}</td>
                            <td>{$row['mobile_number']}</td>
                            <td>{$row['city']}, {$row['state']}</td>
                            <td>
                                <a href='update_user.php?id={$row['id']}' class='btn-action btn-edit'>Edit</a>
                                <a href='delete_user.php?id={$row['id']}' class='btn-action btn-delete' onclick='return confirm(\"Are you sure you want to delete this user?\")'>Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center;'>No users found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</main>

<?php include('includes/footer.php'); ?>