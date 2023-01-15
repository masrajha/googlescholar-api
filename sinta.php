<?php
require 'vendor/autoload.php';

use Goutte\Client;

$client = new Client();
$crawler = $client->request('GET', 'https://sinta.kemdikbud.go.id/authors/profile/6021756');

// Find all the article elements on the page
$articles = $crawler->filter('.profile-article')->filter('.ar-list-item')->each(function 
($node){
    $title = $node->filter('.ar-title');
    $link  = $title->filter('a')->attr('href');
    $year  = $node->filter('.ar-year');
    $cited  = $node->filter('.ar-cited');
    return $title->text()." ".$link." ".$year->text()." ".$cited->text();
});

// Print the text of each article
foreach ($articles as $article) {
    //$title = $article->filter('.ar-title');
    echo $article."\n";
    //echo $title."\n";
}

