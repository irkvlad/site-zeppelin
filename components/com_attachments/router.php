<?php
defined('_JEXEC') or die('Restricted access');

function AttachmentsBuildRoute(&$query)
{
    // Syntax to upload an attachment:
    //      index.php?option=com_attachments&task=upload&artid=44
    //         --> index.php/attachments/upload/article/44

    // Syntax to delete an attachment:
    //      index.php?option=com_attachments&task=delete&id=4&artid=44&from=article
    //          --> /attachments/delete/4/article/44
    //          --> /attachments/delete/4/article/44/from/article
    // Note: The 'from' clause indicates which view the item was called from (eg, article or frontpage)

    $segments = array();

    if ( isset($query['task']) ) {
        $segments[] = $query['task'];
        unset($query['task']);
        }

    if ( isset($query['id']) ) {
        $segments[] = $query['id'];
        unset($query['id']);
        }

    if ( isset($query['artid']) ) {
        $segments[] = 'article';
        $segments[] = $query['artid'];
        unset($query['artid']);
        }

    if ( isset($query['from']) ) {
        $segments[] = 'from';
        $segments[] = $query['from'];
        unset($query['from']);
        }

    if ( isset($query['tmpl']) ) {
        $segments[] = 'tmpl';
        $segments[] = $query['tmpl'];
        unset($query['tmpl']);
        }

    if ( isset($query['change']) ) {
        $segments[] = 'change';
        $segments[] = $query['change'];
        unset($query['change']);
        }

    return $segments;
}


function AttachmentsParseRoute($segments)
{
    $vars = array();
    $task = $segments[0];
    $vars['task'] = $task;

    // Handle the the main keyword clause
    if ( ($task == 'delete') || ($task == 'delete_warning') ||
         ($task == 'update') || ($task == 'download')  ) {
        $vars['id'] = $segments[1];
        }

    // Handle the other clauses
    for ( $i=0; $i < count($segments); $i++ ) {

        // Look for article IDs
        if ( ($segments[$i] == 'article') && ($segments[$i-1] != 'from') ) {
            if ( $i+1 >= count($segments) ) {
                echo "<br>Error in AttachmentsParseRoute:  Found 'article' without a following article ID!<br>";
                exit;
                }
            $vars['artid'] = $segments[$i+1];
            }

        // Look for 'from' clause
        if ( $segments[$i] == 'from' ) {
            if ( $i+1 >= count($segments) ) {
                echo "<br>Error in AttachmentsParseRoute:  Found 'from' without any info!<br>";
                exit;
                }
            $vars['from'] = $segments[$i+1];
            }

        // Look for 'tmpl' clause
        if ( $segments[$i] == 'tmpl' ) {
            if ( $i+1 >= count($segments) ) {
                echo "<br>Error in AttachmentsParseRoute:  Found 'tmpl' without any template name!<br>";
                exit;
                }
            $vars['tmpl'] = $segments[$i+1];
            }

        // Look for 'change' clause
        if ( $segments[$i] == 'change' ) {
            if ( $i+1 >= count($segments) ) {
                echo "<br>Error in AttachmentsParseRoute:  Found 'change' without item to change!<br>";
                exit;
                }
            $vars['change'] = $segments[$i+1];
            }
        }

    return $vars;
}


?>
