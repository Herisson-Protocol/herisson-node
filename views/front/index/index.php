
    <div id="page">
        <h1>
            <?php echo sprintf(__("%s bookmarks", HERISSON_TD), $title); ?>
        </h1>

    </div>

<?php
$this->includePartial(__DIR__."/friends.php", array(
    'friendBookmarks' => $friendBookmarks,
    'friends' => $friends
));
require __DIR__."/../footer.php";

