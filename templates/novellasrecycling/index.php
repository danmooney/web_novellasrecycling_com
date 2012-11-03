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
<div id="bg_body_container" unselectable="on">
    <img id="bg_body" src="templates/<?= $this->template ?>/img/bg_body.jpg" />
</div>
<div id="wrapper">
    <div id="bg_wrapper_fade"></div>
    <div id="header">
        <div id="logo">
            <a href="<?= JURI::base() ?>"></a>
        </div>
        <div class="social-container">
            <div class="social" id="twitter">
                <a href="https://twitter.com/antoncoviello" target="_blank"></a>
            </div>
            <div class="social" id="facebook">
                <a href="#" target="_blank"></a>
            </div>
        </div>
        <div class="clr"></div>
        <div class="menu-container">
            <jdoc:include type="modules" name="mainmenu" />
        </div>
    </div>
    <div class="clr"></div>
    <div id="content">
        <jdoc:include type="component" />
    </div>
    <div id="image-cache">
        <img src="templates/<?= $this->template ?>/img/social/twitter_hover.png" />
        <img src="templates/<?= $this->template ?>/img/social/facebook_hover.png" />
    </div>
</div>
</body>
</html>