<?php
error_reporting(0);

/**
 * GitHub Post-Receive Deployment Hook.
 * 
 * @author Chin Lee <kwangchin@gmail.com>
 * @copyright Copyright (C) 2012 Chin Lee
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version 1.0
 */

class GitHubHook
{
  /**
   * @var string Remote IP of the person.
   * @since 1.0
   */
  private $_remoteIp = '';
  
  /**
   * @var object Payload from GitHub.
   * @since 1.0
   */
  private $_payload = '';
  
  /**
   * @var boolean Log debug messages.
   * @since 1.0
   */
  private $_debug = FALSE;
  
  /**
   * @var array Branches.
   * @since 1.0
   */
  private $_branches = array();
  
  
  
  /**
   * Constructor.
   * @since 1.0
   */
  function __construct() {
    $this->_remoteIp = $_SERVER['REMOTE_ADDR'];
    
    if (isset($_POST['payload'])) {
      $this->_payload  = json_decode($_POST['payload']);
    } else {
      header('HTTP/1.1 404 Not Found');
      echo '404 Not Found.';
      exit;
    }
  }
  
  /**
   * Enable log of debug messages.
   * @since 1.0
   */
  public function enableDebug() {
    $this->_debug = TRUE;
  }
  
  /**
   * Add a branch.
   * @param string $name Branch name, defaults to 'master'.
   * @param string $title Branch title, defaults to 'development'.
   * @param string $path Relative path to development directory, defaults to '/var/www/'.
   * @param array $author Contains authorized users' email addresses, defaults to everyone.
   * @since 1.0
   */
  public function addBranch($name = 'master', $title = 'development', $path = '/var/www/', $author = array()) {
    $this->_branches[] = array(
      'name'   => $name,
      'title'  => $title,
      'path'   => $path,
      'author' => $author
    );
  }
  
  /**
   * Log a message.
   * @param string $message Message to log.
   * @since 1.0
   */
  public function log($message) {
    if ($this->_debug) {
      file_put_contents('log/hook.log', '[' . date('Y-m-d H:i:s') . '] - ' . $message . PHP_EOL, FILE_APPEND);
    }
  }
  
  /**
   * Deploys.
   * @since 1.0
   */
  public function deploy() {
    if ($this->_remoteIp == '207.97.227.253' || $this->_remoteIp == '50.57.128.197') {
      foreach ($this->_branches as $branch) {
        if ($this->_payload->ref == 'refs/heads/' . $branch['name']) {
          $this->log('Deploying to ' . $branch['title'] . ' server');
          shell_exec('./deploy.sh ' . $branch['path'] . ' ' . $branch['name']);
        }
      }
    }
  }
}