<?php
$messages = get_flash();
foreach ($messages as $message):
    $type = $message['type'] ?? 'info';
    $text = $message['message'] ?? '';
?>
    <div class="alert alert-<?php echo e($type); ?> alert-dismissible fade show" role="alert">
        <?php echo e($text); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endforeach; ?>
