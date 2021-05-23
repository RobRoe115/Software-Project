<?php

require 'includes/init.php';

if(isset($_SESSION['user_id']) && isset($_SESSION['email'])){
    $user_data = $user_obj->find_user_by_id($_SESSION['user_id']);
    if($user_data ===  false){
        header('Location: logout.php');
        exit;
    }
        // FETCH ALL USERS WHERE ID IS NOT EQUAL TO MY ID
    $all_users = $user_obj->all_users($_SESSION['user_id']);
}
else{
    header('Location: logout.php');
    exit;
}
// Create database connection
$username = "id15736258_robertsusername";
$password = "BN2^weul1OY<k5eC";
$database = "id15736258_softwareproject";
$link = new mysqli("localhost", $username, $password, $database);
  // Initialize message variable
  $msg = "";
  $id =$_SESSION['user_id'];
  $sqlquery="SELECT username FROM users WHERE id=$id";
  $namedata = $link->query("$sqlquery");
  $row2 = mysqli_fetch_array($namedata, MYSQLI_ASSOC);
  $info2 = $row2['username'];
  $name = $info2;

	//get jdata from database
	if($data = $link->query("SELECT jdata FROM users WHERE id=$id")){
		//echo "Record got successfully";
	} else {
		echo "Error getting record: " . $link->error;
	}
	while($row = mysqli_fetch_array($data, MYSQLI_ASSOC)) {
	//putting specific data in txt file
		file_put_contents('GeoJSON.txt', ($row['jdata']));
	}
	$jdata=file_get_contents('GeoJSON.txt', true);
	$data=substr($jdata, 0,-3);
	//file_put_contents('GeoJSON.js', $jdata);
	
	//edit input info
	$lat_err ="USE +EAST AND -WEST";
	$long_err ="USE +NORTH AND -SOUTH";
	
  // If upload button is clicked ...
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
  	// Get image name
  	$image = $_FILES['image']['name'];
  	// Get text
  	$text = mysqli_real_escape_string($link, $_POST['text']);

  	// image file directory
  	$target = "images/".basename($image);

  	$sql = "INSERT INTO images (image,text,user) VALUES ('$image', '$text','$name')";
  	// execute query
  	mysqli_query($link, $sql);

  	if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
  		$msg = "Image uploaded successfully";
  	}//end if
  	else{
  		$msg = "Failed to upload image";
  	}//end else
  	$file = 'test.js';
  	$test = "test";
    $impath = "https://ncisoftwareproject.000webhostapp.com/images/".$image;
    if(empty(trim($_POST["lat"]))){
			$lat_err = "Please enter value";
		} else{
			$lat = trim($_POST["lat"]);
		}
		if(empty(trim($_POST["long"]))){
			$long_err = "Please enter value";
		} else{
			$long = trim($_POST["long"]);
		}
	    if(!empty($lat && $long)){
	        $info = '{
	        type: "Feature",
		  "geometry": { "type": "Point", "coordinates": ['.$lat.', '.$long.']},
		  "properties": {
			  "title": "'.$name.'",
			  "marker-color": "#3c4e5a",
			  "marker-symbol": "marker",
			  "marker-size": "large",

			  // Store the image url and caption in an array.
			  "images": [
				["'.$impath.'","'.$test.'"],

			  ]
		  }
	  },
	  ]};';
    	    $insert = $data.$info;
    	    file_put_contents('GeoJSON.js', $insert);
    	  
    	    $json_data = json_encode($insert);
    	    
	        if($data = $link->query("UPDATE users SET jdata= $json_data WHERE id =$id")){
		           // echo "Record got successfully";
	        } else {
		        echo "Error getting record: " . $link->error;
	        }
    		
	    }
  }//end if
 

  mysqli_close($link);
  
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Image Upload</title>
		<link rel="stylesheet"type="text/css" href="style.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
		<link rel="stylesheet" href="style.css"/>
		<script src='https://code.jquery.com/jquery-1.11.0.min.js'></script>
	</head>

	<body>
		<div class="container">
				<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
				<div class="top">
				<div class="row">
					<div class="col-sm-3">
					</div>
					<div class="col-sm-6">
						<br><br>
					</div>
					<div class="col-sm-3">
					<br><br>
					<h4><?php echo  $user_data->username;?></h4>
					</div>	
				</div>
			</div>
				<div class="bottom">
					<div class="row">
					<div class="col-xs-4 col-sm-2">
				  <div class="sidebar">
					<ul class="nav">
					  <li>
						<a href="map.php">
						  <i class="zmdi zmdi-link"></i> Map
						</a>
					  </li>
					  <br>
					  <li>
						<a href="upload.php">
						  <i class="zmdi zmdi-widgets"></i> Upload
						</a>
					  </li>
					  <br>
					  <li>
						<a href="profile.php">
						  <i class="zmdi zmdi-calendar"></i> Friends
						</a>
					  </li>
					  <br>
					  <li>
						<a href="logout.php">
						  <i class="zmdi zmdi-comment-more"></i> Log Out
						</a>
					  </li>
					</ul>
				  </div>
				  </div>
						<div class="up"
						<br><br>
						<div class="col-xs-8 col-sm-10">
						<form method = "post" action = "upload.php" enctype="multipart/form-data" method="post">
							<input type="hidden" name="size" value="1000000">
							<h3>Upload a Photo </h3>
							<h6>STEP 1 - Select the photo you want to upload.</h6>
							<div>
								<input type="file" name="image">
							</div>
							<br>
							<h6>STEP 2 - Write something about the photo you are uploading.</h6>
							<div>
								<textarea name ="text" cols ="40" rows="4" name="image_text" placeholder="Say something about this image..."></textarea>
							</div>
														<br>
							<h6>STEP 3 - Enter The longitude and latitude of the image.</h6>
							<div class="form-group <?php echo (!empty($lat_err)) ? 'has-error' : ''; ?>">
                                <label>Latitude</label>
                			    <input type="text" name="lat" class="form-control">
                			    <span class="help-block"><?php echo $lat_err; ?></span>
                		    </div>    
                		    <div class="form-group <?php echo (!empty($long_err)) ? 'has-error' : ''; ?>">
                			    <label>Longitude</label>
                		    	<input type="text" name="long" class="form-control">
                		    	<span class="help-block"><?php echo $long_err; ?></span>
                            </div>
							
							<br>
							<h6>STEP 4 - Click the Submit button!</h6>
							<div class ="subbtn">
							<input type="submit" name="Submit" value="Submit">
							</div>
						</div>
					</form>
					</div>
			</div>
		</div>    
	</body>
</html>