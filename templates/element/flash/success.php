<?php
/**
 * @var \App\View\AppView $this
 * @var array $params
 * @var string $message
 */
echo $this->element('flash/default', [
  'message' => $message,
  'params' => $params,
  'iconClass' => 'bi bi-check-circle-fill',
  'bgClass' => 'text-bg-success'] );
