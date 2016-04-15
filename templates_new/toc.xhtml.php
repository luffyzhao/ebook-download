<?php echo '<?xml version="1.0" encoding="UTF-8" ?>' ?>

<html xmlns="http://www.w3.org/1999/xhtml" xmlns:epub="http://www.idpf.org/2007/ops">
<head>
<title>toc.xhtml</title>
<link href="Styles/stylesheet.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <nav id="toc" epub:type="toc">
        <h1 class="frontmatter"><?php echo $this->bookProperties['title'] ?></h1>
        <ol class="contents">
            <?php foreach ($this->chapterList as $k => $chapter): ?>
                <li><a href="chap<?php echo $k ?>.xhtml"><?php echo $chapter ?></a></li>
            <?php endforeach;?>
        </ol>
    </nav>
</body>

</html>