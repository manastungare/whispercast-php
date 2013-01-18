<!DOCTYPE html>
<html>
  <head>
    <title><?= $title ?></title>
    <link rel="stylesheet" href="styles.css">
  </head>
  <body>
    <h1><?= $title ?></h1>
    <h2><?= str_replace("/", " &raquo; ", $path) ?></h2>
    <div class="M3U-Link">
      <a href="<?= $m3uUrl ?>">Start Playing</a>
    </div>
    <div class="Library">
