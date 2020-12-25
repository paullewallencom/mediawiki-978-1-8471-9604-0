<?php
# MoodleGlossary MediaWiki Extention
# Created by Jack Eapen based on Benjamin Kahn's Glossary extyension.
#
# Based on the Emoticon MediaWiki Extension written by Alex Wollangk (alex@wollangk.com)

if ( !defined( 'MEDIAWIKI' ) ) {
        die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}
 
global $wgHooks;
global $wgExtensionCredits;
 
$wgExtensionFunctions[] = "wfTooltip";
 
$wgExtensionCredits['parserhook'][] = array(
        'name' => 'MoodleGlossary',
        'status' => 'beta',
        'type' => 'hook',
        'author' => 'Jack Eapen based on code by Benjamin Kahn (xkahn@zoned.net)',
        'version' => '1.1',
        'update' => '25-09-2007',
        'description' => 'Display description of a Moodle Glossary word on mouseover, if that word appears in a wiki article',
);
 
function wfTooltip() {
    global $wgParser;
    $wgParser->setHook( "tooltip", "tooltip" );
}
 
$wgHooks['ParserBeforeStrip'][] = 'fnTooltips';
 
function tooltip ( $input ) {
  $input = preg_replace ('/XQX/', '', $input);
  $split = explode("|", $input, 2);
  return "<b><font color=\"brown\"><span class=\"glossary\" title=\"tip- " . $split[1] . "\">" . $split[0] . "</span></font></b>";
}
 
// The callback function for replacing acronyms with tooltip tags
function fnTooltips( &$parser, &$text, &$strip_state ) {
  global $action; // Access the global "action" variable
  global $wgMoodleTablePrefix;
  global $wgMoodleDBServer;
  global $wgMoodleDBName;
  global $wgMoodleDBUser;
  global $wgMoodleDBPassword;
 // Only do the replacement if the action is not edit or history
 
        if(
                $action !== 'edit'
                && $action !== 'history'
                && $action !== 'delete'
                && $action !== 'watch'
                && $parser->mTitle->getNamespace() != NS_SPECIAL
                && $parser->mTitle->mNamespace !== 8
        )
        {
 
                $acro = array ();
                $repl = array ();
 
               $conn= mysql_connect($wgMoodleDBServer,$wgMoodleDBUser,$wgMoodleDBPassword) ;
               mysql_select_db($wgMoodleDBName);
                $sql='select concept,definition from '.$wgMoodleTablePrefix.'glossary_entries where approved=1';
                $result=mysql_query($sql);
                if($result) {
                       while ($currEmoticon=mysql_fetch_row($result))
                        {
// start by trimming the search value
                                        $currEmoticon[ 0 ] = trim( $currEmoticon[ 0 ] );
//Jack-to replace the TRUSTTEXT inserted in Moodle
                                       $currEmoticon[ 1 ]= str_replace('#####TRUSTTEXT#####','',$currEmoticon[ 1 ]);
                                        // trim the replacement value
                                         $currEmoticon[ 1 ] = strip_tags( $currEmoticon[ 1 ] );
                                        $currEmoticon[ 1 ] = trim( $currEmoticon[ 1 ] );
 
                                        array_push ($acro, '/(\b)' . $currEmoticon[ 0 ] . '(\b)/');
                                         array_push ($repl, 'XQX' . $currEmoticon[ 0 ] . "XQX|XQX" . $currEmoticon[ 1 ] . 'XQX');
 
// $text=mysql_error();
                        }
 
               $text = preg_replace ($acro, $repl, $text);
             }
             mysql_close($conn);
  return true;      //Jack-this line for MW 1.11 compatibility
}
return false;  //Jack-this line for MW 1.11 compatibility
}
 
?>

