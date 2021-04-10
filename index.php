<?php
date_default_timezone_set('Asia/Kolkata');
session_start();

if (isset($_GET['logout'])) {

    //Simple exit message
    $logout_message = "<div class='msgln'><span class='left-info'>User <b class='user-name-left'>" . $_SESSION['name'] . "</b> has left the chat session.</span><br></div>";
    file_put_contents("log.html", $logout_message, FILE_APPEND | LOCK_EX);
    $content = file_get_contents('clientnames.txt');
    $content = str_replace($_SESSION['name']."<br>\n", '', $content);
    file_put_contents('clientnames.txt', $content);

    session_destroy();
    header("Location: index.php"); //Redirect the user
}

if (isset($_POST['enter'])) {
  $content = file_get_contents('clientnames.txt');
    if ($_POST['name'] != "" && strpos($content,$_POST['name'])==false) {
        $_SESSION['name'] = stripslashes(htmlspecialchars($_POST['name']));

        $login_message = "<div class='msgln'><span class='join-info'>User <b class='user-name-join'>" . $_SESSION['name'] . "</b> has joined the chat session.</span><br></div>";
       file_put_contents("log.html", $login_message, FILE_APPEND | LOCK_EX);
      file_put_contents("clientnames.txt", $_SESSION['name']."<br>\n", FILE_APPEND | LOCK_EX);
    } 
    elseif(strpos($content,$_POST['name'])!=false) {
        echo '<div class="alert alert-danger" role="alert">
        Name Already in use write different name!
        </div>';
    }
    else {
        echo '<div class="alert alert-danger" role="alert">
        Please Write your name to continue!
        </div>';
    }
}

function loginForm()
{
    echo
    '<div id="loginform">
    <p>Please enter your name to continue!</p>
    <form action="index.php" method="post">
      <label for="name"></label>
      <input type="text" name="name" id="name" placeholder="Name"/>
      <input type="submit" name="enter" id="enter" value="Enter" />
    </form>
  </div>';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />

    <title>Web based chat using php</title>
    <meta name="description" content="Web based chat using php<" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css" />
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
</head>

<body>

    <?php
    if (!isset($_SESSION['name'])) {
        loginForm();
    } else {
    ?>
        <nav class="navbar navbar-expand-sm navbar-dark fixed-top" style="background:#ec6f66d8;backdrop-filter:blur(15px)">
          <a class="navbar-brand" href="#">Divya Kumar Baid</a>
          <div class="collapse navbar-collapse">
           <div class="navbar-nav">
              <a href="https://github.com/DivyaKumarBaid" class="nav-item nav-link">Github</a>
              <a href="https://www.linkedin.com/in/divya-kumar-baid-98a087200/" class="nav-item nav-link">LinkedIn</a>
              <a href="https://twitter.com/DivyakumarBaid1?s=09" class="nav-item nav-link">Twitter</a>
            </div>
           </div>
        </nav>
   <div class="container-fluid">
      <div class="row">
      <div class="col-10">
        <div id="wrapper">
              <div id="menu">
                  <p class="welcome"><span id="n"><b><?php echo $_SESSION['name']; ?></b></span></p>
                  <p class="logout"><a id="exit" href="#"><button type="button" class="btn btn-outline-dark">Exit</button></a></p>
              </div>
              <div id="chatbox">
                 <?php
                   if (file_exists("log.html") && filesize("log.html") > 0) {
                    $contents = file_get_contents("log.html");
                    echo $contents;
                    }
                  ?>
              </div>

              <form name="message" action="">
                <input name="usermsg" type="text" id="usermsg" required />
                <input name="submitmsg" type="submit" id="submitmsg" value="Send" />
              </form>
        </div>
      </div>
      <div id="col">
        <div id="usersname">
            <?php
              if (file_exists("clientnames.txt") && filesize("clientnames.txt") > 0) {
                    $contents = file_get_contents("clientnames.txt");
                    echo $contents;
              }
            ?>
        </div>
        </div>
        </div>
        </div>
       
        
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script type="text/javascript">
            // jQuery Document
            $(document).ready(function() {
                $("#submitmsg").click(function() {
                    var clientmsg = $("#usermsg").val();
                    $.post("post.php", {
                        text: clientmsg
                    });
                    $("#usermsg").val("");
                    return false;
                });

                function loadLog() {
                    var oldscrollHeight = $("#chatbox")[0].scrollHeight - 20; //Scroll height before the request

                    $.ajax({
                        url: "log.html",
                        cache: false,
                        success: function(html) {
                            $("#chatbox").html(html); //Insert chat log into the #chatbox div

                            //Auto-scroll           
                            var newscrollHeight = $("#chatbox")[0].scrollHeight - 20; //Scroll height after the request
                            if (newscrollHeight > oldscrollHeight) {
                                $("#chatbox").animate({
                                    scrollTop: newscrollHeight
                                }, 'normal'); //Autoscroll to bottom of div
                            }
                        }
                    });
                }
                function loadNames() {
                    var oldscrollHeight = $("#usersname")[0].scrollHeight - 20; //Scroll height before the request

                    $.ajax({
                        url: "clientnames.txt",
                        cache: false,
                        success: function(html) {
                            $("#usersname").html(html); //Insert chat log into the #chatbox div

                            //Auto-scroll           
                            var newscrollHeight = $("#usersname")[0].scrollHeight - 20; //Scroll height after the request
                            if (newscrollHeight > oldscrollHeight) {
                                $("#usersname").animate({
                                    scrollTop: newscrollHeight
                                }, 'normal'); //Autoscroll to bottom of div
                            }
                        }
                    });
                }

                setInterval(loadLog,100);
                setInterval(loadNames,10000);

                $("#exit").click(function() {
                    var exit = confirm("Are you sure you want to end the session?");
                    if (exit == true) {
                        window.location = "index.php?logout=true";
                    }
                });
            });
        </script>
       
</body>

</html>
<?php
    }
?>