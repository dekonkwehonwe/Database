<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
        <title>Database exam. Superheroes dating site</title>
</head>
<body>
    <style>
        body {
            background-color: darksalmon;
        }
        img {
            height: 200px;
            width: auto;
        }
        .usercard {
            margin: 30px;
            background-color: white;
            float: left;
            border-radius: 10px;
            padding: 20px;
        }
        .comment {
            background-color: #eee;
            padding: 10px;
        }
        .updateprofileform {
            padding: 10px;
            margin: 10px;
            background-color: burlywood;
            border-radius: 10px;
            float: left;
        }
        .usernameShow {
           font-size: 150%;
            text-align: center;
            margin: 2px;
        }
        .like-btn img, .message-btn img{
            height: 30px;
            width: auto;
        }
        .like-btn, .message-btn {
             opacity: 0.5;
            font-size: 150%;
        }
        .like-btn:hover, .message-btn:hover  {
            opacity: 1;
        }
        .inbox p{
            background-color: antiquewhite;
            float: left;
        }
    </style>
    
    
    <?php   
    $db = new PDO('mysql:host=localhost;dbname=superheroes-dating;charset=utf8mb4', 'root', ''); 

    
         if (isset($_POST['submitButton'])) {
             $sqlupdate =  "UPDATE user 
                            SET username = '".$_POST['name']."',
                                gender = '".$_POST['gender']."',
                                age = ".$_POST['age'].", 
                                superpower = '".$_POST['superpower']."'
                            WHERE id=1";
             $query = $db->prepare( $sqlupdate );
             $query->execute();
             $results = $query->fetchAll();
         }
         if (isset($_POST['submitMessageButton'])) {
             sendMessage($db, $_POST['message'], $_POST['topic'],  $_POST['userId']);
         }
    
        $sql = " SELECT * FROM user";
        $query = $db->prepare( $sql );
        $query->execute();
        $results = $query->fetchAll();
    
    
        foreach( $results as $row ){  
        ?>
        <section class="usercard"> 
        <p>
        <?php       
            //var_dump( $row );
            echo "<p class='usernameShow'>".$row["username"]. "</p>";
            
            echo $row["gender"]. ", "
                .$row["age"] ."<br>"
                .$row["superpower"]."<br>";
                   
            
            ?>
            <img src="img/like.png" style="height:20px; width:auto;">
            
            
            <?php
            echo $row["likes"]."<br>"; ?>
            
            <?php
            echo '<img src="img/'.$row["picture"].'" >';
            ?>
        </p>
        
        <button class="like-btn" onClick="function() {document.write(' <?php addLike($db, $row['liked'], $row['likes'], $row['username'] ); ?>'; );}">
            Like! <img  src="img/like.png" >  
        </button>
        <button class="message-btn" onclick="" > 
            Message me! <img  src="img/message.png" >  
        </button>
        <br>    
        <button class="sendgift-btn" onClick=""> 
            Send Gift!
        </button>
            
        <br><br>
        Comments    
        <p class="comment">
            <?php 
                echo $row["comment"]."<br>";
            ?>
        </p>
            
        <form name="form" action="" method="post">
            <input type="text" name="addcomment" id="addcomment" >
            <section style="clear: both;" id="submitbtnsection" >
                  <input type="submit" name="submitButton" id="submitButton" value="Add Comment"/>
            </section>
        </form>    
        </section>
        <?php
        }    
    
    
    function addLike ($db, $liked, $likes, $name) {
        $addlike = "UPDATE user 
                    SET liked=1, likes = likes + 1
                    WHERE username='".$name."';";
        $removelike = "UPDATE user 
                    SET liked=0, likes = likes - 1
                    WHERE username='".$name."';";
        
        if ($liked == 0) {
            $sql = $addlike;
        }
        else {
            $sql = $removelike; 
        }
        $query = $db->prepare( $sql );
        $query->execute();
        
    };
    
    function sendMessage ($db, $content, $topic, $user_id) {
        $insert = "INSERT INTO `message` (`Id`, `content`, `topic`, `user_id`) VALUES (NULL, '".$content."', '".$topic."', '".$user_id."');";
            
        $query = $db->prepare( $insert );
        $query->execute();
        $results = $query->fetchAll();
        
    }
    function displaymessage ($db) {
        $select = "SELECT user.id, user.username, message.content, message.topic, message.user_id
        FROM user, message
        WHERE  user.id = user_id AND user_id = 0";
        
        $query = $db->prepare( $select );
        $query->execute();
        $results = $query->fetchAll();
        
        foreach ($results as $row) {
            echo "<p>"."<h2>". $row["topic"]."</h2>"; 
            echo $row["content"]."</p>";
        }
        
    }
    
    ?>
    
    <script>
    function addLike () {
        document.write(' <?php addLike($db, $row['liked'], $row['likes'], $row['username'] ); ?>'; );
    }
    </script>
   
    <form class="updateprofileform" action="" method="post" >
        <br><h1>Update Profile</h1><br>
            <label>Name</label><br/>
            <input type="text" name="name" id="name" maxlength="50"/> <br />

            <label>Gender</label><br/>
              <input type="radio" name="gender" value="male" checked> Male<br>
              <input type="radio" name="gender" value="female"> Female<br>
              <input type="radio" name="gender" value="other"> Other<br>
           

            <label>Age</label><br/>
            <input type="number" name="age" id="age" min="1" max="200"/> <br />
            <!--<textarea name="comments" id="comments" rows="4" cols="50"></textarea><br />-->
        
            <label>Superpower</label><br/>
            <input type="text" name="superpower" id="superpower" maxlength="100" /> <br />

            <!--<label>image</label><br/>
            <input type="text" name="img" id="img" maxlength="100" /> <br />-->

            <section style="clear: both;" id="submitbtnsection" >
                  <input type="submit" name="submitButton" id="submitButton" value="UPDATE"/>
            </section>
    </form>
    
    <section class="inbox">
        <?php 
            displaymessage($db);
        ?>
        
        <form class="updateprofileform" action="" method="post" >
        <br><h2>Send message</h2><br>
            <label>Topic</label><br/>
            <input type="text" name="topic" id="topic" maxlength="50"/> <br />

            <label>Message</label><br/>
            <input type="text" name="message" id="message" maxlength="100" /> <br />
            
            <label>User</label><br/>
              <input type="radio" name="userId" value="0" checked> LoggedUser<br>
              <input type="radio" name="userId" value="2"> BattyBat1489<br>
              <input type="radio" name="userId" value="3"> Naruto<br>
              <input type="radio" name="userId" value="4"> SpidyOhYeah<br>
              <input type="radio" name="userId" value="5"> Wolvie257<br>
            <section style="clear: both;" id="submitbtnsection" >
                  <input type="submit" name="submitMessageButton" id="submitMessageButton" value="SEND"/>
            </section>
    </form>
    </section>
    
    
    
</body>
</html>