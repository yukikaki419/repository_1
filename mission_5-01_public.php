<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>掲示板forum_1</title>
</head>
<body>
<?php
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//データベースからテーブルを持ってきて配列に格納する。
$sql = 'SELECT * FROM table_forum_1';
$stmt = $pdo -> query($sql);
$results = $stmt->fetchAll();
$length_lines = count($results);
$last_id = ($results[$length_lines - 1])['id'];
$newest_id = $last_id + 1 ;
$date = date("Y/m/d H:i:s");
?>
<form action="" method="post">
       新たに投稿を作成します。<br>
        <input type="text" name="nick_name" placeholder="名前"> <br>
        <input type="text" name="new_content" placeholder="内容"> <br>
        <input type="text" name="new_password" placeholder="パスワード"> <br>
        <input type="submit" name="new_submit" value="送信"> 
       
</form>
 <?php
  $nick_name = $_POST["nick_name"];
  $new_content = $_POST["new_content"];
  $new_submit = $_POST["new_submit"];
  $new_password = $_POST["new_password"];

  if(isset($new_submit)){
    if(empty($new_content)){
        echo "エラー！内容が入力されていません！";
    } 
    else{
        $date = date("Y/m/d H:i:s");
        echo "送信！";
        //ニックネームが未入力の場合。
        if(empty($nick_name)){$nick_name = "名無しさん";}
        $sql = $pdo -> prepare("INSERT INTO table_forum_1
        (id, name, comment, date, password) 
        VALUES (:id, :name, :comment, :date, :password)");
        $sql -> bindParam(':id', $newest_id , PDO::PARAM_INT);
        $sql -> bindParam(':name', $nick_name, PDO::PARAM_STR);
        $sql -> bindParam(':comment', $new_content, PDO::PARAM_STR);
        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
        $sql -> bindParam(':password', $new_password, PDO::PARAM_STR);
        // プリペアドステートメントの実行。
        $sql -> execute();

        $new_route = true ;
    }
}    
?>

<br>
<form action="" method="post">
  指定された番号の投稿を削除します。<br>
   <input type="number" name="del_num" placeholder="削除する番号"> <br>
   <input type="text" name="del_password" placeholder="パスワード"> <br>
   <input type="submit" name="del_submit" value="実行"> 
   
</form>
<?php
$del_num = $_POST["del_num"];
$del_submit = $_POST["del_submit"];
$del_password = $_POST["del_password"];
// 次は，(4)削除ボタンが押された場合。
if(isset($del_submit)){
   if(empty($del_num)){
       echo "エラー！削除する数字を入力してください！";
       }
   else { // まず，パスワードの照応。
        for($i = 0; $i <= ($length_lines-1) ; $i++ ){
            $id_to_check = ($results[$i])['id'];
            $pw_to_check = ($results[$i])['password'];
            if($id_to_check == $del_num){
                if($pw_to_check  == $del_password){
                    echo "<br>正しいパスワードが入力されました！";
                    $password_check = true ;
                }
                else{
                    echo "<br>エラー！パスワードが誤っています！";
                    $password_check = false ;
                }
            }
        }
        if($password_check == true){
            // 削除
            $sql = 'delete from table_forum_1 where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt -> bindParam(':id', $del_num, PDO::PARAM_INT);
            $stmt -> execute();
            $new_route = true ;

        } //ifの終わり
    } // elseの終わり
}//ifの終わり
?>

<br>
<form action="" method="post">
    指定された番号の投稿を編集します。<br>
    （1）編集する投稿番号とパスワードを入力し，「要請」を押しましょう。 <br>
    <input type="number" name="edit_num" placeholder="編集する番号"> <br>
    <input type="text" name="edit_password" placeholder="パスワード"> <br>
    <input type="submit" name="edit_submit" value="要請">
</form>
<?php
 $edit_num = $_POST["edit_num"];
 $edit_submit = $_POST["edit_submit"];
 $edit_password = $_POST["edit_password"];
 if(isset($edit_submit)){
    if(!isset($edit_num)){
        echo "エラー！編集する番号が入力されていません！";
        }
    else{ // まず，パスワードの照応。
        for($i = 0; $i <= ($length_lines-1) ; $i++ ){
            $id_to_check = ($results[$i])['id'];
            $pw_to_check = ($results[$i])['password'];
            if($id_to_check == $edit_num){
                if($pw_to_check == $edit_password){
                    echo "<br>正しいパスワードが入力されました！";
                    $password_check = true ;
                }
                else{
                    echo "<br>エラー！パスワードが誤っています！";
                    $password_check = false ;
                }
            }
        }
        if($password_check == true){
                echo "<br>編集する投稿番号：".$edit_num."ですね！";
                foreach($results as $row){
                $id_to_check = $row['id'];
                    if((int)$id_to_check == (int)$edit_num){
                        echo '<p>'.$row['id']."：".$row['comment'].'</p>';
                        echo '<p>'."ID：".$row['name'].'</p>';
                        echo '<p>'.$row['date'].'</p>';
                        $str_to_edit = $row['comment'];
                    }
                }
        }
    }
}

?>
<br>
<form action="" method="post">
        （2）上のフォームで入力された，編集する番号に対応する投稿を編集します。<br>
    <input type="number" name=edit_num_2 placeholder="自動的に入力"
        value =<?php if($password_check == true){echo $edit_num;}?>> <br>
    <input type="text" name="edit_str" placeholder="編集内容" value= <?php echo $str_to_edit;  ?>> <br>
    <input type="submit" name="edit_submit_2" value="編集実行">
</form>
<br>

<?php
$edit_str = $_POST["edit_str"];
$edit_submit_2 = $_POST["edit_submit_2"];
$edit_num_2 = (int)$_POST["edit_num_2"];
// (7) データレコードの編集
if(isset($edit_submit_2)){
$sql = 'UPDATE table_forum_1 SET comment=:comment WHERE id=:id';
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':comment', $edit_str , PDO::PARAM_STR);
$stmt->bindParam(':id', $edit_num_2, PDO::PARAM_INT);
$stmt->execute();
echo "編集が実行されました。";
$new_route = true ;
}




?>

<?php 
// read_table の処理。
 if($new_route = true){
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    $sql = 'SELECT * FROM table_forum_1';
    $stmt = $pdo -> query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo '<p>'.$row['id']."：".$row['comment'].'</p>';
        echo '<p>'."ID：".$row['name'].'</p>';
        echo '<p>'.$row['date'].'</p>';
    }
}


?>
</body>
</html>
