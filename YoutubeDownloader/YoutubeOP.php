<?php 

class YouTubeDownloader {
    /* 
        The Regex pattern which is used to validate the provided URL is a from a valid youtube video
     */ 
    private $link_pattern = "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed)\/))([^\?&\"'>]+)/"; 
    //Holds the video ID ex: for -> https://www.youtube.com/watch?v=KtYu6_YOM_E the videoID will be -> KtYu6_YOM_E
    private $video_id; 
    //Holds the title of the video
    private $video_title; 
    //Holds the provided video URL
    private $video_url; 
    /* 
        Derieve the contents of the URL to test whether it is valid
     */ 
    private function getVideoInfo(){ 
        return file_get_contents("https://www.youtube.com/get_video_info?video_id=".$this->extractVideoId($this->video_url)."&cpn=CouQulsSRICzWn5E&eurl&el=adunit"); 
    } 
      
    /* 
        Retrieve the video ID from the provided video URL
     */ 
    private function extractVideoId($video_url){ 
        $parsed_url = parse_url($video_url); 
        if($parsed_url["path"] == "youtube.com/watch"){ 
            $this->video_url = "https://www.".$video_url; 
        }elseif($parsed_url["path"] == "www.youtube.com/watch"){ 
            $this->video_url = "https://".$video_url; 
        } 
         
        if(isset($parsed_url["query"])){ 
            $query_string = $parsed_url["query"]; 
            //parse the string separated by '&' to array 
            parse_str($query_string, $query_arr); 
            if(isset($query_arr["v"])){ 
                return $query_arr["v"]; 
            } 
        }    
    } 
     
    /* 
        Check whether the RegEx pattern matches, else return false
     */ 
    public function getDownloader($url){ 
        //Matching with the provided URL
        if(preg_match($this->link_pattern, $url)){ 
            $this->video_url = $url;
            return $this; 
        } 
        return false; 
    } 
      
    //Get download information with video contents
    public function getVideoDownloadLink(){ 
        parse_str($this->getVideoInfo(), $data); 
        //decode the JSON array
        $videoData = json_decode($data['player_response'], true); 
        $videoDetails = $videoData['videoDetails']; 
        $streamingData = $videoData['streamingData']; 
        $streamingDataFormats = $streamingData['formats']; 
        //Get video title
        $this->video_title = $videoDetails["title"]; 
        //Get the video information inclusiding download URLs
        $video_data_map = array(); 
        $videoData = array();
        foreach($streamingDataFormats as $stream){ 
            $videoData["title"] = $this->video_title;
            $videoData["quality"] = $stream["quality"];
            $videoData["qualityLabel"] = $stream["qualityLabel"];
            $videoData["url"] = $stream["url"];
            $video_data_map [] = $videoData;
        } 
        return $video_data_map; 
    } 
    
    //Checks whether the provided URL is from a valid video
    public function hasVideo(){ 
        $valid = true; 
        parse_str($this->getVideoInfo(), $data); 
        if($data["status"] == "fail"){ 
            $valid = false; 
        }  
        return $valid; 
    } 
      
}

?>
