// Add AuthJoomla
// Joomla! MySQL Host Name.
$wgAuthJoomla_MySQL_Host     = '';
// Joomla! MySQL Username.      
$wgAuthJoomla_MySQL_Username = '';
// Joomla! MySQL Password.        
$wgAuthJoomla_MySQL_Password = '';
// Joomla! MySQL Database Name.    
$wgAuthJoomla_MySQL_Database = '';
// Joomla! MySQL Database TablePrefix.           
$wgAuthJoomla_TablePrefix      = "jos_";
// Joomla! Absolute path. 
$wgAuthJoomla_Path             = '';
 
require_once("$IP/extensions/AuthJoomla/AuthJoomla.php");
$wgAuth = new AuthJoomla();

