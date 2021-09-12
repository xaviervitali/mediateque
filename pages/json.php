
<?php
require_once "../functions.php";
$idForm = $_REQUEST["idForm"] ?? "";
$database = 'MyVideos119';
$user = 'root';
$pass = 'Soleil13';
$host = 'localhost';
$portSQL      = 3307;
$charsetSQL   = "";
$dsnSQL       = "mysql:host=$host;port=$portSQL;charset=$charsetSQL;dbname=$database";

if ($idForm  == "backup") {
    $date = date("_d-m-Y_H-i-s");
    $dir = "/volume1/web/BackupSql/kodiDb/MesVideos$date.sql";
    try {
        system("mysqldump --host=$host --user=$user --password=$pass $database > $dir");
        system("gzip $dir");
        echo json_encode(["backup" => "Sauvegarde de la base de donnée Kodi effectuée dans '"  . $dir . "'"]);
    } catch (Exception $e) {
        echo json_encode(["backup" => $e]);
    }
    die;
}


$lastEpisodesFromTvShowPDO = sendSQL("SELECT e.idFile , e.c12 AS 'saison', e.c13 AS 'ep' , e.idShow , t.c00 as 'title' ,t.c08 as genre, t.c01 as 'pitch' , t.c06 as 'poster', t.dateAdded as 'downloadedAt',t.lastPlayed 
FROM `episode` e 
 INNER JOIN (SELECT MAX(`idFile`) as 'idFileMax' FROM `episode` GROUP BY `idShow`) m on e.idFile=m.idFileMax INNER JOIN tvshow_view t on t.idShow=e.idShow INNER JOIN files f on f.idFile=e.idFile ORDER BY t.c00 ASC", [])
;


$lastEpisodesFromTvShow = [];
foreach ($lastEpisodesFromTvShowPDO as $lastEpisode) {

    $epList = [];
    $episodes = readTableLine("episode_view", $lastEpisode['idShow']);

    // foreach ($episodes as $k) {
    //     $k["c06"] = getDataString($k["c06"], "http", ".jpg");
    //     $epList[] = $k;
    // }


    // $epGuide = getDataString($lastEpisode['ep_guide'], "http", "rating");
    // $epGuide = json_decode($epGuide);
    // $lastEpisode['ep_guide'] = $epGuide;
    $lastEpisode['poster'] =
        (getImageStringFromC06($lastEpisode['poster']));


    // $lastEpisode['episodesList'] = $epList;
    $lastEpisodesFromTvShow[] = $lastEpisode;

}

 echo(json_encode($lastEpisodesFromTvShow));

?>

