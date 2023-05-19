<?php

/**
* This Router will return a documentation or about page
*
* @author Mario Nemecek
*
*/

class Router {
    private $page;

    public function __construct() {
      if(checkLogin() === true) {
        $url = $_SERVER["REQUEST_URI"]; //get the URL
        $path = parse_url($url)["path"]; //get only the path from the URL
        $path = str_replace(BASEPATH,"",$path); //remove the base path to get file name
        $pathArr = explode("/", $path);
        $view = (empty($pathArr[0])) ? 'week' : $pathArr[0];
        $checkedDate = '';
        if (!empty($pathArr[1])) {
          if (DateTime::createFromFormat('Y-m-d', $pathArr[1]) !== false) {
            $dateArr = explode("-", $pathArr[1]);
            $checkedDate = checkdate($dateArr[1], $dateArr[2], $dateArr[0]) ? $pathArr[1] : date("Y-m-d");
          }
        }
        $this->route($view, $checkedDate);
      }
      else {
        $this->login();
      }
    }


    /**
    * Function to create a new page
    *
    * @param string $path the current path/page name in url
    */
    public function route($viewPath, $datePath) {
      if ($datePath == '') {
        $this->page = new Calendar("Week View", 'week', date("Y-m-d"));
      }
      else {
        switch($viewPath) {
          case 'day':
            $this->page = new Calendar("Day View", $viewPath, $datePath);
            break;
          case 'week':
            $this->page = new Calendar("Week View", $viewPath, $datePath);
            break;
          case 'month':
            $this->page = new Calendar("Month View", $viewPath, $datePath);
            break;
          default:
            $this->page = new Calendar("Week View", 'week', date("Y-m-d"));
            break;
        }
      }
    }

    public function login() {
      $this->page = new LoginPage("Calendar Login");
    }

    /**
    * Function to return page
    *
    * @return page
    */
    public function get_page() {
        return $this->page->get_page();
    }
}
?>
