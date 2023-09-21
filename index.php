 <?php
    //// Displaying errors
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);

    //// Load up the DB connection
    require_once('../project2/_includes/db.php');

    //setting up users
    // $user1 = "amelie";
    // $passw1 = "pogo1234";
    // $user2 = "mary";
    // $passw2 = "flower12";




    //// POST check & content check via if conditional...
    if (isset($_POST["username"]) && !empty($_POST["username"]) && $_POST["username"] != " ") {

        /// Assigning values to variables as well as "sanitizing" the inputs
        $username = trim(strip_tags($_POST["username"]));
        $password = trim(strip_tags($_POST["password"]));

        /// DB SELECT OPERATION
        $user = $db->real_escape_string($username);
        $passw = $db->real_escape_string($password);

        $query = "SELECT * FROM zjhu5_messages WHERE username = '$username'";
        $result = mysqli_query($db, $query);

        if (!$result) {
            die("Database query failed: " . mysqli_error($db));
        }

        if (mysqli_num_rows($result) == 0) {
            echo ("<p>user not found</p>");
            exit();
        }

        $user_data = mysqli_fetch_assoc($result);

        // echo("<pre>");
        // var_dump($user_data);
        // echo("</pre>");




        //echo "Form data is OK";
        //if(($username === $user1 && $passw1 === $password) || ($username === $user2 && $passw2 === $password)) {
        //if($password == $user_data['password']) -> won't work, because $password isn't hashed yet
        if (password_verify($password, $user_data['password'])) {
            session_start();
            $_SESSION["username"] = $username;
            $_SESSION["id"] = $user_data['id'];
            //Boolean switch type variable
            $_SESSION["logged_in"] = true;

            print "Hi there, " . $_SESSION["username"];
        } else {
            echo ("<p>Password verification failed</p>");
            exit();
        }
    }


    // if(isset($_GET["logout"])) {
    //     session_start();
    //     session_destroy();
    //     echo 'You have been logged out. <a href="login.php">Go back</a>';
    // }

    ?>

 <!DOCTYPE html>
 <html lang="en">

 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>PHP Simple Login</title>
     <link rel="stylesheet" href="./asset/css/chatapp.css">

 </head>

 <body>


     <?php if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) : ?>

         <img id="logo" src="img/Logo.svg" alt="">
         <header id="welcome-box">
             <h1>Welcome <?php print $_SESSION["username"]; ?></h1>
             <a href="logout.php">Log out</a>
         </header>

         <p id="messages">l o a d i n g</p>

         <form id="msg-box"> <!-- action="views/chat.php?post" method="post" -->
             <input type="text" placeholder="type a message" name="msg">
             <input type="submit" value="Post it!">
         </form>

         <script>
             const myMessages = document.querySelector("#messages");

             let lastMsgId = 0;
             /**
              * Load ALL messages
              */
             let audio = new Audio("/asset/media/ping-82822.mp3");

             //let msgData; 
             function loadMessages() {
                 fetch("views/chat.php?loadAll")
                     .then((result) => result.json())
                     .then((data) => {
                         console.log(data);
                         //msgData = data;
                         myMessages.innerHTML = "";

                         data.forEach(element => {
                             let who = (element.uid !== window.myUserId) ? 'they' : 'me';
                             let msgBox = `
                <span class="msg ${who}" data-id="${element.id}">
                    ${element.message}<br>
                    <i>${element.uname}</i>
                </span>
                <br>
            `;
                             myMessages.innerHTML += msgBox;
                         });

                         lastMsgId = data[data.length - 1].id;


                     });
             }
             loadMessages();

             /**
              * Sending A message
              */

             const chatForm = document.getElementById("msg-box");

             chatForm.addEventListener("submit", (event) => {
                 event.preventDefault();

                 let chatFormData = new FormData(chatForm);

                 fetch("views/chat.php?post", {
                         body: chatFormData,
                         method: "POST"
                     })
                     .then((res) => {
                         refreshMessages();
                     });

                 chatForm.reset();
             });

             /**
              * Refreshing the messages
              */

             function refreshMessages() {

                 let refreshFormData = new FormData();
                 refreshFormData.set('lastMsgId', lastMsgId);

                 fetch("views/chat.php?resfreshMessages", {
                         body: refreshFormData,
                         method: "POST"
                     })
                     .then((res) => res.json())
                     .then((data) => {
                         console.log(data);

                         if (data.length !== 0) {
                             audio.play();
                             data.forEach(element => {
                                 let who = (element.uid !== window.myUserId) ? 'they' : 'me';
                                 let msgBox = `
                    <span class="msg ${who}" data-id="${element.id}">
                        ${element.message}<br>
                        <i>${element.uname}</i>
                    </span>
                    <br>
                `;
                                 myMessages.innerHTML += msgBox;
                             });

                             lastMsgId = data[data.length - 1].id;
                         }
                     });

             }

             setInterval(refreshMessages, 5000);
             window.myUserId = <?php echo ($_SESSION["id"]); ?>;
         </script>




     <?php
        elseif ($_SERVER['REQUEST_METHOD'] == "POST") : ?>

         <h2>oops, that didn't work... </h2>
         <a href="./index.php">Try again.</a>

     <?php else : ?>

         <h1>Please log in first</h1>
         <form action="./index.php" method="post">
             <input type="text" name="username" placeholder="User name please" pattern=".{3,}" required>
             <input type="password" name="password" placeholder="Password please" pattern=".{8,}" required>
             <input type="submit" value="Log me in!">
         </form>

     <?php endif; ?>



 </body>

 </html>