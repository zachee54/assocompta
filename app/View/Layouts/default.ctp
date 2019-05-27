<!DOCTYPE html>
<html>
<head>
  <?php echo $this->Html->charset(); ?>
  <title>CFE</title>
  <?php
    echo $this->Html->css('cfe');
    echo $this->fetch('css');
  ?>
</head>
<body>
  <?php
  echo $this->Flash->render();
  echo $this->fetch('content');
  ?>
</body>
</html>
