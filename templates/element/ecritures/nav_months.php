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
            $date = mktime(0, 0, 0, $navMonth, 10);
            // Cf. http://userguide.icu-project.org/formatparse/datetime
            $formattedDate = $this->Time->format($date, 'LLLL');
            
            echo $this->Html->link($formattedDate, [
              'controller' => 'ecritures',
              'action' => 'index',
              $navYear,
              $navMonth]);
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
