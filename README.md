## GitHub Post-Receive Deployment Hook

Deploying applications to development, staging and production never been so easy with GitHub Post-Receive Deployment Hook script!

### Installation

Clone the script:

<pre><code>$ <strong>git clone https://github.com/kwangchin/GitHubHook.git</strong>
</code></pre>

Go to your `GitHub repo` &gt; `Admin` &gt; `Service Hooks`, select `Post-Receive URLS` and enter your hook URL like this:

![GitHub Post-Receive URLs](http://s23.postimage.org/r8eui3jdn/Git_Hub_Hook_01.png)

### How It Works

GitHub provides [Post-Receive Hooks](http://help.github.com/post-receive-hooks/) to allow HTTP callback with a HTTP Post. We then create a script for the callback to deploy the systems automatically.

You will need to create branches like `stage` and `prod` in Git before proceeding into the configurations.

You then can have a brief look into `hook.php`, a WebHook example provided for you to experience how simple the configurations are.

<pre><code>&lt;?php
require_once('class.GitHubHook.php');

// Initiate the GitHub Deployment Hook
$hook = new GitHubHook;

// Enable the debug log, kindly make `log/hook.log` writable
$hook-&gt;enableDebug();

// Adding `stage` branch to deploy for `staging` to path `/var/www/testhook/stage`
$hook-&gt;addBranch('stage', 'staging', '/var/www/stage');

// Adding `prod` branch to deploy for `production` to path `/var/www/testhook/prod`
$hook-&gt;addBranch('prod', 'production', '/var/www/prod', array('user@gmail.com'));

// Deploy the commits
$hook-&gt;deploy();
</code></pre>

In this example, we enabled the debug log for messages with timestamp. You can disable this by commenting or removing the line `$hook->enableDebug()`

We have a staging site and a production site in this example. You can add more branches easily with `$hook->addBranch()` method if you have more systems to deploy.

We then use `$hook->deploy()` to deploy the systems.

## 

### Security

Worry about securities? We have enabled IP check to allow only [GitHub hook addresses](https://help.github.com/articles/what-ip-addresses-does-github-use-that-i-should-whitelist) (CIDR notation):  `207.97.227.253/32`, `50.57.128.197/32`, `108.171.174.178/32`, `50.57.231.61/32`, `204.232.175.64/27`, `192.30.252.0/22` to deploy the systems. We also return a `404 Not Found` page when there is illegal access to the hook script.

For better security, try hiding this hook script in deep directories like `http://www.example.com/let/us/play/hide/and/seek/` and/or renaming the `hook.php` file into `a40b6cf7a5.php`.

### For Developers

We are trying to make developers life easier. Kindly fork this on GitHub and submit your pull requests to help us.
