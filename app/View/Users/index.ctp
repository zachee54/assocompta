<?php
echo $this->Html->css('users/index', array('inline' => false));

?>
<table id="usersIndex">
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
        echo $this->Html->link(
          'Modifier',
          array(
            'action' => 'edit',
            $userData['id']));
        ?>
      </td>
    </tr>
    <?php
  }
  ?>
  </tbody>
</table>