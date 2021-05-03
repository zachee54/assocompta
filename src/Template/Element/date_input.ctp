<div class="input date">
  <?php
  if (!isset($options)) {
    $options = [];
  }
  
  echo $this->Form->label($field, $label);
  echo $this->Form->text($field, array_merge(
    ['type' => 'date'],
    $options));
  echo $this->Form->error($field);
  ?>
</div>
