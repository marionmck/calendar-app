<?php

/*
 * @author Mario Nemecek
 *
*/

include "../config/config.php";

$dbConn = getConnection();

function updateEvent($dbConn) {
  $user_id = filter_has_var(INPUT_POST, 'user-id') ? $_POST['user-id'] : null;

  $title = filter_has_var(INPUT_POST, 'event-title') ? $_POST['event-title'] : null;
  $location = filter_has_var(INPUT_POST, 'event-location') ? $_POST['event-location'] : null;
  $day = filter_has_var(INPUT_POST, 'event-date') ? $_POST['event-date'] : null;
  $timeStart = filter_has_var(INPUT_POST, 'time-start') ? $_POST['time-start'] : null;
  $timeEnd = filter_has_var(INPUT_POST, 'time-end') ? $_POST['time-end'] : null;
  $description = filter_has_var(INPUT_POST, 'description') ? $_POST['description'] : null;

  $start_date_time = $day.' '.explode(' ', $timeStart)[0];
  $end_date_time = $day.' '.explode(' ', $timeEnd)[0];

  echo $start_date_time;

  $query = "INSERT INTO events (user_id, title, description, location, start_date_time, end_date_time)
  VALUES (:user_id, :title, :description, :location, :start_DT, :end_DT)";

  $stmt = $dbConn->prepare($query);
  $stmt->bindValue(':user_id', $user_id);
  $stmt->bindValue(':title', $title);
  $stmt->bindValue(':location', $location);
  $stmt->bindValue(':start_DT', $start_date_time);
  $stmt->bindValue(':end_DT', $end_date_time);
  $stmt->bindValue(':description', $description);
  $stmt->execute();
}

updateEvent($dbConn);

?>
