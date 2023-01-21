<?php
error_reporting(1);
require_once('vendor/autoload.php');


use Goutte\Client;


header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET["user"]))
    $_GET["user"] = "5980587";

function getProfile($user)
{
    $client = new Client();
    $url = 'https://sinta.kemdikbud.go.id/authors/profile/' . $user;

    $crawler = $client->request('GET', $url);
    $contents = $crawler->filter('.content-box');
    if ($contents->count() == 0)
        return [];
    // Find user profile on the page
    $profiles = $contents->each(function ($content) {
        $profile = new stdClass();
        $node = $content->filter('.p-3');
        $name = $node->filter('h3');
        $photo = $node->filter('img')->attr('src');
        $stat = $content->filter('.stat-profile');
        $nums = $stat->filter('.pr-num')->each(
            function ($el) {
                return $el->text();
            }
        );
        $scores = array();
        foreach ($nums as $score) {
            array_push($scores, $score);
        }
        $profile->name = $name->text();
        $profile->photo = $photo;
        $profile->sinta_score = $scores[0];
        $profile->sinta_score_3yr = $scores[1];
        $profile->sinta_score_afill = $scores[2];
        $profile->sinta_score_afill_3yr = $scores[3];
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
function getArticles($user, $source = "")
{
    $client = new Client();

    $url = 'https://sinta.kemdikbud.go.id/authors/profile/' . $user;
    if (isset($source))
        $url .= '/?view=' . $source;

    $crawler = $client->request('GET', $url);
    $contents = $crawler->filter('.profile-article')->filter('.ar-list-item');
    if ($contents->count() == 0)
        return [];
    // Find all the article elements on the page
    $articles = $contents->each(function ($node) {
        $article = new stdClass();
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

function getIprs($user, $source = "iprs")
{
    $client = new Client();
    $url = 'https://sinta.kemdikbud.go.id/authors/profile/' . $user;
    if (isset($source))
        $url .= '/?view=' . $source;

    $crawler = $client->request('GET', $url);
    $contents = $crawler->filter('.profile-article')->filter('.ar-list-item');
    if ($contents->count() == 0)
        return [];

    // Find all the article elements on the page
    $iprs = $contents->each(function ($node) {
        $ipr = new stdClass();
        $title = $node->filter('.ar-title');
        $link = $title->filter('a')->attr('href');
        $year = $node->filter('.ar-year');
        $ipr_number = $node->filter('.ar-cited');
        $ipr_cat = $node->filter('.ar-quartile');
        $pub = $node->filter('.ar-pub');
        $ipr->title = $title->text();
        $ipr->link = $link;
        $ipr->year = $year->text();
        $ipr->number = $ipr_number->text();
        $ipr->inventor = $pub->text();
        $ipr->category = $ipr_cat->text();

        // return $title->text()." ".$link." ".$year->text()." ".$cited->text();
        return $ipr;

    });

    // Print the text of each article
    $data = [];
    foreach ($iprs as $ipr) {
        //$title = $article->filter('.ar-title');
        //echo $article."\n";
        //echo $title."\n";
        array_push($data, $ipr);
    }
    return $data;
}
/**
 * 
 * Fungsi yang digunakan untuk mengambil data penelitian dan pengabdian
 * @param mixed $user
 * @param mixed $source
 * @return array
 * 
 * $source value 'services' or 'researches'
 */
function getResearches($user, $source = "researches", $page = 1)
{
    $client = new Client();
    $url = 'https://sinta.kemdikbud.go.id/authors/profile/' . $user;
    if (isset($source))
        $url .= '/?view=' . $source;

    if (isset($page))
        $url .= '&page=' . $page;

    $crawler = $client->request('GET', $url);

    // Find all the article elements on the page
    $contents = $crawler->filter('.profile-article')->filter('.ar-list-item');
    if ($contents->count() == 0)
        return [];

    $researches = $contents->each(function ($node) {
        $research = new stdClass();
        $title = $node->filter('.ar-title');
        $link = $title->filter('a')->attr('href');
        $year = $node->filter('.ar-year');
        $fund = $node->filter('.ar-quartile');
        $src = $node->filter('.text-info');
        // $pub = $node->filter('.ar-pub');
        $research->title = $title->text();
        $research->link = $link;
        $research->year = $year->text();
        $research->fund = $fund->text();
        $research->source = "";
        if ($src->count() > 0)
            $research->source = $src->text();


        // $research->pub = $pub->text();

        // return $title->text()." ".$link." ".$year->text()." ".$cited->text();
        return $research;

    });

    // Print the text of each article
    $data = [];
    foreach ($researches as $research) {
        //$title = $article->filter('.ar-title');
        //echo $article."\n";
        //echo $title."\n";
        array_push($data, $research);
    }
    return $data;
}
function summary($user)
{
    $client = new Client();
    $url = 'https://sinta.kemdikbud.go.id/authors/profile/' . $user;

    $crawler = $client->request('GET', $url);
    $contents = $crawler->filter('table tr');
    if ($contents->count() == 0)
        return [];

    $tableData = $contents->each(function ($row) {
        return $row->filter('td')->each(function ($cell) {
            return $cell->text();
        }
        );
    });
    array_shift($tableData);
    $summaries = [];
    foreach ($tableData as $el) {
        $label = array_shift($el);
        $summaries[$label]["scopus"] = $el[0];
        $summaries[$label]["gs"] = $el[1];
        $summaries[$label]["wos"] = $el[2];
    }
    return $summaries;
}
$sinta = new stdClass();
$sinta->profile = getProfile($_GET["user"]);
$gs = [];
$gs=array_merge($gs,getArticles($_GET["user"], "googlescholar", 1));
$gs=array_merge($gs,getArticles($_GET["user"], "googlescholar", 2));

$sinta->articles->scopus = getArticles($_GET["user"], "scopus");
$sinta->articles->wos = getArticles($_GET["user"], "wos");
$sinta->articles->googlescholar = $gs;
// // $sinta->articles->garuda = getArticles($_GET["user"],"garuda");
// // $sinta->articles->rama = getArticles($_GET["user"],"rama");
$sinta->iprs = getIprs($_GET["user"]);
$sinta->researches = getResearches($_GET["user"]);
$sinta->service = getResearches($_GET["user"], "services");
$sinta->summary = summary($_GET["user"]);

print_r($sinta);

// echo count($dt);
// echo json_encode($dt);