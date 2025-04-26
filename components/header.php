<?php
// /view/includes/header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= $page_title ?? 'EmpowerCraft'; ?></title>
  <link rel="stylesheet" href="../assets/css/profile.css">
  <?php if (isset($additional_css)): ?>
    <?php foreach ($additional_css as $css): ?>
      <link rel="stylesheet" href="<?= $css; ?>">
    <?php endforeach; ?>
  <?php endif; ?>
</head>
<body></body>