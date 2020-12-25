# Disable reading line, for anonymous (not-logged-in => * ) :
$wgGroupPermissions['*']['read'] = false;

# Enable anonymous to read the followings pages :
$wgWhitelistRead = array( "Main Page", "Special:Userlogin", "-", "MediaWiki:Monobook.css" );
