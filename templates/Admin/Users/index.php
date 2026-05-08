<nav>
  <?= $this->Html->link(
    '<i class="bi bi-plus-circle-fill me-2"></i>Ajouter un utilisateur',
    ['action' => 'edit'],
    [ 'escape' => false,
      'class' => 'btn text-primary my-4' ]);
  ?>
</nav>

<table class="table table-hover">
  <thead>
    <tr>
      <th>Nom</th>
      <th>Login</th>
      <th class="text-center">Administrateur</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($users as $user): ?>
    <tr>
      <td><?= $user->nom ?></td>
      <td><?= $user->login ?></td>
      <td class="text-center">
        <?php
        if ($user->admin)
          echo '<i class="bi bi-person-check-fill text-success"></i>';
        ?>
      </td>
      <td>
        <?= $this->Html->link(
          '<i class="bi bi-pencil"></i>',
          [ 'action' => 'edit',
            $user->id ],
          [ 'escape' => false,
            'class' => 'link-primary' ]) ?>

        &nbsp;

        <?= $this->Form->postLink(
          '<i class="bi bi-trash"></i>',
          [ 'action' => 'delete',
            $user->id],
          [ 'method' => 'delete',
            'confirm' => "Êtes-vous sûr de vouloir supprimer $user->nom ?",
            'escape' => false,
            'class' => 'link-primary' ]) ?>
      </td>
    </tr>
  <?php endforeach ?>
  </tbody>
</table>
