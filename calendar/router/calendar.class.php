<?php

if(isset($_SESSION['login-error'])){
  unset($_SESSION['login-error']);
}

/**
*   Creates an HTML webpage using the given parameters
*
*   @author Mario Nemecek
*/

class Calendar {

    private $pageStart;
    private $pageTitle;
    private $css;
    private $js;
    private $pageNav;
    private $main;
    private $pageEnd;

    public function __construct($pageTitle, $view, $date) {
        // @todo - initialise properties and call methods as required
        $this->set_css();
        $this->set_js();
        $this->set_pageStart($pageTitle, $this->css);
        $this->set_nav();
        $this->set_main($view, $date);
        $this->set_pageEnd($this->js);
    }

    /**
     * Sets the starting HTML tags and metadata for the start of the page
     *
     * @param string $pageTitle the title for the page
     * @param string $css link to the css file
     */
    private function set_pageStart($pageTitle, $css) {
        $this->pageStart = <<<PAGESTART
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="Mario Nemecek">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$pageTitle</title>
    <link href="$css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
PAGESTART;
    }

    /**
     * Sets the path for the css file
     */
    private function set_css() {
        $this->css = BASEPATH.CSSPATH;
    }

    /**
     * Sets the path for the js file
     */
    private function set_js() {
        $this->js = BASEPATH.JSPATH;
    }

    protected function set_nav() {
      $user_id = $_SESSION["user_id"];
      $this->pageNav = <<<NAV
<div id="calendar-side-panel" class="side-panel">
  <input type="hidden" id="user_id" value="$user_id"/>
  <a href="javascript:void(0)" class="closebtn" onclick="closePanel()">&times;</a>

  <div class="calendar-controls">
    <span id="date-heading">Friday, 4th May 2021</span>
    <div id="control-btn-container">
      <div id="calendar-view-menu">
        <button id="day-view-btn" class="view-btn" type="button" name="day">Day</button>
        <button id="week-view-btn" class="view-btn" type="button" name="week">Week</button>
        <button id="month-view-btn" class="view-btn" type="button" name="month">Month</button>
      </div>

      <div id="date-menu">
        <div class="prev-next">
          <button id="prev-btn" class="change-date-btn" type="button" name="previous">Previous</button>
          <button id="next-btn" class="change-date-btn" type="button" name="next">Next</button>
        </div>
        <div class="current-date">
          <button id="today-btn" class="side-panel-btn" type="button" name="today">Today</button>
        </div>
      </div>
    </div>
  </div>

  <div class="form-container">
    <button type="button" id="create-event-btn">Create New Event</button>
    <form id="event-form" autocomplete="off">
      <label for="title">Title</label>
      <input type="text" id="title-input" name="event-title" placeholder="Add Title">

      <label for="Location">Location</label>
      <input type="text" id="location-input" name="event-location" placeholder="Add Location">

      <label for="event-date">Day of Event</label>
      <input type="date" id="date-input" name="event-date" placeholder="dd-mm-yyyy">

      <div class="time-select-container">
        <label for="time-start">Event Start Time</label>
        <select id="time-start" class="select-time" name="time-start">
        </select>
      </div>

      <div class="time-select-container">
        <label for="time-end">Event End Time</label>
        <select id="time-end" class="select-time" name="time-end">
        </select>
      </div>

      <label for="description">Description</label>
      <textarea id="event-description" name="description" placeholder="Add Description" rows="5" cols="75" maxlength="250"></textarea>

      <div id="form-button-container">
        <input id="event-form-submit-btn" type="submit" value="Submit">
        <button id="delete-btn" disabled><i class="fa fa-trash"></i></button>
      </div>
    </form>
  </div>

  <div class="footer">
    <button id="logout-btn" class="side-panel-btn" type="button" name="logout">Logout</button>
  </div>
</div>
NAV;
}

    /**
     * Sets $main to the main section of the page
     */
    private function set_main($view, $date) {
        $this->main = $this->mainHTML($view, $date);
    }

    /**
     * Creates the main section of the page using heredoc
     *
     * @param string $heading the page heading
     * @param string $text a paragraph about the page
     *
     * @return the HTML for the main section
     */
    protected function mainHTML($view, $date) {
        return <<<MAIN
<div id="main">
  <div id="container">
    <button id="panel-btn" onclick="openPanel()"><i class="fa fa-bars"></i></button>
    <div id="calendar" class="calendar-$view" data-date="$date"></div>
  </div>
</div>
MAIN;
    }



    /**
     * Sets the closing HTML tags for the end of the page
     */
    private function set_pageEnd($js) {
        $this->pageEnd = <<<PAGEEND
  <script src="https://cdnjs.cloudflare.com/ajax/libs/luxon/1.26.0/luxon.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="$js"></script>
</body>
</html>
PAGEEND;
    }

    /**
     * Returns the whole webpage
     *
     * @return all the sections needed to make this webpage
     */
    public function get_page() {
        return
            $this->pageStart .
            $this->pageNav .
            $this->main .
            $this->pageEnd;
    }
}

?>
