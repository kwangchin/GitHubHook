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
   * @var array GitHub's IP addresses for hooks.
   * @since 1.1
   */
  private $_ips = array('54.235.183.49', '54.235.183.23', '54.235.118.251', '54.235.120.57', '54.235.120.61', '54.235.120.62');

  /**
   * Constructor.
   * @since 1.0
   */
  function __construct() {
    /* Support for EC2 load balancers */
    if (
        isset($_SERVER['HTTP_X_FORWARDED_FOR']) &&
        filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)
      ) {
      $this->_remoteIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $this->_remoteIp = $_SERVER['REMOTE_ADDR'];
    }

    if (isset($_POST['payload'])) {
      $this->_payload  = json_decode($_POST['payload']);
    } else {
      $this->_notFound('Payload not available from: ' . $this->_remoteIp);
    }
  }

  /**
   * Centralize our 404.
   * @param string $reason Reason of 404 Not Found.
   * @since 1.1
   */
  private function _notFound($reason = NULL) {
    if ($reason !== NULL) {
      $this->log($reason);
    }

    header('HTTP/1.1 404 Not Found');
    echo '404 Not Found.';
    exit;
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
    if (in_array($this->_remoteIp, $this->_ips)) {
      foreach ($this->_branches as $branch) {
        if ($this->_payload->ref == 'refs/heads/' . $branch['name']) {

          $this->log('Deploying to ' . $branch['title'] . ' server');
          shell_exec('./deploy.sh ' . $branch['path'] . ' ' . $branch['name']);
        }
      }
    } else {
      $this->_notFound('IP address not recognized: ' . $this->_remoteIp);
    }
  }
}
