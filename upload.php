
<?php


					$target_dir = "fotos_jogadores/";
					$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
					$uploadOk = 1;
					$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
					// Check if image file is a actual image or fake image
					if(isset($_POST["submit"])) {
					  $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
					  if($check !== false) {
					    echo "File is an image - " . $check["mime"] . ".";
					    $uploadOk = 1;
					  } else {
					    echo "File is not an image.";
					    $uploadOk = 0;
					  }
					}
	

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
</head>
<body>
	<h1>UPLOAD</h1>
 	<form method="post" enctype="multipart/form-data">
		  Select image to upload:
		  <input type="file" name="fileToUpload" id="fileToUpload">
		  <input type="submit" value="Upload Image" name="submit">
	</form>

</body>
</html>