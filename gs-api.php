<?php
header('Content-Type: application/json; charset=utf-8');
require 'vendor/autoload.php';

use Goutte\Client;

if (!isset($_GET["user"]))
    $_GET["user"] = "wuHZs4sAAAAJ";

function getProfile($user)
{
    $client = new Client();
    $url = 'https://scholar.google.com/citations?user=' . $user;
    $gs = new stdClass();

    $crawler = $client->request('GET', $url);
    $name = $crawler->filter('#gsc_prf_in');
    $affiliation = $crawler->filter('#gsc_prf_i > div:nth-child(2)');
    
    $gs->name=$name->text();
    $gs->affiliation=$affiliation->text();

    $tableContent = $crawler->filter('#gsc_a_b');

    $tableData = $tableContent->filter('tr')->each(function ($row){
        $pub = new stdClass();
        $title = $row->filter('td.gsc_a_t > a');
        $authors = $row->filter('td.gsc_a_t > div:nth-child(2)');
        $jurnal = $row->filter('td.gsc_a_t > div:nth-child(3)');
        $cited = $row->filter('td.gsc_a_c > a');
        $year = $row->filter('td.gsc_a_y > span');

        $pub->title=$title->text();
        $pub->authors=$authors->text();
        $pub->jurnal=$jurnal->text();
        $pub->cited=$cited->text();
        $pub->year=$year->text();

        return $pub;
        // return $row->filter('td')->each(function ($cell){
        //     return $cell->text();
        // });
    });
    // echo $name->text() . " " . $affiliation->text();
    
    $gs->publications=$tableData;
    return $gs;
}

echo json_encode(getProfile($_GET["user"]));