<?php
$pageTitle = "Manage Users";
$activePage = "users";
require_once '../includes/admin-header.php';

// Fetch all users
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h3 style="margin: 0;">User Management</h3>
        <span style="background: #e5e7eb; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">
            Total Users: <?php echo count($users); ?>
        </span>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                    <th style="padding: 1rem;">ID</th>
                    <th style="padding: 1rem;">Name</th>
                    <th style="padding: 1rem;">Email</th>
                    <th style="padding: 1rem;">Role</th>
                    <th style="padding: 1rem;">Joined Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr style="border-bottom: 1px solid #f3f4f6; transition: background 0.2s; cursor: default;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                    <td style="padding: 1rem; color: #6b7280;">#<?php echo $user['id']; ?></td>
                    <td style="padding: 1rem; font-weight: 600;"><?php echo htmlspecialchars($user['name']); ?></td>
                    <td style="padding: 1rem;"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td style="padding: 1rem;">
                        <span style="padding: 0.25rem 0.6rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
                            background: <?php echo $user['role'] == 'admin' ? '#ddd6fe' : '#e5e7eb'; ?>; 
                            color: <?php echo $user['role'] == 'admin' ? '#5b21b6' : '#374151'; ?>;">
                            <?php echo $user['role']; ?>
                        </span>
                    </td>
                    <td style="padding: 1rem; color: #6b7280;"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/admin-footer.php'; ?>
