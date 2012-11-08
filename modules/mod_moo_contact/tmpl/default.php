<div id="contact-form-container">
    <form id="contact-form" action="" method="post" class="form-validate">
        <?= $template::invalidationMessage() ?>
        <div class="float-l">
            <div class="formline">
                <?php // <label for="first_name">First Name:</label> ?>
                <?= $template::input('first_name', null, 'tabindex="1" data-placeholder="First Name"') ?>
            </div>
            <div class="formline">
                <?php // <label for="last_name">Last Name:</label> ?>
                <?= $template::input('last_name', null, 'tabindex="3" data-placeholder="Last Name"') ?>
            </div>
        </div>
        <div class="float-r">
            <div class="formline">
                <?php // <label for="phone">Phone:</label> ?>
                <?= $template::input('phone', null, 'tabindex="2" data-placeholder="Telephone Number"') ?>
            </div>
            <div class="formline">
                <?php // <label for="email">E-Mail:</label> ?>
                <?= $template::input('email', null, 'tabindex="4" data-placeholder="Email Address"') ?>
            </div>

        </div>
        <div class="clr"></div>
        <div class="formline">
            <?php // <label for="notes">Comments:</label> ?>
            <?= $template::textarea('comments', null, 'tabindex="5" data-placeholder="Comments"') ?>
        </div>
        <button id="submit-button" tabindex="6" class="button float-r" type="submit">Submit</button>
    </form>
</div>
<?php
    $session->clear('fields');
    $session->clear('invalid_fields');
