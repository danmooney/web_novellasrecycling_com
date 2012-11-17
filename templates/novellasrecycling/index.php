<?php
    defined('_JEXEC') or die('Restricted Access');
?>
<!DOCTYPE html>
<html>
<head>
    <script src="templates/<?= $this->template ?>/js/jquery-1.8.2-min.js"></script>
    <script src="templates/<?= $this->template ?>/js/jquery.anythingslider-1.8.6-min.js"></script>
    <jdoc:include type="head" />
    <link rel="stylesheet" type="text/css" href="templates/<?= $this->template ?>/css/template.css" />
    <link rel="shortcut icon" href="templates/<?= $this->template ?>/favicon.ico" />
</head>
<body>
<div id="bg-body-container" unselectable="on">
    <img id="bg-body" src="templates/<?= $this->template ?>/img/bg_body.jpg" />
</div>
<div id="wrapper">
    <div class="fg-leaf leaf-top-left"></div>
    <div class="fg-leaf leaf-bottom-right"></div>
    <div id="bg-wrapper-fade"></div>
    <div id="header">
        <div id="logo">
            <a href="<?= JURI::base() ?>"></a>
        </div>
        <div class="social-container">
            <div class="social" id="twitter">
                <a href="https://twitter.com/antoncoviello" target="_blank"></a>
            </div>
            <div class="social" id="facebook">
                <a href="https://www.facebook.com/pages/Novella-Scrap-Yard/135290503190578" target="_blank"></a>
            </div>
        </div>
        <div class="clr"></div>
        <div class="menu-container">
            <jdoc:include type="modules" name="mainmenu" />
        </div>
    </div>
    <div class="clr"></div>
    <div class="carousel-container">
        <div class="bg-shadow bg-shadow-top"></div>
        <div class="bg-shadow bg-shadow-bottom"></div>
        <ul class="carousel">
            <li>
                <img src="templates/<?= $this->template ?>/img/carousel/1.jpg" />
            </li>
            <li>
                <img src="templates/<?= $this->template ?>/img/carousel/2.jpg" />
            </li>
            <li>
                <img src="templates/<?= $this->template ?>/img/carousel/aluminum_scraps.jpg" />
            </li>
            <li>
                <img src="templates/<?= $this->template ?>/img/carousel/4.jpg" />
            </li>
        </ul>
    </div>
    <div class="navigation-container">
        <div class="navigation-bg-container">
            <div class="navigation-bg navigation-left"></div>
            <div class="navigation-bg navigation-right"></div>
        </div>
        <div class="clr"></div>
        <div class="navigation"></div>
    </div>
    <div id="content">
        <jdoc:include type="component" />
        <div class="clr"></div>
        <h1 id="callout" class="center">Give Us a Call Today!  (203) 743-5275</h1>
    </div>
    <div class="clr"></div>
</div><?php // wrapper ?>
<div id="footer">
    <div id="bg-footer-ripped"></div>
    <div id="bg-footer-white">
        <p id="authors">
            Designed by
            <a target="_blank" href="http://scsdesigninc.com/">ScsDesignInc.com</a>
            and developed by
            <a target="_blank" href="http://danronmoon.com/">DanRonMoon.com</a>
        </p>
    </div>
</div>
<div id="image-cache">
    <img src="templates/<?= $this->template ?>/img/social/twitter_hover.png" />
    <img src="templates/<?= $this->template ?>/img/social/facebook_hover.png" />
</div>
<script src="templates/<?= $this->template ?>/js/carousel.js"></script>
</body>
</html>