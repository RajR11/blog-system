<?php
/**
 * Created by PhpStorm.
 * User: Raj
 * Date: 31-03-2019
 * Time: 12:48
 */
require_once('utils/post-utils.php');
require_once('utils/generic-utils.php');
require_once ('utils/user-utils.php');
$user_info = SessionUtils::check_user_login_status();
if (!$user_info){
    header('Location: ../login.php');
    exit();
}

$pageMode='master';
$detailID=null;
function processRequest() {
    global $pageMode, $detailID;
    $action = @$_GET['action'];
    if ($action == 'detail') {
        // get the user id
        $post_id = @$_GET['post_id'];

        if (preg_match('|^[0-9]+$|i', $post_id)) {

            $pageMode = 'detail';
            $detailID = $post_id;
        }
    }
}
processRequest();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="jquery-3.3.1.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>Edit Post</title>
</head>
<style>
    table{
        margin: auto;

    }
    .nav-bar{
        width: 100%;
        height: 20%;
        background: blue;
        color: white;
    }
    #sitemap{
        width: 20%;
        height: 100%;
        border: 2px solid black;
        float: left;
    }
    #main{
        width: 80%;
        height: 100%;

        float: right;
    }
</style>
<body>
<nav class="nav-bar text-center">
    <h1>Edit Post</h1>
    <div class="user-function">
        <?php
        $user_info=SessionUtils::check_user_login_status();
        if($user_info){
            echo '<h3 style="text-align: center;">Welcome '.$user_info['display_name'].'</h3>';
        }
        ?>
    </div>
</nav>
<div id="sitemap">

    <h4 class="text-center">Site Navigation</h4>
        <ul>
            <li><a href="index.php">Timeline</a></li>
            <li><a href="edit_user_details.php?action=detail&user_id=<?php echo $user_info['user_id'];?>">Edit User</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
</div>
<div id="main">
    <?php if ($pageMode == 'detail') {

        // Only display the post detail
        $searched_post=PostUtils::get_post_detail($detailID);

        if ($searched_post['user_id']!=$user_info['user_id'] ){
            exit();
        }
        if(!$searched_post){
            $errors=OutputUtils::get_display_errors();
            foreach ($errors as $error){
                echo "<h3>".$error['message']."</h3>";
            }
        }else{  ?>
            <div class="col-md-12 update-inputs"><h3 class='text-center'>Post Found</h3>
                <table cellpadding="10px">
                    <tr>
                        <td><h5>Post ID: </h5></td>
                        <td class="text-info"><h6><?php echo $searched_post['post_id'];?></h6></td>
                    </tr>
                    <tr>
                        <td><h5>User ID: </h5></td>
                        <td class="text-info"><h6><?php echo $searched_post['user_id'];?></h6></td>
                    </tr>
                    <tr>
                        <td><h5>Post header: </h5></td>
                        <td class="text-info"><h7><input id="edit" type="text" value="<?php echo $searched_post['post_header'];?>"></h7></td>
                    </tr>
                    <tr>
                        <td><h5>Content: </h5></td>
                        <td class="text-info"><h7><textarea id="edit"><?php echo $searched_post['content'];?></textarea></h7></td>
                    </tr>
                    <tr>
                        <td><h5>likes: </h5></td>
                        <td class="text-info"><h6><?php echo $searched_post['likes'];?></h6></td>
                    </tr>
                    <tr>
                        <td id="<?php echo $searched_post['post_id'];?>"><button id="update-button" class="btn btn-success" disabled>Update Post</button></td>
                    </tr>
                </table>
            </div>
        <?php  } } ?>
</div>
<script>
    // already have jquery
    $(function() {
        var update_button=$('#update-button');

        $('.update-inputs').find('input,textarea').keyup(function () {
            update_button.attr('disabled',false);
        });
        update_button.click(function () {
            if(confirm('Are you sure you want to update the post?')){
                var content=$('.update-inputs').find('textarea').val();
                var post_header=$('.update-inputs').find('input').val();
                var post_id=$(this).parent().attr('id');
                console.log(post_id);
                $.ajax('ajax.php?action=edit_post',{
                    method: 'POST',
                    data:{
                        post_id:post_id,
                        content:content,
                        post_header:post_header
                    }
                    ,
                    success: function (response_json) {
                        response=JSON.parse(response_json);
                        alert(response.message);
                        update_button.attr('disabled',true);
                    },
                    error: function (jqXHR, textStatus, errorThrown ) {
                        console.log( 'Could not get posts, server response: ' + textStatus + ': ' + errorThrown );
                    }
                });
            }
        });
    });
</script>
</body>
</html>