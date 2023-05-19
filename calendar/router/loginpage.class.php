<?php

/**
*   Creates an HTML webpage using the given parameters
*   @author Mario Nemecek
*/

class LoginPage {

    private $pageStart;
    private $pageTitle;
    private $css;
    private $main;
    private $pageEnd;

    public function __construct($pageTitle) {
        // @todo - initialise properties and call methods as required
        $this->set_css();
        $this->set_js();
        $this->set_pageStart($pageTitle, $this->css);
        $this->set_main();
        $this->set_pageEnd();
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

    /**
     * Sets $main to the main section of the page
     */
    private function set_main() {
        $this->main = $this->mainHTML();
    }

    /**
     * Creates the main section of the page using heredoc
     *
     * @param string $heading the page heading
     * @param string $text a paragraph about the page
     *
     * @return the HTML for the main section
     */
    protected function mainHTML() {
      $error = '';
      if(isset($_SESSION['login-error'])){
        $error = $_SESSION['login-error'];
      }
      $basepath = rtrim(BASEPATH, "/");
      return <<<MAIN
<div id="main">
  <h1 id="login-heading">Calendar Login</h1>
  <form class="login-form" action="$basepath/php/loginProcess.php" method="post">
    <div class="login-container">
      <label for="email"><b>Email</b></label>
      <input type="text" placeholder="Enter Email" name="email" required>

      <label for="psw"><b>Password</b></label>
      <input type="password" placeholder="Enter Password" name="psw" required>

      <button type="submit">Login</button>
      <span id="form-error">$error</span>
    </div>
  </form>
</div>
MAIN;
    }



    /**
     * Sets the closing HTML tags for the end of the page
     */
    private function set_pageEnd() {
        $this->pageEnd = <<<PAGEEND
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
            $this->main .
            $this->pageEnd;
    }
}

?>
