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
    <div class="background">
        <div class="login-box">
            <h1>Login</h1>
            <form method="post" class="login" name="login" action="">
                <input type="text" value="" name="login-email" placeholder="Email" />
                <br/>
                <input type="password" value="" name="password" placeholder="Password"/>
                <br/>
                <input type="submit" name="login" value="Login"/>
            </form>
            <?php
                session_start();

                //establish db connection
                include ("connection.php");
                $conn = new mysqli($server_name, $username, $password, $database_name);

                if ($conn->connect_error) {
                    die("Error: Could not connect to database: " . $conn -> connect_error);
                }

                if (isset($_POST["login"])){
                    try {
                        $email = $_POST['login-email'];
                        $password = $_POST['password'];

                        $results = "SELECT student_id from users_info where student_email = ?;";
                        $query = $conn->prepare($results);
                        $query->bind_param('s', $email);
                        $query->execute();
                        $result = $query->get_result();

                        // check to see if valid email request has returned any rows.
                        $count = mysqli_num_rows($result);
                        if ($count != 0){
                            while ($row = $result->fetch_assoc()) {
                                $id = $row['student_id'];
                            }

                            $results = "SELECT password from users_passwords where student_id = ?;";
                            $query = $conn->prepare($results);
                            $query->bind_param('i', $id);
                            $query->execute();
                            $result = $query->get_result();

                            while ($row = $result->fetch_assoc()) {
                                $pass = $row['password'];
                            }
    
                            if (password_verify($password, $pass)){
                                $_SESSION['id'] = $id;
                                header('Location: index.php');
                            }
                            else {
                                echo "<p class='incorrect'>Incorrect password, please try again.</p>";
                            }
                        }
                        else {
                            echo "<p class='incorrect'>Incorrect email, please try again.</p>";
                        }
                       
                    } catch (mysqli_sql_exception $e) {
                        $error = "There is an error: " . $e->getMessage();
                        echo $error;
                    }
                }

            ?>
            <a class="reg-link" href="register.php">Don't have an account? Register here!</a>
            <br>
        </div>
    </div>
</body>