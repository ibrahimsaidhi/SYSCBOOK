<?php
   session_start();
   include ("connection.php");
   $conn = new mysqli($server_name, $username, $password, $database_name);

   if ($conn->connect_error) {
       die("Error: Could not connect to database: " . $conn -> connect_error);
   }
   // global reference variables since arguments cannot be passed by reference in prepared statements.
   $null = NULL;
   $zero = 0;
   $one = 1;

   if (isset($_POST["register"])){
      try {
         $first_name = $_POST['first_name'];
         $last_name = $_POST['last_name'];
         $dob = $_POST['DOB'];
         $student_email = $_POST['student_email'];
         $program = $_POST['program'];
         $confirm = $_POST['confirm'];
         $password = $_POST['password'];

         $check = "SELECT * from users_info where student_email=?;";

         $query = $conn->prepare($check);
         $query->bind_param('s', $student_email);
         $query->execute();

         $result = $query->get_result();
         $count = mysqli_num_rows($result);
         
         if ($count == 0 and $password == $confirm){
            $sql = "INSERT INTO users_info (student_email, first_name, last_name, dob) values (?, ?, ?, ?);";
         
            $statement = $conn->prepare($sql);
            $statement->bind_param('ssss', $student_email, $first_name, $last_name, $dob);
            $statement->execute();

            $id = 0;
            $results = "SELECT student_id from users_info where first_name = ? and last_name = ? and dob = ?;";
            $statement = $conn->prepare($results);
            $statement->bind_param('sss', $first_name, $last_name, $dob);
            $statement->execute();

            $query = $statement->get_result();
            while ($row = $query->fetch_assoc()) {
               $id = $row['student_id'];
            }
            
            $sql2 = "INSERT INTO users_program (student_id, program) values (?, ?);";
            $statement = $conn->prepare($sql2);
            $statement->bind_param('is', $id, $program);
            $statement->execute();

            
            $sql3 = "INSERT INTO users_avatar (student_id, avatar) values (?, ?);";
            $statement = $conn->prepare($sql3);
            $statement->bind_param('is', $id, $zero);
            $statement->execute();

            $sql4 = "INSERT INTO users_address (student_id, street_number, street_name, city, province, postal_code) values (?, ?, ?, ?, ?, ?);";
            $statement = $conn->prepare($sql4);
            
            $statement->bind_param('iissss', $id, $zero, $null, $null, $null, $null);
            $statement->execute();

            $password = password_hash($password, PASSWORD_BCRYPT);
            $sql5 = "INSERT INTO users_passwords (student_id, password) values (?, ?);";
            $statement = $conn->prepare($sql5);
            $statement->bind_param('is', $id, $password);
            $statement->execute();

            $sql6 = "INSERT INTO users_permissions (student_id, account_type) values (?, ?);";
            $statement = $conn->prepare($sql6);
            $statement->bind_param('ii', $id, $one);
            $statement->execute();

            // setting session variables for student attributes
            $_SESSION['id'] = $id;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;
            $_SESSION['dob'] = $dob;
            $_SESSION['program'] = $program;
            $_SESSION['student_email'] = $student_email;
         }
         else {
            header('Location: register.php');
            echo "
               <script>
                  alert('Passwords do not match. Please try again');
               </script>
            ";
         }
      }
      
      catch (mysqli_sql_exception $e) {
         $error = "There is an error: " . $e->getMessage();
         echo $error;
      }
   }
   else if (!isset($_SESSION['id'])){
      header('Location: login.php');
   }
   else if (isset($_POST["submit"])){
      try {
         $first_name = $_POST['first_name'];
         $last_name = $_POST['last_name'];
         $dob = $_POST['DOB'];
         $student_email = $_POST['student_email'];
         $program = $_POST['program'];
         $street_number = $_POST['street_number'];
         $street_name = $_POST['street_name'];
         $city= $_POST['city'];
         $province = $_POST['province'];
         $code = $_POST['postal_code'];
         $avatar = $_POST['avatar'];

         // retrieve ID from session set in register.php
         $id = $_SESSION['id'];

         $sql = "UPDATE users_info SET student_email = ?, first_name=?, last_name=?, dob=? where student_id = ?;";
         $statement = $conn->prepare($sql);
         $statement->bind_param('ssssi', $student_email, $first_name, $last_name, $dob, $id);
         $statement->execute();

         $sql2 = "UPDATE users_avatar SET avatar=? where student_id = ?;";
         $statement = $conn->prepare($sql2);
         $statement->bind_param('si', $avatar, $id);
         $statement->execute();

         $sql3 = "UPDATE users_program SET program = ? where student_id = ?;";
         $statement = $conn->prepare($sql3);
         $statement->bind_param('si', $program, $id);
         $statement->execute();

         $sql4 = "UPDATE users_address SET street_number = ?, street_name=?, city=?, province=?, postal_code=? where student_id = ?;";
         $statement = $conn->prepare($sql4);
         $statement->bind_param('sssssi', $street_number, $street_name, $city, $province, $code, $id);
         $statement->execute();    
      }
      
      catch (mysqli_sql_exception $e) {
         $error = "There is an error in retrieving the data: " . $e->getMessage();
         echo $error;
      }
   }
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <title>Update SYSCX profile</title>
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
               <h2>Update Profile information</h2>
               <form method="post" action="">
                  <fieldset>
                     <legend><span>Personal information</span></legend>
                     <?php
                        $id = $_SESSION['id'];
                        $results = "SELECT * from users_info where student_id = ?;";
                        $statement = $conn->prepare($results);
                        $statement->bind_param('i', $id);
                        $statement->execute();

                        $res = $statement->get_result();
               
                        while ($row = $res->fetch_assoc()) {
                           $first_name = $row['first_name'];
                           $last_name = $row['last_name'];
                           $dob = $row['dob'];
                        }

                       
                        echo "<table>
                        <tr>
                           <td>
                              <label>First Name: </label>
                              <input type='text' name='first_name' id='first_name' placeholder='ex. John' value=$first_name>
                           </td>
                           <td>
                              <label>Last Name: </label>
                              <input type='text' name='last_name' id='last_name' placeholder='ex. Snow' value=$last_name>
                           </td>
                           <td>
                              <label>DOB: </label>
                              <input type='date' name='DOB' id='DOB' value=$dob>
                           </td>
                        </tr>
                     </table>";
                     ?>
                     
                  </fieldset>
                  <fieldset>
                     <legend><span>Address</span></legend>
                     <?php
                        $id = $_SESSION['id'];
                        $results = "SELECT * from users_address where student_id = ?";

                        $statement = $conn->prepare($results);
                        $statement->bind_param('i', $id);
                        $statement->execute();

                        $res = $statement->get_result();
               
                        while ($row = $res->fetch_assoc()) {
                           $street_name = $row['street_name'];
                           $street_number = $row['street_number'];
                           $city = $row['city'];
                           $province = $row['province'];
                           $code = $row['postal_code'];
                        }

                        echo "<table>
                        <tr>
                           <td>
                              <label>Street Number: </label>
                              <input type='number' name='street_number' id='street_number' value=$street_number>
                           </td>
                           <td>
                              <label>Street Name: </label>
                              <input type='text' name='street_name' id='street_name' value=$street_name>
                           </td>
                           <td></td>
                        </tr>
                        <tr>
                           <td>
                              <label>City: </label>
                              <input type='text' name='city' id='city' value=$city>
                           </td>
                           <td>
                              <label>Province: </label>
                              <input type='text' name='province' id='province' value=$province>
                           </td>
                           <td>
                              <label>Postal Code: </label>
                              <input type='text' name='postal_code' id='postal_code' value=$code>
                           </td>
                        </tr>
                     </table>";
                     ?>
                  </fieldset>
                  <fieldset>
                     <legend><span>Profile Information</span></legend>

                     <?php
                        $id = $_SESSION['id'];
                        $results = "SELECT * from users_program where student_id = ?;";
                        $statement = $conn->prepare($results);
                        $statement->bind_param('i', $id);
                        $statement->execute();

                        $res = $statement->get_result();
               
                        while ($row = $res->fetch_assoc()) {
                           $program = $row['program'];
                        }

                        $results2 = "SELECT * from users_info where student_id = ?;";
                        $statement = $conn->prepare($results2);
                        $statement->bind_param('i', $id);
                        $statement->execute();

                        $res2 = $statement->get_result();
               
                        while ($row = $res2->fetch_assoc()) {
                           $student_email = $row['student_email'];
                        }

                        echo "<table>
                        <tr>
                           <td>
                              <label>Email Address: </label>
                              <input type='text' name='student_email' id='student_email' value=$student_email>
                           </td>
                        </tr>
                        <tr>
                           <td>
                              <label>Program: </label>
                              <select name='program' id='program' value=$program>
                                 <option value=$program selected disabled hidden>$program</option>
                                 <option value='Computer Systems Engineering'>Computer Systems Engineering</option>
                                 <option value='Electrical Engineering'>Electrical Engineering</option>
                                 <option value='Software Engineering'>Software Engineering</option>
                                 <option value='Communications Engineering'>Communications Engineering</option>
                                 <option value='Biomedical and Electrical Engineering'>Biomedical and Electrical Engineering</option>
                                 <option value='Special'>Special</option>
                              </select>
                           </td>
                        </tr>
                        <tr>
                           <td><label>Choose your Avatar: </label></td>
                        </tr>
                        <tr>
                           <td>
                              <input type='radio' id='avatar1' name='avatar' value=0> 
                                 <label for='avatar1'><img class='avatar' src='images/img_avatar1.png' alt='Profile Avatar'></label>
                              
                              <input type='radio' id='avatar2' name='avatar' value=1> 
                                 <label for='avatar2'><img class='avatar' src='images/img_avatar2.png' alt='Profile Avatar'></label>
                              
                              <input type='radio' id='avatar3' name='avatar' value=2> 
                                 <label for='avatar3'><img class='avatar' src='images/img_avatar3.png' alt='Profile Avatar'></label>
                              
                              <input type='radio' id='avatar4' name='avatar' value=3> 
                                 <label for='avatar4'><img class='avatar' src='images/img_avatar4.png' alt='Profile Avatar'></label>
                           
                              <input type='radio' id='avatar5' name='avatar' value=4> 
                                 <label for='avatar5'><img class='avatar' src='images/img_avatar5.png' alt='Profile Avatar'></label>
                           </td>
                        </tr>
                        <tr>
                           <td>
                              <input type='submit' name='submit' value='Submit'>
                              <input type='reset' value='Reset'>
                           </td>
                        </tr>
                     </table>";
                     ?>
                  </fieldset>
               </form>
            </section>
         </main>
      </div>
      <?php
         $result3 = "SELECT * from users_avatar where student_id = ?;";
         $statement = $conn->prepare($result3);
         $statement->bind_param('i', $id);
         $statement->execute();

         $res = $statement->get_result();

         while ($row = $res->fetch_assoc()) {
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