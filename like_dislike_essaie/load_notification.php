<?php
//load_notification.php
include ('database_connection.php');
if (isset($_POST["view"])) {
    $query = "
 SELECT user_content_like.user_id, content.description, user_details.user_name, user_details.user_image FROM user_content_like
 INNER JOIN content 
  ON content.content_id = user_content_like.content_id 
 INNER JOIN user_details 
  ON user_details.user_id = user_content_like.user_id 
  WHERE content.user_id = :user_id 
  AND user_content_like.status = :status 
  ORDER BY user_content_like.user_content_like_id DESC
 ";
    $statement = $connect->prepare($query);
    $statement->execute(
        array(
            ':user_id' => $_SESSION['user_id'],
            ':status' => 'not-seen'
        )
    );
    $result = $statement->fetchAll();
    $total_row = $statement->rowCount();
    $output = '';
    if ($total_row > 0) {
        foreach ($result as $row) {
            $user_name = '';
            if ($row['user_id'] == $_SESSION['user_id']) {
                $user_name = '<img src="images/' . $row["user_image"] . '" class="img-thumbnail" width="40" height="40" /> You have';
            } else {
                $user_name = '<img src="images/' . $row["user_image"] . '" class="img-thumbnail" width="40" height="40" /> ' . $row['user_name'] . ' has ';
            }
            $output .= '
   <li>
     <a href="#">
      <strong>' . $user_name . '</strong> like your post "' . substr($row["description"], 0, 25) . '"
     </a>
    </li>
   ';
        }
    } else {
        $output .= '
  <li><a href="#" class="text-bold text-italic">No Notification Found</a></li>
  ';
    }
    if ($_POST["view"] != '') {
        $select_query = "
  SELECT * FROM content WHERE user_id = :user_id
  ";
        $statement = $connect->prepare($select_query);
        $statement->execute(
            array(
                ':user_id' => $_SESSION['user_id']
            )
        );
        $result = $statement->fetchAll();
        foreach ($result as $row) {
            $update_query = "
   UPDATE user_content_like 
    SET status = 'seen' 
    WHERE content_id = :content_id 
    AND status = :status
   ";
            $statement = $connect->prepare($update_query);
            $statement->execute(
                array(
                    ':content_id' => $row['content_id'],
                    ':status' => 'not-seen'
                )
            );
        }
    }
    $statement = $connect->prepare($query);
    $statement->execute(
        array(
            ':user_id' => $_SESSION['user_id'],
            ':status' => 'not-seen'
        )
    );
    $total_row = $statement->rowCount();
    $data = array(
        'notification' => $output,
        'unseen_notification' => $total_row
    );
    echo json_encode($data);
}
