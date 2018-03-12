<?php
    function findPicturePath($dir,$file)
    {
    	$items = glob($dir.'/*');
    	$found='';

    	for($i = 0;$i < count($items); $i++)
    	{
    		if(is_dir($items[$i]))
    		{
    			//echo $items[$i].'</br>';
    			$found=findPicturePath($items[$i],$file);
    			if($found===$items[$i].'/'.$file)
    			{
    				return (string)$found;
    			}
    		}
    		else if($items[$i] === $dir.'/'.$file)
    		{
    			//echo "----".$items[$i].'<br/>';
    			return (string)$items[$i];
    		}
    		/*else {
    			echo "----".$items[$i].'<br/>';
    		}*/
    	}
    }

    function upload($index,$destination,$maxsize=FALSE,$extensions=FALSE)
    {
        if(!isset($_FILES[$index]) OR $_FILES[$index]['error'] > 0)
        {
            echo '<p>Une erreur s\'est produite lors de l\'upload</p>';
            return FALSE;
        }
        if($maxsize !== FALSE AND $_FILES[$index]['size'] > $maxsize)
        {
            echo '<p>Le fichier dépasse la taille maximale autorisée</p>';
            return FALSE;
        }
        $ext = substr(strrchr($_FILES[$index]['name'],'.'),1);
        if($extensions !== FALSE AND !in_array($ext,$extensions))
        {
            echo '<p>L\'extension du fichier uploadé n\'est pas autorisée</p>';
            return FALSE;
        }

        return move_uploaded_file($_FILES[$index]['tmp_name'],$destination);
    }

    //Suppression d'un dossier utilisateur

 // When the directory is not empty:
 function rrmdir($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir") rmdir($dir."/".$object); else unlink($dir."/".$object);
       }
     }
     reset($objects);
     rmdir($dir);
   }
 }
?>
