<?php 

include_once 'YoutubeOP.php'; 

if (empty($_POST['url'])) {
    $errors = array('err_title'=>'Invalid parameters','err_msg'=>'Parameter (url) not found. Provide the required parameters using POST method');
    echo json_encode(array("ERROR"=>array($errors)));
    die('exit');
}

//get data for URL
$youtubeURL = $_POST['url'];
$handler = new YouTubeDownloader(); 
$downloader = $handler->getDownloader($youtubeURL);

if($downloader === false) {
    //Return error if the provided url is not valid
    $errors = array('err_title'=>'URL not valid','err_msg'=>'Could not find any video or the provided url is not from a valid video.');
    echo json_encode(array("ERROR"=>array($errors)));
    die('exit');
}

if($downloader->hasVideo()){ 
    $videoDownloadLink = $downloader->getVideoDownloadLink(); 
    echo json_encode(array("RESULT"=>$videoDownloadLink));
}else{ 
    $errors = array('err_title'=>'No video found','err_msg'=>'Could not find any video from the provided url');
    echo json_encode(array("ERROR"=>array($errors)));
}

?>