<?php
/**
 * Created by PhpStorm.
 * User: Raj
 * Date: 31-03-2019
 * Time: 12:48
 */
require ('../utils/post-utils.php');
require ('../utils/generic-utils.php');
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
    <script src="../jquery-3.3.1.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>Posts Panel</title>
</head>
<style>
    table{
        margin: auto;

    }
</style>
<body>
<h1 class="text-primary text-center bg-dark">Posts Panel</h1>
<?php if ($pageMode == 'master') {
    $posts=PostUtils::posts();
    // Full post table goes here
    ?>
    <br>
    <table border="1px solid black" class="text-center" cellpadding="10px">
        <tr><th>Post ID</th>
            <th>User ID</th>
            <th>Content</th>
            <th>Likes</th>
            <th>Post Header</th>
            <th></th>
        </tr>
        <?php    foreach ($posts as $post) { ?>
            <tr>
                <td><?php echo $post['post_id'];?></td>
                <td><?php echo $post['user_id'];?></td>
                <td><?php echo $post['content'];?></td>
                <td><?php echo $post['likes'];?></td>
                <td><?php echo $post['post_header'];?></td>
                <td><a class="action" href="<?php echo 'posts.php?action='.GenericUtils::u('detail').'&post_id='.$post['post_id']; ?>">Details</a></td>
            </tr>
        <?php    } ?>
    </table>


<?php } elseif ($pageMode == 'detail') {

    // Only display the user detail
    $searched_post=PostUtils::get_post_detail($detailID);
    if(!$searched_post){
        $errors=OutputUtils::get_display_errors();
        foreach ($errors as $error){
            echo "<h3>".$error['message']."</h3>";
        }
    }else{ ?>

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

   <?php }
}?>
        <script>
            // already have jquery
            $(function() {
                var update_button=$('#update-button');

                $('.update-inputs').find('input,textarea').change(function () {
                    update_button.attr('disabled',false);
                });
                update_button.click(function () {
                    if(confirm('Are you sure you want to update the post?')){
                        var content=$('.update-inputs').find('textarea').val();
                        var post_header=$('.update-inputs').find('input').val();
                        var post_id=$(this).parent().attr('id');
                        console.log(post_id);
                        $.ajax('../ajax.php?action=edit_post',{
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