<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


function sendSQL($requeteSQL, $tabAssoColonneValeur)
{
    $database = 'MyVideos119';
    $user = 'root';
    $pass = 'Soleil13';
    $host = 'localhost';
    $portSQL      = 3306;
    $charsetSQL   = "utf8";
    $dsnSQL       = "mysql:host=$host;port=$portSQL;charset=$charsetSQL;dbname=$database";

    $objPDO = new PDO($dsnSQL, $user, $pass, []);

    $objPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

    $objPDOStatement = $objPDO->prepare($requeteSQL);

    $objPDOStatement->execute($tabAssoColonneValeur);


    $objPDOStatement->setFetchMode(PDO::FETCH_ASSOC);

    return $objPDOStatement;
}



function readTableLine($nomTable, $id)
{
    $id = intval($id);

    // ETAPE1: CONSTRUIRE LA REQUETE SQL
    $requeteSQL =
        <<<CODESQL

SELECT * FROM $nomTable
WHERE idShow = $id

CODESQL;

    // JE PEUX MAINTENANT ENVOYER LA REQUETE SQL PREPAREE
    // ET JE COMPLETE AVEC LE TABLEAU DES VALEURS (VIDE ICI)
    $objPDOStatement = sendSQL($requeteSQL, ["id" => $id]);

    return $objPDOStatement;
}

function saveDB()
{
    global $database, $user, $pass, $host, $date, $dir, $portSQL, $charsetSQL;
    system("mysqldump --host=$host --user=$user --password=$pass    $database > $dir");
    system("gzip $dir");
}


function clearTags($img)
{
    $img = strstr($img, '>https');
    $img = strstr($img, '</thumb>', true);
    $img = str_replace(">h", "h", $img);
    return $img;
}

function getDataString($str, $startChr, $endChr)
{
    $start = strpos($str, $startChr);
    $end = strpos($str, $endChr, $start + 1);

    return substr($str, $start, $end + strlen($endChr) - $start);
}

function getImageStringFromC06($str){
    $startStr = 'preview="';
    $startStrtLen = strlen($startStr);
    $startPos = strpos($str, $startStr) +   $startStrtLen;
    $endStr = '"';
    $endStrtLen = strlen($endStr);
    $endpos =  strpos($str, $endStr, $startPos);
  
  
    return substr($str, $startPos ,$endpos - $startPos);

}