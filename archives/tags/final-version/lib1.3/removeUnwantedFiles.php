<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html>
<head>
	<title>Directories and Files Remover by Ratius</title>
</head>
	<body>
	<div>
	<? 
	if (!isset($_POST['delete'])) {
		echo '
		<form id="form" method="post" action="">
			<div style="width:50px;margin:auto;text-align:center;">
				<input type="submit" name="delete" value="Start script" />
			</div>
		</form>
		';
	} else {
/**
 * Ratius' Recursive Deletion script
 * 
* @desc This script will prune all directories and files specified
* by the two lists above. The directories list will prune all directories
* and files under the named directory and the files list will delete/
* only files encountered in the loop and delete them if they were not
* already deleted under named directories.
* 
* Note that this script will NOT delete specific directories, you can only
* name directories in the list and everything under it will be pruned.
* 
* Example: delete .svn files in a project along with it's .project, Thumb.dn files
* and .settings/.cache folders would be declared as:
* 
* static $directories = array('.svn', '.settings', '.cache');
* static $files = array('Thumbs.db', '.project');
* 
* This will remove every folders that are .svn, .settings and .cache and remove
* the files Thumbs.db and .project under every directories where this script
* is located at.
* 
* @author Steven Rosato
* @version 1.2 - Last revision: 2009-02-03
*/

function recursive_remove_directory( $directory, $empty = FALSE )
{
	
	/***** EDIT THOSE FOR DIFFERENT RESULTS *****/
	static $directories = array('.svn', '.settings', '.cache');
	static $files = array('Thumbs.db', '.project');
	/*******************************************/
	
	static $nbFilesDeleted = 0;
	static $nbDirectoriesDeleted = 0;
	
	// if the path has a slash at the end we remove it here
	if( substr($directory, - 1) == '/' ) {
		$directory = substr($directory, 0, - 1);
	}
	
	/* Handle files and directories errors */
	if( ! file_exists($directory) || ! is_dir($directory) ) {
		echo "<span style=\"color:red;\">Not a file or directory, script HALTED!</span>";
		return FALSE;
	}elseif(!is_readable($directory)){
		echo "<span style=\"color:red;\">Directory $directory not readable! Script HALTED!</span>";
		return FALSE;
	}else{
		$handle = opendir($directory);
		
		//echo "Opening $directory ... <br />";

		/* scan through the items inside and call the recursive fonction again */
		while (FALSE !== ($item = readdir($handle)))
		{
			/* if the filepointer is not the current directory or the parent directory */
			if($item != '.' && $item != '..') {
				/* we build the new path to delete */
				$path = $directory.'/'.$item;

				/* Remove files according to the array */
				if(is_dir($path)) {
					recursive_remove_directory($path); //recursive call
				}else{
					$i=0;
					$found = FALSE;
					while($i < count($directories) && !$found){
						$found = in_array($directories[$i], explode('/', $directory));
						$i++;
					}
					
					if(!$found) {
						$i=0;
						$found = FALSE;
						while($i < count($files) && !$found){
							$found = $item == $files[$i];
							$i++;
						}
					}
					
					/* we remove the file */
					if($found){
						@chmod($path, 0777); //removes read-only attribute
						unlink($path);
						$nbFilesDeleted++;
						echo "<span style=\"color:green;\">Removed file $path</span><br />";
					}
				}
			}
		}
		closedir($handle);

		if($empty == FALSE) {
			
			/* Delete directories according to the array */
			$i=0;
			$found = FALSE;
			while($i < count($directories) && !$found){
				$found = in_array($directories[$i], explode('/', $directory));
				$i++;
			}
			$rmDir = $found;
			
			if($rmDir){
				if(!@rmdir($directory)){
		 			echo "<span style=\"color:red;\">Cannot remove directory $directory</span><br />";
					return FALSE;
				} else {
					echo "<span style=\"color:green;\">Removed directory $directory</span><br />";
					$nbDirectoriesDeleted++;
				}
			}
		}
		return array($nbFilesDeleted,$nbDirectoriesDeleted);
	}
}

$deletedComponents = recursive_remove_directory('.', FALSE); //method call

/* Number of files and directories deleted */
$f = $deletedComponents[0] > 1 ? 'files' : 'file'; 
$d = $deletedComponents[1] > 1 ? 'directories' : 'directory'; 

echo "<br />Deleted <strong>$deletedComponents[0]</strong> $f.";
echo "<br />Deleted <strong>$deletedComponents[1]</strong> $d.";
	} //end else for $_POST
?>
</div>
</body>
</html>