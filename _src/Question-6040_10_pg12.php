class MonoBookTemplate extends QuickTemplate 
{ 
/** 
* Template filter callback for MonoBook skin. 
* Takes an associative array of data set from a SkinTemplate-based 
* class, and a wrapper for MediaWiki's localization database, and 
* outputs a formatted page. 
* 
* @access private 
*/ 
function execute() { 
// Suppress warnings to prevent notices about missing indexes in // $this->data 
wfSuppressWarnings();
?>
