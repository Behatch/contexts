<?php

error_reporting(E_ALL);

header('Date: ' . date('c'));
header('Expires: ' . date('c', time() + 60));
header('Content-Type: text/html; charset=utf-8');

$body = file_get_contents('php://input');
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && !empty($body)) {
    // For PUT with body we simulate an empty response with 204
    header("HTTP/1.0 204 No Content");
    die();
}

?>

You have sent a <?php print $_SERVER['REQUEST_METHOD']; ?> request.

<?php print sizeof($_SERVER); ?> header(s) received.
<?php foreach($_SERVER as $key => $value): ?>
  <br /><?php print $key ?> : <?php print $value; ?>
<?php endforeach; ?>

<?php if(sizeof($_REQUEST) == 0): ?>
  <br />No parameter received.
<?php else: ?>
  <br /><?php print sizeof($_REQUEST); ?> parameter(s) received.
  <?php foreach($_REQUEST as $key => $value): ?>
    <br /><?php print $key ?> : <?php print $value; ?>
  <?php endforeach; ?>
<?php endif; ?>

<?php if(sizeof($_FILES) == 0): ?>
  <br />No files received.
<?php else: ?>
  <br /><?php print sizeof($_FILES); ?> file(s) received.
  <?php foreach($_FILES as $key => $value): ?>
    <br /><?php print $key ?> - name : <?php print $value['name']; ?>
    <br /><?php print $key ?> - error : <?php print $value['error']; ?>
    <br /><?php print $key ?> - size : <?php print $value['size']; ?>
  <?php endforeach; ?>
<?php endif; ?>

<?php if($body == null): ?>
  <br />No body received.
<?php else: ?>
  <br />Body : <?php print $body; ?>
<?php endif; ?>
