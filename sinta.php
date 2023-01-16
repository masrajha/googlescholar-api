<?php
header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET["user"]))
    $_GET["user"] = "6800649";


require 'vendor/autoload.php';

use Goutte\Client;

function summary($user)
{
    $client = new Client();
    $url = 'https://sinta.kemdikbud.go.id/authors/profile/' . $user;
    if (isset($source))
        $url .= '/?view=' . $source;

    $crawler = $client->request('GET', $url);
    $tableData = $crawler->filter('table tr')->each(function ($row) {
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

print_r(summary($_GET["user"]));

