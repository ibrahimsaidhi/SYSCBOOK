<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <title>Login</title>
   <link rel="stylesheet" href="assets/css/reset.css">
   <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <header>
        <h1>SYSCX</h1>
        <p>Social media for SYSC students in Carleton University</p>
    </header>
    <div class="container">
      <div class="nav-bar">
         <nav>
            <ul>
               <li><a href="index.php">Home</a></li>
               <li><a href="profile.php">Profile</a></li>
               <li><a href="user_list.php">Users List</a></li>
               <li><a href="logout.php">Log out</a></li>
            </ul>
         </nav>
      </div>
  
      <div class='forms'>
        <section>
            <h2>User List</h2>
            <table border='1' class="user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student Email</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Program</th>
                        <th>Account Type</th>
                    </tr>
                </thead>
                <?php
                    session_start();
                    include ("connection.php");
                    $conn = new mysqli($server_name, $username, $password, $database_name);

                    if ($conn->connect_error) {
                        die("Error: Could not connect to database: " . $conn -> connect_error);
                    }

                    if (!isset($_SESSION['id'])){
                        header('Location: login.php');
                    }

                    $id = $_SESSION['id'];

                    $sql2 = "SELECT * FROM `users_permissions` where student_id = ?;";
                    $statement = $conn->prepare($sql2);
                    $statement->bind_param('i', $id);
                    $statement->execute();

                    $res = $statement->get_result();

                    while ($row = $res->fetch_assoc()){
                        $account_type = $row['account_type'];
                    }

                    if ($account_type == 1){
                        echo "
                            <div>
                                <p>Permission denied</p>
                                <a href='index.php'>Back to Home</a>
                            </div>
                        ";
                    }
                    else {
                        $results = "SELECT * from users_info left join users_program on users_program.student_id = users_info.student_id left join users_permissions on users_permissions.student_id = users_program.student_id;";
                        $statement = $conn->prepare($results);
                        $statement->execute();

                        $res = $statement->get_result();
                
                        while ($row = $res->fetch_assoc()) {
                            $student_id = $row['student_id'];
                            $first_name = $row['first_name'];
                            $last_name = $row['last_name'];
                            $student_email = $row['student_email'];
                            $program = $row['program'];
                            $account_type = $row['account_type'];
                            echo "<tr>
                                <td>$student_id</td>
                                <td>$student_email</td>
                                <td>$first_name</td>
                                <td>$last_name</td>
                                <td>$program</td>
                                <td>$account_type</td>
                            </tr>";
                        }
                    }
                ?>
            </table>
        </section>
    </div>
        <?php
            
            $id = $_SESSION['id'];
            

            $results2 = "SELECT * from users_info where student_id = $id;";
            $query = $conn->query($results2);
            
            while ($row = $query->fetch_assoc()) {
                $first_name = $row['first_name'];
                $last_name = $row['last_name'];
                $student_email = $row['student_email'];
            }

            $result2 = "SELECT * from users_program where student_id = $id;";
            $query = $conn->query($result2);

            while ($row = $query->fetch_assoc()) {
                $program = $row['program'];
            }

            $result3 = "SELECT * from users_avatar where student_id = $id;";
            $query = $conn->query($result3);

            while ($row = $query->fetch_assoc()) {
                $avatar = $row['avatar'];
            }

            $avatar = $avatar + 1;

            echo
            "<div class='profile'>
                <div class='profile-info'>
                <p class='name'>$first_name  $last_name</p>
                <img src='images/img_avatar$avatar.png' alt='Profile Picture' class='profile-image'>
                <p class='email'>$student_email </p>
                <p class='program'>$program</p>
                </div>
            </div>";
            $conn -> close();
        ?>
    </div>
</body>