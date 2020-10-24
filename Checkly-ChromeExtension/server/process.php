<?php
include("simple_html_dom.php");

// Get title from news article 
$url = $_GET['url'];
$c_url = parse_url($url)["host"];
if(strpos($c_url, 'www.') !== false){
    $c_url = substr(parse_url($url)["host"],4); 
}
$html = new simple_html_dom();
$html->load_file($url); 
$titleraw = $html->find('title',0);
$title = $titleraw->innertext;

//$title = "As U.S. budget fight looms, Republicans flip their fiscal script";
$title = urlencode($title);

// Preprocessing
$processingurl = "http://localhost:9814/title?title=".$title;

// Access API to preprocess title ready to go into model 
$ch = curl_init($processingurl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
$data = json_decode($result);
//print_r($data[0]);
curl_close($ch);

// Access model API and display prediction 
$modelurl = 'http://localhost:8089/serving_default/predict';
$ch = curl_init($modelurl);
$d = array("input_2" => $data[0]);
$payload = json_encode(array("instances" => $d));
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);
$data = json_decode($result);
$pred = floatval($data->predictions->input_2->output[0]);
$score = (1 - round($pred,2))*100;

// Edit high score for formatting purposes
if($score == 100){
    $score = 99;
}
echo $score;
?>

