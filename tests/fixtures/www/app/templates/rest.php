You have sent a <?php print $method; ?> request.

<?php print sizeof($server); ?> header(s) received.
<?php foreach ($server as $key => $value): ?>
    <br/><?php print $key ?> : <?php print $value; ?>
<?php endforeach; ?>

<?php if (sizeof($request) == 0): ?>
    <br/>No parameter received.
<?php else: ?>
    <br/><?php print sizeof($request); ?> parameter(s) received.
    <?php foreach ($request as $key => $value): ?>
        <br/><?php print $key ?> : <?php print $value; ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (sizeof($files) == 0): ?>
    <br/>No files received.
<?php else: ?>
    <br/><?php print sizeof($files); ?> file(s) received.
    <?php foreach ($files as $key => $value): ?>
        <br/><?php print $key ?> - name : <?php print $value['name']; ?>
        <br/><?php print $key ?> - error : <?php print $value['error']; ?>
        <br/><?php print $key ?> - size : <?php print $value['size']; ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php if ($body == null): ?>
    <br/>No body received.
<?php else: ?>
    <br/>Body : <?php print $body; ?>
<?php endif; ?>
