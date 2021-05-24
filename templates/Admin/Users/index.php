<?php
echo $this->Html->css(
  array('users/index', 'button'),
  array('block' => true));

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
      ?>
      <tr>
        <td><?= $user->nom ?></td>
        <td><?= $user->login ?></td>
        <td><?php if ($user->admin) echo 'X'; ?></td>
        <td>
          <?php
          echo $this->Html->link('Modifier', [
            'action' => 'edit',
            $user->id ]);
          
          echo '&nbsp;';
          
          echo $this->Form->postLink('Supprimer',
            [ 'action' => 'delete',
              $user->id],
            [ 'method' => 'delete',
              'confirm' => "Êtes-vous sûr de vouloir supprimer $user->nom ?"]);
          ?>
        </td>
      </tr>
      <?php
    }
    ?>
    </tbody>
  </table>
</section>
