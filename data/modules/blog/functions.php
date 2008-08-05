<?php
//Some functions needed for the album-module are defined here

//Predefined variables
$var = $_GET['var'];
$var2 = $_GET['var2'];
$var3 = $_GET['var3'];

//Function: readout blog categories
//------------
function read_blog_catg($dir) {
   $path = opendir($dir);
   while (false !== ($file = readdir($path))) {
       if(($file !== ".") and ($file !== "..")) {
           if(is_file($dir."/".$file))
               $files[]=$file;
           else
               $dirs[]=$file;           
       }
   }
   if (!$dirs) {
	//Include Translation data
	include ("data/settings/langpref.php");
	include ("data/inc/lang/en.php");
	include ("data/inc/lang/$langpref");
	echo "<span class=\"kop4\">$lang_albums14</span>"; }
	
   if($dirs) {
   natcasesort($dirs);
   foreach ($dirs as $dir) {
		//Include Translation data
		include ("data/settings/langpref.php");
		include ("data/inc/lang/en.php");
		include ("data/inc/lang/$langpref");
		echo "<div class=\"menudiv\" style=\"margin: 20px;\">
			<table>
				<tr>
					<td>
						<img src=\"data/modules/blog/images/blog.png\" border=\"0\" alt=\"\">
					</td>
					<td style=\"width: 350px;\">
						<span style=\"font-size: 17pt;\">$dir</span>
					</td>
					<td>
					<a href=\"?module=blog&page=editblog&var=$dir\"><img src=\"data/image/edit.png\" border=\"0\" title=\"$lang_blog7\" alt=\"$lang_blog7\"></a>		
					</td>
					<td>
					<a href=\"?module=blog&page=deleteblog&var=$dir\"><img src=\"data/image/delete_from_trash.png\" border=\"0\" title=\"$lang_blog6\" alt=\"$lang_blog6\"></a>		
					</td>
				</tr>
			</table>
			</div>"; }
   }
   closedir($path);
}

//Function: readout blogposts
//------------
function read_blog_posts($dir) {
   $path = opendir($dir);
   while (false !== ($file = readdir($path))) {
       if(($file !== ".") and ($file !== "..")) {
           if(is_file($dir."/".$file))
               $files[]=$file;
           else
               $dirs[]=$file;           
       }
   }
	if (!$files) {
	//Include Translation data
	include ("data/settings/langpref.php");
	include ("data/inc/lang/en.php");
	include ("data/inc/lang/$langpref");
	echo "<span class=\"kop4\">$lang_albums14</span>"; }

   if($files) {
	natcasesort($files);
	$files = array_reverse($files);

   foreach ($files as $file) {
	//Include Translation data
	include("data/settings/langpref.php");
	include("data/inc/lang/en.php");
	include("data/inc/lang/$langpref");
	//Include the post-information
	$var = $_GET['var'];	
	include("data/blog/$var/posts/$file");

echo "<div class=\"menudiv\" style=\"margin: 10px;\">
		<table>
			<tr>
				<td>
				<img src=\"data/modules/blog/images/blog.png\" alt=\"\" border=\"0\">
				</td>
				<td style=\"width: 500px;\">
				<span style=\"font-size: 17pt;\">$title</span>
				</td>
				<td>
				<a href=\"?module=blog&page=editpost&var=$file&var2=$var\"><img src=\"data/image/edit.png\" border=\"0\" title=\"$lang_blog11\" alt=\"$lang_blog11\"></a>		
				</td>
				<td>
				<a href=\"?module=blog&page=editreactions&var=$file&var2=$var\"><img src=\"data/modules/blog/images/reactions.png\" border=\"0\" title=\"$lang_blog19\" alt=\"$lang_blog19\"></a>		
				</td>
				<td>
				<a href=\"?module=blog&page=deletepost&var=$file&var2=$var\"><img src=\"data/image/delete_from_trash.png\" border=\"0\" title=\"$lang_blog12\" alt=\"$lang_blog12\"></a>		
				</td>
				</tr>
				<tr>
				<td></td>
				<td><span style=\"font-size: 12px; font-style: italic;\">$postdate</span></td>				
				</tr>
		</table>
</div>";
        }
   }
   closedir($path);
}

//Function: readout reactions on a blogpost
//------------
function read_blog_reactions($dir) {
   $path = opendir($dir);
   while (false !== ($file = readdir($path))) {
       if(($file !== ".") and ($file !== "..")) {
           if(is_file($dir."/".$file))
               $files[]=$file;
           else
               $dirs[]=$dir."/".$file;    
       }
   }
   
	if (!$files) {
	//Include Translation data
	include ("data/settings/langpref.php");
	include ("data/inc/lang/en.php");
	include ("data/inc/lang/$langpref");
	echo "<span class=\"kop4\">$lang_albums14</span>"; }
	
   if($files) {

   natcasesort($files);
	$files = array_reverse($files);
   
   foreach ($files as $file) {
			  $var = $_GET['var'];
			  $var2 = $_GET['var2'];
			  list($reactiondir, $extension) = explode(".", $var);
			  
			  //Include the reaction information
			  include("data/blog/$var2/reactions/$reactiondir/$file");
			  //Change html enters in real ones
			  $message = str_replace("<br />", "\n", $message);
			  
           //Include Translation data
			  include ("data/settings/langpref.php");
			  include ("data/inc/lang/en.php");
			  include ("data/inc/lang/$langpref");
			  
			  echo "<div class=\"menudiv\" style=\"margin: 10px;\">
			  <table>
			  <tr>
			  		<td>
			  			<img src=\"data/modules/blog/images/reactions.png\" alt=\"\" border=\"0\">
			  		</td>
			  		<td style=\"width: 600px;\">
			  			<form method=\"post\" action=\"\">
			  			<b>$lang_install17</b><br>
			  			<input name=\"cont1\" type=\"text\" value=\"$title\"><br><br>
			  			
			  			<textarea name=\"cont2\" rows=\"5\" cols=\"65\">$message</textarea><br><br>
			  			
			  			<input name=\"cont3\" type=\"hidden\" value=\"$name\">
			  			<input name=\"cont4\" type=\"hidden\" value=\"$postdate\">
			  			<input name=\"cont5\" type=\"hidden\" value=\"$file\">
			  			<input type=\"submit\" name=\"Submit\" value=\"$lang_install13\">
			  			</form>
			  		</td>
			  		<td>
			  			<a href=\"?module=blog&page=deletereactions&var=$file&var2=$var2&var3=$reactiondir\"><img src=\"data/image/delete_from_trash.png\" border=\"0\" title=\"$lang_blog21\" alt=\"$lang_blog21\"></a>
			  		</td>
			  </tr>
			  </table>
			  </div>";			  
			  }
   }
   closedir($path);
}
?>