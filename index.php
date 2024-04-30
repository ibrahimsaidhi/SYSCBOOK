<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <title>Home - SYSCX</title>
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
      <div class="forms">
         <main>
            <section>
               <h2>New Post</h2>  
               <form
                  method="post"
                  action="">
                  <fieldset>
                     <table>
                        <tr>
                        <td>
                           <textarea
                              name="new_post"
                              class="new_post"
                              cols="55"
                              rows="5"
                              placeholder="What is happening? (max 280 char)"
                           ></textarea>
                        </td>
                        </tr>
                        <tr>
                           <td>
                              <div>
                                 <input type="submit" name="submit" value="Post" />
                                 <input type="reset" value="Reset" />
                              </div>
                           </td>
                        </tr>
                     </table>
                  </fieldset>
               </form>
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

                  else if (isset($_POST["submit"])){
                     try {
                        $new_post = $_POST['new_post'];
                        $id = $_SESSION['id'];
                        $date = date("Y-m-d");
                        // crashes when there is double quotes in message post.
                        $sql1 = 'INSERT INTO `users_posts` (`student_id`, `new_post`, `post_date`) values (?, ?, ?);';
                        $statement = $conn->prepare($sql1);
                        $statement->bind_param('iss', $id, $new_post, $date);
                        $statement->execute();

                        $sql2 = "SELECT * FROM users_posts ORDER BY post_id DESC LIMIT 10;";
                        $results = $conn->query($sql2);

                        while ($row = $results->fetch_assoc()){
                           $p = $row['new_post'];

                           echo "<details open class='posts'>
                              <summary>Post</summary>
                                 <p>
                                    $p 
                                 </p>
                              </details>";
                        } 
                     }
                     
                     catch (mysqli_sql_exception $e) {
                        $error = "There is an error: " . $e->getMessage();
                        echo $error;
                     }
                  }   
               ?>

            </section>
         </main>
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
</html>
