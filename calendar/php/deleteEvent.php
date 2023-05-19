<?php

/*
 * @author Mario Nemecek
 *
*/

include "../config/config.php";

$dbConn = getConnection();

function deleteEvent($dbConn) {
  $id = filter_has_var(INPUT_POST, 'event_id') ? $_POST['event_id'] : null;

  $query = "DELETE FROM events WHERE event_id = :id";

  $stmt = $dbConn->prepare($query);
  $stmt->bindValue(':id', $id);
  $stmt->execute();
}

deleteEvent($dbConn);

?>
