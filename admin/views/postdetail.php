<?php
    $header_title = '投稿詳細';
    include('../admin/views/layouts/header.php');
?>
    <h1>投稿詳細</h1>
    <ul>
        <li>
            投稿ID:
            <?php echo $post['id'] ?><br />
            名前：
            <div id="post_name">
            <?php if (!empty($post['user_id'])) : ?>
                <?php echo h($current_user_name['name']); ?><br />
            <?php else : ?>
                <?php echo h($post['name']) ?><br />
            <?php endif ?>
            </div>
            本文 :
            <font id="post_color" color="<?php echo h($post['color']) ?>">
                <?php echo h($post['comment']) ?>
            </font><br />
            画像：
            <?php if (!empty($post['picture'])) : ?>
                <img src="../images/posts/<?php echo h($post['picture']) ?>" width="300" height="200"><br />
            <?php else : ?>
                なし<br />
            <?php endif ?>
            時間：
            <?php echo h($post['created_at']) ?><br />
            ---------------------------------------------<br />
        </li>
    </ul>
    <form action="deleted.php" method="post" id="deleteform">
        <input type="hidden" value="<?php echo $post['id'] ?>" name="post_id">
        <input type="submit" value="投稿削除">
    </form>
    <input type="button" id="btn" value="投稿編集" class="show-modal" data-id="<?php echo $post['id'] ?>">
    <h2>レス一覧</h2>
    <table border="2">
        <tr>
            <th>ID</th>
            <th>投稿日時</th>
            <th>名前</th>
            <th>本文</th>
            <th>編集リンク</th>
            <th>削除ボタン</th>
        </tr>
        <?php foreach ($reply_posts as $reply_post) : ?>
        <tr>
            <td>
                <?php echo $reply_post['id'] ?>
            </td>
            <td>
                <?php echo h($reply_post['created_at']) ?>
            </td>
            
            <td id="reply_name_<?php echo h($reply_post['id']) ?>">
            <?php if (isset($reply_post['user_id'])) : ?>
                <?php echo h($user_names[$reply_post['user_id']]) ?>
            <?php else : ?>
                <?php echo h($reply_post['name']) ?>
            <?php endif ?>
            </td>
            <td>
                <font id="reply_font_<?php echo h($reply_post['id']) ?>" color="<?php echo $reply_post['color'] ?>">
                    <?php echo h($reply_post['comment']) ?>
                </font>
            </td>
            <td>
                <input type="button" id="edit_reply_btn" value="レス編集" class="show-reply-modal" data-reply="<?php echo $reply_post['id'] ?>">
            </td>
            <td>
                <form action="reply_delete.php" method="post" id="delete_reply_form">
                    <input type="hidden" value="<?php echo $reply_post['id'] ?>" name="reply_id">
                    <input type="hidden" value="<?php echo $post['id'] ?>" name="post_id">
                    <input type="submit" value="削除">
                </form>
            </td>
        </tr>
        <?php endforeach ?>
    </table>
    <!--投稿モーダル-->
    <div id="modalwin" class="modalwin hide">
        <a herf="#" class="modal-close"></a>
        <h1>投稿編集</h1>
        <div class="modalwin-contents">
            <input id="input_id" type="hidden" name="name" value="">
            <input id="input_name" type="text" name="name" value="">
            <br />
            <textarea id="input_comment" name="comment" rows="4" cols="20"></textarea><br />
            <img id="img" src="" width="30" height="30"><br />
            <select id="input_color" name="color">
            <?php foreach($select_color_options as $key => $value) : ?>
                <option value="<?php echo $key ?>"><?php echo $value; ?></option>
            <?php endforeach ?>
            </select>
            <br />
            <button id="ajax">編集</button>
            <br />
            <button id="close">閉じる</button>
        </div>
    </div>
    <!--レスモーダル-->
    <div id="modalwin2" class="modalwin hide">
        <a herf="#" class="modal-close"></a>
        <h1>レス編集</h1>
        <div class="modalwin-contents">
            <input id="reply_id" type="hidden" name="name" value="">
            <input id="reply_name" type="text" name="name" value="">
            <br />
            <textarea id="reply_comment" name="comment" rows="4" cols="20"></textarea><br />
            <img id="reply_img" src="" width="30" height="30"><br />
            <select id="reply_color" name="color">
            <?php foreach($select_color_options as $key => $value) : ?>
                <option value="<?php echo $key ?>"><?php echo $value; ?></option>
            <?php endforeach ?>
            </select>
            <br />
            <button id="reply_ajax">編集</button>
            <br />
            <button id="reply_close">閉じる</button>
        </div>
    </div>
    
    <script type="text/javascript">
    
        $(function() {
            $('.show-modal').on('click', function() {
                
                var id = $(this).data('id');
                
                $.ajax({
                    url:'get_ajax.php',
                    type:'GET',
                    data:{
                        'id': id,
                    },
                    dataType: 'json',
                }).done(function(post) {
                    $("#input_id").val(post.id);
    				$("#input_name").val(post.name);
    				$("#input_comment").val(post.comment);
    				if (post.picture !== null) {
    				    $("#img").attr('src', '/kadai-ibg/images/posts/' + post.picture);
    				} else {
    				    $("#img").attr('src', '/kadai-ibg/images/posts/noimage.png');
    				}
    				$("#input_color").val(post.color);
                }).fail(function()  {
                    alert("通信に失敗しました");
                }); 
            });

            $('#ajax').on('click', function() {
                
                $.ajax({
                    url:'edit_ajax.php',
                    type:'POST',
                    data:{
                        'id':$("#input_id").val(),
                        'name':$("#input_name").val(),
                        'comment':$("#input_comment").val(),
                        'color':$("#input_color").val(),
                    },
                    dataType: 'json',
                }).done(function(response) {
                    if (response['status'] === true) {
                        alert("編集しました。");
                        var post = response['post'];
                        $('#post_name').text(post['name']);
                        $('#post_color').text(post['comment']);
                        $('#post_color').attr('color',　post['color']);
                    } else {
                        alert(response['errors']);
                    }
                    
                }).fail(function()  {
                    alert("通信に失敗しました");
                });
            });
            
            
             $('.show-reply-modal').on('click', function() {
                
                var reply_id = $(this).data('reply');
                
                $.ajax({
                    url:'get_reply_ajax.php',
                    type:'GET',
                    data:{
                        'reply_id': reply_id,
                    },
                    dataType: 'json',
                }).done(function(reply_post) {
                    
                    $("#reply_id").val(reply_post.id);
    				$("#reply_name").val(reply_post.name); 
    				$("#reply_comment").val(reply_post.comment);
    				if (reply_post.picture !== null) {
    				    $("#reply_img").attr('src', '/kadai-ibg/images/replies/' + reply_post.picture);
    				} else {
    				    $("#reply_img").attr('src', '/kadai-ibg/images/replies/noimage.png');
    				}
    				$("#reply_color").val(reply_post.color);
                }).fail(function() {
                    alert("通信に失敗しました");
                });
            });
                
            $('#reply_ajax').on('click', function() {
                
                $.ajax({
                    url:'edit_reply_ajax.php',
                    type:'POST',
                    data:{
                        'id':$("#reply_id").val(),
                        'name':$("#reply_name").val(),
                        'comment':$("#reply_comment").val(),
                        'color':$("#reply_color").val(),
                    },
                    dataType: 'json',
                }).done(function(response) {
                    if (response['status'] === true) {
                        alert("編集しました。");
                        var reply = response['reply']
                        $('#reply_name_' + reply['id']).text(reply['name']);
                        $('#reply_font_' + reply['id']).text(reply['comment']);
                        $('#reply_font_' + reply['id']).attr('color',　reply['color']);
                    } else {
                        alert(response['errors']);
                    }
                    
                }).fail(function()  {
                    alert("通信に失敗しました");
                });
            });

        });
    </script>
<?php
    include('../admin/views/layouts/footer.php');
?>