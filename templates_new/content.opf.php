<?php echo '<?xml version="1.0" encoding="UTF-8" ?>' ?>

<package xmlns="http://www.idpf.org/2007/opf" xmlns:dc="http://purl.org/dc/elements/1.1/" unique-identifier="Id" version="3.0">

    <metadata xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:opf="http://www.idpf.org/2007/opf">
        <dc:title><?php echo $this->bookProperties['title'] ?></dc:title>
        <dc:creator><?php echo $this->bookProperties['author'] ?></dc:creator>
        <dc:language><?php echo $this->bookProperties['language'] ?></dc:language>
        <dc:identifier id="Id"><?php echo $this->bookProperties['id'] ?></dc:identifier>
        <meta property="dcterms:modified"><?php echo $this->bookProperties['timestamp'] ?></meta>
<?php if ($this->bookProperties['image'] != ''): ?>
        <meta name="cover" content="cover-image" />
<?php endif;?>
    </metadata>

    <manifest>
        <item id="toc" properties="nav" href="toc.xhtml" media-type="application/xhtml+xml" />
        <item id="ncx" href="toc.ncx" media-type="application/x-dtbncx+xml" />
        <item id="style" href="Styles/stylesheet.css" media-type="text/css" />
        <item id="titlepage" href="title_page.xhtml" media-type="application/xhtml+xml" />
<?php if ($this->bookProperties['image'] != ''): ?>
        <item id="cover-image" href="images/cover.jpg" media-type="image/jpeg"/>
<?php endif;?>
<?php foreach ($this->chapterList as $k => $chapter): ?>
        <item id="chapter<?php echo $k ?>" href="chap<?php echo $k ?>.xhtml" media-type="application/xhtml+xml" />
<?php endforeach;?>
    </manifest>

    <spine toc="ncx">
        <itemref idref="titlepage" />
<?php foreach ($this->chapterList as $k => $chapter): ?>
        <itemref idref="chapter<?php echo $k ?>" />
<?php endforeach;?>
    </spine>
</package>
