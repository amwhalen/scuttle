<?php
// Searches through a user's tags for a particular term and returns tags that match.

// Force HTTP authentication first!
require_once 'httpauth.inc.php';
require_once '../header.inc.php';

$tagservice  =& ServiceFactory::getServiceInstance('TagService');
$userservice =& ServiceFactory::getServiceInstance('UserService');

// Get passed-in information
if (isset($_REQUEST['term']) && (trim($_REQUEST['term']) != ''))
    $term = trim(urldecode($_REQUEST['term']));
else
    $term = NULL;

// start output
header('Content-Type: application/json');

if (!is_null($term)) {
    // Get the tags relevant to the passed-in variables.
    $tags =& $tagservice->getTagsLike($term, $userservice->getCurrentUserId());
} else {
    $tags = array();
}

// Output JSON
if (count($tags)) {
    
    $tagStrings = array();
    foreach ($tags as $row) {
        $tagStrings[] = convertTag($row['tag'], 'out');
    }
    echo '["' . implode('","', $tagStrings) . '"]';

} else {

    // no term, no tags
    echo "[]";

}