<!DOCTYPE html>
<html>
<head>
  <?php echo $this->Html->charset(); ?>
  <meta name="robots" content="noindex, nofollow">
  <title>CFE</title>
  <?php
    echo $this->Html->css('cfe');
    echo $this->fetch('css');
  ?>
</head>
<body>
  <header>
    <div>Centre de Formation et d'Entraide</div>
    <?php
    if (AuthComponent::user()) {
      ?>
      <aside>
        <header><?php echo AuthComponent::user('nom'); ?></header>
        <nav>
          <ul>
            <li>
              <?php
              echo $this->Html->link(
                'Changer mon mot de passe',
                array('controller' => 'users', 'action' => 'moncompte'));
              ?>
            </li>
            <li>
              <?php
              echo $this->Html->link(
                'Déconnexion',
                array('controller' => 'users', 'action' => 'logout'));
              ?>
            </li>
          </ul>
        </nav>
      </aside>
      <?php
    }
    ?>
  </header>
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
