<?php
/**
 * Startup PHP script
 * Starts Awesome IRC Bot in the background
 * If you would like verbose output, run bot.php
 * instead
 */

if (PHP_SAPI === 'CLI')
{
	$command = (stristr(PHP_OS, 'win')) ? 'cls' : 'clear';
	passthru($command);
}
error_reporting(0);

echo "Welcome to Awesome IRC Bot v2\n";
echo "Created by AwesomezGuy, follow @AwesomezGuy on Twitter\n\n";
echo "Backgrounding process...\n";
echo "Please note that this startup script will NOT work on Windows, use 'php bot.php' instead, and background it in some way";

// Fork
$pid = pcntl_fork();

/**
 * At this point there are now 2 process running, 
 * one with $pid and one without.
 * The one without $pid is the child, and we will now 
 * use it to initialize the main script.
 * The parent process will simply die peacefully
 */
if (!$pid)
	exec("php " . __DIR__ . "/bot.php &");
?>
