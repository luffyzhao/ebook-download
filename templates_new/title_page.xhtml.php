<?php echo '<?xml version="1.0" encoding="UTF-8" ?>' ?>

<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
      <title><?php echo $this->bookProperties['author'] ?> - <?php echo $this->bookProperties['title'] ?></title>
      <link rel="stylesheet" href="Styles/stylesheet.css" type="text/css" />
    </head>

    <body class="title_page">
        <h1><?php echo $this->bookProperties['title'] ?></h1>
        <h2><?php echo $this->bookProperties['author'] ?></h2>
    </body>
</html>
