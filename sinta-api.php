<?php
error_reporting(1);
require 'vendor/autoload.php';

use Goutte\Client;

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET["user"]))
    $_GET["user"] = "6021756";

    function getProfile($user){
        $client = new Client();
        $url = 'https://sinta.kemdikbud.go.id/authors/profile/' . $user;
    
        $crawler = $client->request('GET', $url);
    
        // Find user profile on the page
        $profiles = $crawler->filter('.content-box')->each(function ($content) {
            $profile = new stdClass();
            $node = $content->filter('.p-3');
            $name = $node->filter('h3');
            $photo = $node->filter('img')->attr('src');
            $stat = $content->filter('.stat-profile');
            $nums = $stat->filter('.pr-num')->each(function ($el) {
                return $el->text();
            }
            );
            $scores = array();
            foreach ($nums as $score) {
                array_push($scores, $score);
            }
            $profile->sinta_score = $scores[0];
            $profile->sinta_score_3yr = $scores[1];
            $profile->sinta_score_afill = $scores[2];
            $profile->sinta_score_afill_3yr = $scores[3];
            $profile->name = $name->text();
            $profile->photo = $photo;
            return $profile;
            //print_r($profile);
            //return $name->text()." ".$photo." ".$scores[0];
        });
        return $profiles[0];
    }
/**
 * Get all article from SINTA
 * has two parameters, user and source
 * - user is sinta-id
 * - source value: scopus, wos, googlescholar, garuda, rama 
 */
function getArticles($user,$source="")
{
    $client = new Client();
    $url = 'https://sinta.kemdikbud.go.id/authors/profile/' . $user;
    if (isset($source))
        $url .= '/?view=' . $source;

    $crawler = $client->request('GET', $url);

    // Find all the article elements on the page
    $articles = $crawler->filter('.profile-article')->filter('.ar-list-item')->each(function ($node) {
        $article=new stdClass();
        $title = $node->filter('.ar-title');
        $link = $title->filter('a')->attr('href');
        $year = $node->filter('.ar-year');
        $cited = $node->filter('.ar-cited');
        $pub = $node->filter('.ar-pub');
        $article->title = $title->text();
        $article->link = $link;
        $article->year = $year->text();
        $article->cited = $cited->text();
        $article->pub = $pub->text();

        // return $title->text()." ".$link." ".$year->text()." ".$cited->text();
        return $article;

    });

    // Print the text of each article
    $data = [];
    foreach ($articles as $article) {
        //$title = $article->filter('.ar-title');
        //echo $article."\n";
        //echo $title."\n";
        array_push($data, $article);
    }
    return $data;
}


/*
foreach($profiles as $p){
echo $p."\n";
}
*/
$sinta = new stdClass();
$sinta->profile = getProfile($_GET["user"]);
$sinta->data = getArticles($_GET["user"]);
//print_r($sinta);
echo json_encode($sinta);