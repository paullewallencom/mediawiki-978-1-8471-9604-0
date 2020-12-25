<?php
/**
 * WordPress Comments extension
 * For documentation, please see http://www.mediawiki.org/wiki/Extension:WordPress_Comments
 *
 * @ingroup Extensions
 * @author Greg Perry
 * @version 0.9.0
 */
define('WORDPRESSCOMMENTS_VERSION','0.9.0, 2007-12-1');

//Extension credits that show up on Special:Version
$wgExtensionCredits['parserhook'][] = array(
 'name' => 'WordPress Comments',
 'url' => 'http://www.mediawiki.org/wiki/Extension:WordPress_Comments',
 'version' => WORDPRESSCOMMENTS_VERSION,
 'author' => 'Greg Perry',
 'description' => '<tt><wp:comments></tt> parser hook to show WordPress comments on a MediaWiki.'
);

//Avoid unstubbing $wgParser on setHook() too early on modern (1.12+) MW versions, as per r35980
if ( defined( 'MW_SUPPORTS_PARSERFIRSTCALLINIT' ) ) {
 $wgHooks['ParserFirstCallInit'][] = 'wordpresscomments';
} else {
 $wgExtensionFunctions[] = 'wordpresscomments';
}

function wordpresscomments(){
 global $wgParser;
 $wgParser->setHook ( 'wp:comments', 'get_wordpress_comments' );
 return true;
}

function get_wordpress_comments($data) {

 $param = array();
 $info = explode("\n", $data);
 if(is_array($info)){
 foreach($info as $lin){
 $line = explode("=",$lin, 2);
 if(count($line)==2){
 $param[trim($line[0])] = trim($line[1]);
 }
 }
}
 $wp_post_title = (isset($param["wp_post_title"]))? $param["wp_post_title"] : ""; // TODO - default to page name
 $wp_url = (isset($param["wp_url"]))? $param["wp_url"] : "";

 global $wgParser, $wgDBserver, $wgDBname;
 global $wgDBuser, $wgDBpassword, $wgPageName;

 $wgParser->disableCache();

 mysql_connect($wgDBserver,$wgDBuser,$wgDBpassword) or die("Unable toconnect to database" . mysql_error());
 @mysql_select_db("$wgDBname") or die("Unable to select database $wgDBname");
 mysql_query("SET NAMES utf8");
 mysql_query("SET CHARACTER_SET utf8");

 $sql = " SELECT * FROM wp_posts WHERE post_title LIKE '$wp_post_title' AND post_status = 'publish' ";
 $result = mysql_query($sql);
 $number = mysql_num_rows($result);
 $wp_post_ID = 0;
 $ret = "";

 if ($number < 1) {
 #$ret .= "no records found"; -- TODO Configurable
 $ret .= "could not find wordpress article with a title like [".$wp_post_title."%]" ;
 } else if ($number == 1){
 $wp_post_ID = mysql_result($result, 0,"ID");
 $sql = " SELECT * FROM wp_comments INNER JOIN wp_posts ON comment_post_ID = ID WHERE ID = $wp_post_ID AND comment_approved = '1' ORDER BY comment_date ";
 $result = mysql_query($sql);
 $number = mysql_num_rows($result);
 $i = 0;
 $ret = "";
 #$ret .= "[".$sql ."]";
 #$ret .= "[".$wp_post_ID ."]";


 if ($number < 1) {
 #$ret .= "no records found"; -- TODO Configurable
 } else {
 $ret .= "<ul class=\"commentlist\">";
 while ($number > $i) {
 $ret .= "<li id=\"comment-". mysql_result($result,$i,"comment_ID") ."\" ";
 $ret .= (($i % 2) ? " " : " class=\"alt\" ");
 $ret .= ">";
 $ret .= " <cite><a href='". mysql_result($result,$i,"comment_author_url") ."' rel='external nofollow'>". mysql_result($result,$i,"comment_author") ."</a></cite> Says:";
 $ret .= " <br />";
 $ret .= " <small class=\"commentmetadata\"><a href=\"#comment-". mysql_result($result,$i,"comment_ID") ."\" title=\"\">". date("M d Y g:i a", strtotime(mysql_result($result,$i,"comment_date"))) ."</a> </small><br>";
 $ret .= " <p>" . mysql_result($result,$i,"comment_content") . "</p>";
 $ret .= "</li>";
 $i++;
 }
 $ret .= "</ul>";
 }

 $reply_form = true;
 if ($reply_form) {
 $ret .= get_wordpress_reply_form($wp_post_ID, $wp_url);
 }

 } else {
 $ret .= "more than one wordpress post match";
 $ret .= "sql [".$sql."]";
 }

 return $ret;
}

function get_wordpress_reply_form ($wp_post_ID, $wp_url) {

 if ($wp_post_ID > 0) {
 $ret = "";
 $ret .= "<h3 id=\"respond\">Leave a Comment</h3>";
 $ret .= "<form action=\"$wp_url/wp-comments-post.php\" method=\"post\" id=\"commentform\">";
 $ret .= "<p><input type=\"text\" name=\"author\" id=\"author\" value=\"\" size=\"22\" tabindex=\"1\" />";
 $ret .= "<label for=\"author\"><small>Name (required)</small></label></p>";
 $ret .= "<p><input type=\"text\" name=\"email\" id=\"email\" value=\"\" size=\"22\" tabindex=\"2\" />";
 $ret .= "<label for=\"email\"><small>Mail (will not be published) (required)</small></label></p>";
 $ret .= "<p><input type=\"text\" name=\"url\" id=\"url\" value=\"\" size=\"22\" tabindex=\"3\" />";
 $ret .= "<label for=\"url\"><small>Website</small></label></p>";
 $ret .= "<p><textarea name=\"comment\" id=\"comment\" cols=\"100%\" rows=\"10\" tabindex=\"4\"></textarea></p>";
 $ret .= "<p><input name=\"submit\" type=\"submit\" id=\"submit\" tabindex=\"5\" value=\"Submit Comment\" />";
 $ret .= "<input type=\"hidden\" name=\"comment_post_ID\" value=\"$wp_post_ID\" />";
 $ret .= "</p>";
 $ret .= "</form>";
 $ret .= " ";
 }
 return $ret;
}


