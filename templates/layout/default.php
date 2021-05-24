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
  <link rel="icon" href="<?= $this->Url->image('favicon.ico'); ?>"/>
</head>
<body>
  <header>
    <div>Centre de Formation et d'Entraide</div>
    <?php
    if ($this->Identity->isLoggedIn()) {
      ?>
      <aside>
        <header><?php echo $this->Identity->get('nom'); ?></header>
        <nav>
          <ul>
            <?php
            if (!$this->Identity->get('readonly')) {
              ?>
              <li>
                <?php
                echo $this->Html->link(
                  'Changer mon mot de passe',
                  array(
                    'prefix' => false,
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
                  'prefix' => false,
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
        echo $this->Html->link('Opérations', [
          'prefix' => false,
          'controller' => 'ecritures',
          'action' => 'index']);
        ?>
      </li>
      <li>
        <?php
        echo $this->Html->link('Bilan', [
          'prefix' => false,
          'controller' => 'stats',
          'action' => 'bilan']);
        ?>
      </li>
      <?php
      if ($this->Identity->get('admin')) {
        ?>
        <li>
          <?php
          echo $this->Html->link('Administration', [
            'prefix' => 'Admin',
            'controller' => 'users',
            'action' => 'index']);
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
