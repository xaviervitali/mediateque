<?php

/**
 * J'obtiens bien la liste des doublons et j'affiche le chemins des fichiers à supprimer
 */
require 'functions.php';

$listDoublonSql = sendSQL("SELECT   COUNT(*) AS nbr_doublon,idShow, c12, c13 FROM `episode`
GROUP BY idShow, c12, c13
HAVING   COUNT(*) > 1", []);

foreach ($listDoublonSql as $ep) {
    $strSql = "SELECT * FROM episode WHERE idShow=" . $ep["idShow"] . ' AND c12=' . $ep["c12"] . ' AND c13=' . $ep["c13"];
    $files = sendSQL($strSql, []);
    $count = 0;
    $delete = 0;
    $tab = [];

    foreach ($files as $file) {
        $tab[] = ['idFile' => $file['idFile'], 'c18' => $file['c18']];
    }

    if (count($tab) === 0) {
        // echo ("<p>Le fichier '" . $file . "' n'a pas pu être supprimé, merci de le supprimer manuellement</p>");
        exit;
    }
    for ($i = 0; $i < count($tab) - 1; $i++) {
        $path = $tab[$i]['c18'];
        $file = str_replace('smb://v38france/', "/volume2/Series", $path);
        var_dump($path);
        // try {
        //     unlink($file);
        //     $strSql = "DELETE FROM `episode` WHERE idFile=" . $tab[$i]['idFile'];
        //     sendSQL($strSql, []);
        // } catch (Exception $e) {
        // }
    }
}
echo ("<p>--- fin de suppression ---</p>");
// unlink("../../Temp/toto.vsmeta");
// $scandir = scandir("../../Temp/");
// foreach ($scandir as $fichier) {
// echo "$fichier<br />";
// }
?>

<a href="index.php"><button>revenir à liste des series</button></a>