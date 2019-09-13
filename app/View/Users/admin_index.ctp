<?php
echo $this->Html->css(
  array('users/index', 'button'),
  array('inline' => false));

?>
<section id="usersIndex">
  <nav>
    <?php
    echo $this->Html->link('Ajouter un utilisateur',
      array('action' => 'edit'),
      array('class' => 'button addButton'));
    ?>
  </nav>

  <table>
    <thead>
      <tr>
        <th>Nom</th>
        <th>Login</th>
        <th>Administrateur</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
    <?php
    foreach ($users as $user) {
      $userData = $user['User'];
      ?>
      <tr>
        <td><?php echo $userData['nom']; ?></td>
        <td><?php echo $userData['login']; ?></td>
        <td><?php if ($userData['admin']) echo 'X'; ?></td>
        <td>
          <?php
          echo $this->Html->link('Modifier', array(
            'action' => 'edit',
            $userData['id']));
          
          echo '&nbsp;';
          
          echo $this->Html->link('Supprimer', array(
            'action' => 'delete',
            $userData['id']));
          ?>
        </td>
      </tr>
      <?php
    }
    ?>
    </tbody>
  </table>
</section>