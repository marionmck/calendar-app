<?php

/*
 * @author Mario Nemecek
 *
*/

include "../config/config.php";

$dbConn = getConnection();

function getEvents($dbConn) {
  $userID = filter_has_var(INPUT_POST, 'userID') ? $_POST['userID'] : null;
  $startDate = filter_has_var(INPUT_POST, 'startDate') ? $_POST['startDate'] : null;
  $endDate = filter_has_var(INPUT_POST, 'endDate') ? $_POST['endDate'] : null;

  $query = "SELECT event_id, user_id, title, description, location, start_date_time, end_date_time
            FROM events
            WHERE start_date_time >= :startDate AND end_date_time <= :endDate
            AND user_id = :userID";

  $stmt = $dbConn->prepare($query);
  $stmt->bindValue(':startDate', $startDate);
  $stmt->bindValue(':endDate', $endDate);
  $stmt->bindValue(':userID', $userID);
  $stmt->execute();
  $recordSet = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $nRecords = count($recordSet);
  return json_encode(array("count"=>$nRecords, "data"=>$recordSet));
}
echo getEvents($dbConn);

?>
