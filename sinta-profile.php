<?php
error_reporting(1);
header('Content-Type: application/json; charset=utf-8');

if(!isset($_GET["user"]))
        $_GET["user"]="6021756";


require 'vendor/autoload.php';

use Goutte\Client;

$client = new Client();
$url = 'https://sinta.kemdikbud.go.id/authors/profile/'.$_GET["user"];
if (isset($_GET["view"]))
   $url .= '/?view='.$_GET["view"];

$crawler = $client->request('GET', $url);

// Find all the article elements on the page
$articles = $crawler->filter('.profile-article')->filter('.ar-list-item')->each(function 
($node){
    $title = $node->filter('.ar-title');
    $link  = $title->filter('a')->attr('href');
    $year  = $node->filter('.ar-year');
    $cited  = $node->filter('.ar-cited');
    $article->title = $title->text();
    $article->link = $link;
    $article->year = $year->text();
    $article->cited = $cited->text();

   // return $title->text()." ".$link." ".$year->text()." ".$cited->text();
   return $article;

});

// Print the text of each article
$data=[];
foreach ($articles as $article) {
    //$title = $article->filter('.ar-title');
    //echo $article."\n";
    //echo $title."\n";
    array_push($data,$article);
}


// Find user profile on the page
$profiles = $crawler->filter('.content-box')->each(function($content){
    $node = $content->filter('.p-3');
    $name = $node->filter('h3');
    $photo = $node->filter('img')->attr('src');
    $stat = $content->filter('.stat-profile');
    $nums = $stat->filter('.pr-num')->each(function($el){
   	return $el->text();
   });
    $scores = array();
    foreach ($nums as $score) {
        array_push($scores,$score);
    }
    $profile->sinta_score=$scores[0];
    $profile->sinta_score_3yr=$scores[1];
    $profile->sinta_score_afill=$scores[2];
    $profile->sinta_score_afill_3yr=$scores[3];
    $profile->name=$name->text();
    $profile->photo=$photo;
    return $profile;
    //print_r($profile);
    //return $name->text()." ".$photo." ".$scores[0];
});
/*
foreach($profiles as $p){
  echo $p."\n";
}
*/

$sinta->profile=$profiles[0];
$sinta->data=$data;
//print_r($sinta);
echo json_encode($sinta);

