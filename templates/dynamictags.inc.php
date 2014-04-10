<?php
/***************************************************************************
Copyright (c) 2005 - 2010 Marcus Campbell
http://scuttle.org/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
***************************************************************************/

$tagservice  =& ServiceFactory::getServiceInstance('TagService');
$userservice =& ServiceFactory::getServiceInstance('UserService');

$logged_on_userid = $userservice->getCurrentUserId();

$userPopularTags        =& $tagservice->getPopularTags($logged_on_userid, 25, $logged_on_userid);
$userPopularTagsCloud   =& $tagservice->tagCloud($userPopularTags, 5, 90, 175); 
$userPopularTagsCount   = count($userPopularTags);

if ($userPopularTagsCount > 0) {
?>

<script type="text/javascript">

$(function(){
    /**
     * Sets up the Tag-It! plugin for autocompletion and tag styling
     */
    $("#tags").tagit({
        autocomplete: {'source': "<?php echo $GLOBALS['root']; ?>api/tags/search"},
        afterTagAdded: function(event, ui) {
            // select the tag in the popular tags list
            // remove the 'x' at the end of the tag
            tagLabel = ui.tag.text();
            tagLabel = tagLabel.substring(0, tagLabel.length - 1);
            $("#popularTags span:contains("+tagLabel+")").removeClass('unselected').addClass("selected");
        },
        afterTagRemoved: function(event, ui) {
            // deselect the tag in the popular tags list
            // remove the 'x' at the end of the tag
            tagLabel = ui.tag.text();
            tagLabel = tagLabel.substring(0, tagLabel.length - 1);
            $("#popularTags span:contains("+tagLabel+")").removeClass('selected').addClass("unselected");
        }
    });

    /**
     * If this is an existing bookmark, highlight any popular tags that are already used
     */
    var tags = $("#tags").tagit("assignedTags");
    $("#popularTags span").each(function() {
        pTag = $(this).text();
        if ($.inArray(pTag, tags) > -1) {
            $(this).addClass('selected').removeClass("unselected");
        }
    });
});

function addTag(ele) {
    var e = $(ele);
    var thisTag = e.text();
    
    // If tag is already listed, remove it
    if ($.inArray(thisTag, $("#tags").tagit("assignedTags")) > -1) {
        $("#tags").tagit("removeTagByLabel", thisTag);
        e.removeClass('selected').addClass("unselected")
    } else {
        $("#tags").tagit("createTag", thisTag);
        e.addClass('selected').removeClass("unselected")
    }
    
    $("#tags").focus();
}

document.write('<div class="collapsible">');
document.write('<h3><?php echo T_('Popular Tags'); ?><\/h3>');
document.write('<p id="popularTags" class="tags">');

<?php
$taglist = '';
foreach(array_keys($userPopularTagsCloud) as $key) {
    $row =& $userPopularTagsCloud[$key];
    $entries = T_ngettext('bookmark', 'bookmarks', $row['bCount']);
    $taglist .= '<span title="'. $row['bCount'] .' '. $entries .'" style="font-size:'. $row['size'] .'" onclick="addTag(this)">'. filter($row['tag']) .'<\/span> ';
}
?>

document.write('<?php echo $taglist ?>');
document.write('<\/p>');
document.write('<\/div>');
</script>

<?php } ?>