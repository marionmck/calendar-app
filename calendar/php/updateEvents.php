<?php

/*
 * @author Mario Nemecek
 *
*/

include "../config/config.php";

$dbConn = getConnection();

function updateEvent($dbConn) {
  $user_id = filter_has_var(INPUT_POST, 'user-id') ? $_POST['user-id'] : null;
  $event_id = filter_has_var(INPUT_POST, 'event-id') ? $_POST['event-id'] : null;

  $title = filter_has_var(INPUT_POST, 'event-title') ? $_POST['event-title'] : null;
  $location = filter_has_var(INPUT_POST, 'event-location') ? $_POST['event-location'] : null;
  $day = filter_has_var(INPUT_POST, 'event-date') ? $_POST['event-date'] : null;
  $timeStart = filter_has_var(INPUT_POST, 'time-start') ? $_POST['time-start'] : null;
  $timeEnd = filter_has_var(INPUT_POST, 'time-end') ? $_POST['time-end'] : null;
  $description = filter_has_var(INPUT_POST, 'description') ? $_POST['description'] : null;

  $start_date_time = $day.' '.explode(' ', $timeStart)[0];
  $end_date_time = $day.' '.explode(' ', $timeEnd)[0];

  function quick_validate($value){
      $value = trim($value);
      $value = stripslashes($value);
      $value = htmlspecialchars($value);
      return $value;
  }

  $title = quick_validate($title);
  $location = quick_validate($location);
  $description = quick_validate($description);

  $query = "UPDATE events SET
  user_id = :user_id,
  title = :title,
  description = :description,
  location = :location,
  start_date_time = :start_DT,
  end_date_time = :end_DT
  WHERE event_id = :event_id";

  $stmt = $dbConn->prepare($query);
  $stmt->bindValue(':user_id', $user_id);
  $stmt->bindValue(':title', $title);
  $stmt->bindValue(':location', $location);
  $stmt->bindValue(':start_DT', $start_date_time);
  $stmt->bindValue(':end_DT', $end_date_time);
  $stmt->bindValue(':description', $description);
  $stmt->bindValue(':event_id', $event_id);
  $stmt->execute();
}

updateEvent($dbConn);


?>
