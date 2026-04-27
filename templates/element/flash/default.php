<?php
/**
 * @var \App\View\AppView $this
 * @var array $params
 * @var string $message
 */
$class = 'message';
if (!empty($params['class'])) {
    $class .= ' ' . $params['class'];
}
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
if (empty($iconClass)) {
  $iconClass = 'bi bi-lightbulb';
}
if (empty($bgClass)) {
  $bgClass = 'bg-info';
}
?>
<div class="toast align-items-center <?= $bgClass ?> border-0" data-bs-delay="10000" role="alert" aria-live="assertive" aria-atomic="true">
  <div class="d-flex align-items-start">
    <div class="toast-body flex-grow-1 d-flex">
      <i class="<?= $iconClass ?> fs-5 me-2"></i>
      <?= $message ?>
    </div>
    <button type="button" class="btn-close btn-close-white mt-2 me-2" data-bs-dismiss="toast" aria-label="Close"></button>
  </div>
</div>
