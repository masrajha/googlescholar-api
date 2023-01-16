<?php
header('Content-Type: application/json; charset=utf-8');

if(!isset($_GET["user"]))
        exit -1;


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

echo json_encode($data);

