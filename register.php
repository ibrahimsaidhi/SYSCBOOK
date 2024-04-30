<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <title>Register on SYSCX</title>
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
               <li><a href="register.php">Register</a></li>
            </ul>
         </nav>
      </div>
      <div class="forms">
         <main>
            <section>
               <h2>Register a New Profile</h2>
               <form method="post" action="profile.php">
                  <fieldset>
                     <legend><span>Personal information</span></legend>
                     <table>
                        <tr>
                           <td>
                              <label>First Name: </label>
                              <input type="text" name="first_name" id="first_name" placeholder="ex. John">
                           </td>
                           <td>
                              <label>Last Name: </label>
                              <input type="text" name="last_name" id="last_name" placeholder="ex. Smith">
                           </td>
                           <td>
                              <label>DOB: </label>
                              <input type="date" name="DOB" id="DOB">
                           </td>
                        </tr>
                     </table>
                  </fieldset>
                  <fieldset>
                     <legend><span>Profile Information</span></legend>
                     <table>
                        <tr>
                           <td>
                              <label>Email Address: </label>
                              <input type="text" name="student_email" id="student_email">
                           </td>
                        </tr>
                        <tr>
                           <td>
                              <label>Program: </label>
                              <select name="program" id="program">
                                 <option value="Choose Program">Choose Program</option>
                                 <option value="Computer Systems Engineering">Computer Systems Engineering</option>
                                 <option value="Electrical Engineering">Electrical Engineering</option>
                                 <option value="Software Engineering">Software Engineering</option>
                                 <option value="Communications Engineering">Communications Engineering</option>
                                 <option value="Biomedical and Electrical Engineering">Biomedical and Electrical Engineering</option>
                                 <option value="Special">Special</option>
                              </select>
                           </td>
                        </tr>
                        <tr>
                           <td>
                              <label>Enter Password: </label>
                              <input type="password" name="password" id="password">
                           </td>
                        </tr>
                        <tr>
                           <td id="confirm-password">
                              <label>Confirm Password: </label>
                              <input type="password" name="confirm" id="confirm">
                           </td>
                        </tr>
                        <tr>
                           <td>
                              <input type="submit" name="register" value="Register">
                              <input type="reset" value="Reset">
                           </td>
                        </tr>
                     </table>
                     <a href="login.php">Have an account? Click here to login</a>
                  </fieldset>
               </form>
            </section>
         </main>
      </div>
      <div class="profile">
         <div class="profile-info"></div>
      </div>
   </div>
</body>
</html>