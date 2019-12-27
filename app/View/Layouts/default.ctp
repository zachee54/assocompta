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
  <link rel="icon" href="<?php echo $this->Html->url('/img/favicon.ico'); ?>"/>
</head>
<body>
  <header>
    <div class="title">
      Centre de Formation et d'Entraide
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
    </div>
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
        <?php
        if (AuthComponent::user('admin')) {
          ?>
          <li>
            <?php
            echo $this->Html->link('Administration', array(
              'controller' => 'users',
              'action' => 'index'));
            ?>
          </li>
          <?php
        }
        ?>
      </ul>
    </nav>
  </header>
  <?php
  echo $this->Flash->render();
  echo $this->fetch('content');
  echo $this->fetch('scriptBottom');
  ?>
</body>
</html>
