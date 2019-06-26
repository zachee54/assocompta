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
  <header>Centre de Formation et d'Entraide</header>
  <nav>
    <ul>
      <li>
        <?php
        echo $this->Html->link('Opérations', array(
          'controller' => 'ecritures',
          'action' => 'index'));
        ?>
      </li>
      <li>
        <?php
        echo $this->Html->link('Frères', array(
          'controller' => 'freres',
          'action' => 'index'));
        ?>
      </li>
    </ul>
  </nav>
  <?php
  echo $this->Flash->render();
  echo $this->fetch('content');
  echo $this->fetch('scriptBottom');
  ?>
</body>
</html>
