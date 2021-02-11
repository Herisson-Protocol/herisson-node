<?php

namespace Herisson\Format;

use Herisson\Repository\FriendRepository;
use Herisson\Format;


class Friend extends Format
{

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->name    = "Friend transfer";
        $this->type    = "friend";
        $this->keyword = "friend";
    }


    /**
     * Print the form to select friend and tags
     *
     * @return void
     */
    public function getForm()
    {

        $friends = FriendRepository::getActives();
        ?>
        <select name="friendId">
            <option value=""><?php echo __('Choose one of your active friend', HERISSON_TD); ?></option>
        <?php
        foreach ($friends as $friend) { ?>
            <option value="<?php echo $friend->id; ?>"><?php echo $friend->name ?> (<?php echo $friend->alias ?>)</option>
        <?php
        } ?>
        </select>
        <br/><br/>
        <!--
        <label>
            <?php echo __('Keyword (optional)', HERISSON_TD); ?>:<br/>
            <input type="text" name="keyword" placeholder="add a keyword to be more specific" style="width: 300px" />
        </label>
        <br/>
        -->

        <?php
    }

    /**
     * Handle the importation of bookmarks from a friend
     *
     * @return a list of Bookmark
     */
    public function import()
    {
        $friendId = post('friendId');
        if (! $friendId) {
            throw new Exception("Missing friend Id");
        }
        $friend = FriendRepository::get(post('friendId'));
        if (! $friend->id) {
            throw new Exception("Unknown friend");
        }
        $bookmarks = $friend->retrieveBookmarks();

        return $bookmarks;


    }



}


