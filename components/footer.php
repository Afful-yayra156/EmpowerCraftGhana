<?php
// /view/includes/footer.php
?>
  <?php if (isset($additional_scripts)): ?>
    <?php foreach ($additional_scripts as $script): ?>
      <script src="<?= $script; ?>"></script>
    <?php endforeach; ?>
  <?php endif; ?>
</body>
</html>