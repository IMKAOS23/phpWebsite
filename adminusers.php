<?php
include "include/dbcon.php";
if (!isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] !== true) {
    header("Location: index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Terminal - Users</title>
    <link rel="stylesheet" href="static/css/basicStyle.css">
    <link rel="stylesheet" href="static/css/formStyle.css">
    <link rel="stylesheet" href="static/css/home.css">
    <link rel="stylesheet" href="static/css/admin.css">
</head>
<body>
    <a href="#main-content" class="skip-nav">Skip to Main Content</a>
    <main class="align-center" id="main-content">
        <?php include "include/admin.php" ?>
        <h1>Admin Users Panel</h1>
        <p>Here you can add, view, edit and delete Users</p>
        <section class="view-admin">
            <h2>Users Table - Add/View/Edit/Delete Users</h2>
            <?php
             $users = $db->select("users", "*,
             CASE WHEN role_id = 1 THEN 'User'
             WHEN role_id = 2 THEN 'Admin'
             WHEN role_id = 3 THEN 'Owner'
             END AS role_name, CASE WHEN gender_id = 1 THEN 'Male'
             WHEN gender_id = 2 THEN 'Female'
             WHEN gender_id = 3 THEN 'Other' END AS gender") ?>
             <form action="addForm.php" class="add-form" method="GET">
                <input type="hidden" name="add" value="user">
                <button type="submit">Add New User</button>
             </form>
             <table class="table">
                <thead>
                    <tr class="table-head">
                        <th>User ID</th>
                        <th>Role</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Gender</th>
                        <th>Date Of Birth</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) { ?>
                        <tr>
                            <td><?php echo $user["user_id"] ?></td>
                            <td><?php echo $user["role_name"] ?></td>
                            <td><?php echo $user["username"] ?></td>
                            <td><?php echo $user["email"] ?></td>
                            <td><?php echo $user["f_name"] ?></td>
                            <td><?php echo $user["l_name"] ?></td>
                            <td><?php echo $user["gender"] ?></td>
                            <td><?php echo $user["birth_date"] ?></td>
                            <td>
                                <form action="editform.php" class="edit" method="GET">
                                    <input type="hidden" name="edit" value="<?php echo $user["user_id"] ?>">
                                    <input type="hidden" name="editTable" value="user">
                                    <button type="submit">Edit User</button>
                                </form>
                            </td>
                            <td>
                                <form action="adminDelete.php" method="POST" id="deleteForm_<?php echo $user['user_id'] ?>" class="delete">
                                    <input type="hidden" name="userIdDelete" value="<?php echo $user['user_id'] ?>">
                                    <input type="hidden" name="deleting" value="true">
                                    <button type="button" onclick="confirmDelete('deleteForm_<?php echo $user['user_id'] ?>','<?php echo $user['username'] ?>')">Delete User</button>
                                </form>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </section>
        <?php include "include/footer.php" ?>
    </main>
    <script src="static/js/admin.js"></script>
</body>
</html>