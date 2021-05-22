<?php
$this->Html->css('ecritures/nav_months', array('block' => true));
?>
<nav class="navMonths">
  Aller au mois de&nbsp;:
  <ul>
  <?php
  foreach ($months as $navYear => $navMonths) {
    ?>
    <li><?php echo $navYear; ?>
      <ul>
        <?php
        foreach ($navMonths as $navMonth) {
          ?>
          <li>
            <?php
            echo $this->Html->link(
              strftime('%B', mktime(0, 0, 0, $navMonth, 10)),
                array(
                  'controller' => 'ecritures',
                  'action' => 'index',
                  $navYear,
                  $navMonth));
            ?>
          </li>
          <?php
        }
        ?>
      </ul>
    </li>
    <?php
  }
  ?>
  </ul>
</nav>
