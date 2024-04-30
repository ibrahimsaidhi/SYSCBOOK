SYSC 4504: Assignment 2
Ibrahim Said
101158275

Known errors:

profile.php 
    - If you do not select a new avatar or program, the program crashes due to their $_POST keys never being used

index.php
    - Adding the " character in a post crashes the program. This is due to the syntax of the SQL command when inserting
      into the database table
