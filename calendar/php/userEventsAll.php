<?php

/*
 * @author Mario Nemecek
 *
*/

include "../config/config.php";

$dbConn = getConnection();

function getEvents($dbConn) {
  $userID = filter_has_var(INPUT_POST, 'userID') ? $_POST['userID'] : null;

  $query = "SELECT event_id, start_date_time, end_date_time
            FROM events
            WHERE user_id = :userID";

  $stmt = $dbConn->prepare($query);
  $stmt->bindValue(':userID', $userID);
  $stmt->execute();
  $recordSet = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $nRecords = count($recordSet);
  return json_encode(array("count"=>$nRecords, "data"=>$recordSet));
}
echo getEvents($dbConn);

?>
