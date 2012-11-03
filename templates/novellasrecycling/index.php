<?php
    defined('_JEXEC') or die('Restricted Access');
?>
<!DOCTYPE html>
<html>
<head>
    <jdoc:include type="head" />
    <link rel="stylesheet" type="text/css" href="templates/<?= $this->template ?>/css/template.css" />
</head>
<body>
<div id="bg_body_container">
    <img id="bg_body" src="templates/<?= $this->template ?>/img/bg_body.jpg" />
</div>
<div id="wrapper">
    <div id="header">
        <a href="#">
            <div id="logo"></div>
        </a>
        <div class="social-container">

        </div>
        <div class="menu-container">
            <jdoc:include type="modules" name="mainmenu" />
        </div>

    </div>
    <div class="clr"></div>
    <div id="content">
        <jdoc:include type="component" />
    </div>
</div>
</body>
</html>