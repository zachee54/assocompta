<!DOCTYPE html>
<html>
<head>
  <?php echo $this->Html->charset(); ?>
  <meta name="robots" content="noindex, nofollow">
  <title>CFE</title>
  <?php
    echo $this->Html->css('cfe');
    echo $this->fetch('css');
    echo $this->fetch('jquery');
  ?>
  <link rel="icon" href="<?php echo $this->Html->url('/img/favicon.ico'); ?>"/>
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
            <?php
            if (!AuthComponent::user('readonly')) {
              ?>
              <li>
                <?php
                echo $this->Html->link(
                  'Changer mon mot de passe',
                  array(
                    'admin' => false,
                    'controller' => 'users',
                    'action' => 'moncompte'));
                ?>
              </li>
              <?php
            }
            
            ?>
            <li>
              <?php
              echo $this->Html->link(
                'Déconnexion',
                array(
                  'admin' => false,
                  'controller' => 'users',
                  'action' => 'logout'));
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
          'admin' => false,
          'controller' => 'ecritures',
          'action' => 'index'));
        ?>
      </li>
      <li>
        <?php
        echo $this->Html->link('Bilan', array(
          'admin' => false,
          'controller' => 'stats',
          'action' => 'bilan'));
        ?>
      </li>
      <?php
      if (AuthComponent::user('admin')) {
        ?>
        <li>
          <?php
          echo $this->Html->link('Administration', array(
            'admin' => true,
            'controller' => 'users',
            'action' => 'index'));
          ?>
        </li>
        <?php
      }
      ?>
    </ul>
  </nav>
  <?php
  
  echo $this->Flash->render();
  echo $this->fetch('content');
  echo $this->fetch('scriptBottom');
  
  ?>
  <footer>
    <?php
    echo $this->Html->link('Mentions légales', array(
      'controller' => 'pages',
      'action' => 'legal'));
    ?>
  </footer>
</body>
</html>
