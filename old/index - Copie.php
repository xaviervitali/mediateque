<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>
        table,
        tr,
        td,
        th {
            border: 1px solid black;
            border-collapse: collapse;
            text-align: center;

            padding: 40px;
            /* padding:10px;    */

        }

        table {
            border-radius: 20px;
            /* border: 2px solid black; */
            overflow: hidden;
        }


        td:nth-child(odd) {
            font-weight: bold;

        }

        tr:nth-child(odd) {
            background-color: aliceblue;
        }

        tr:first-child {
            background-color: cadetblue;
        }

        body {
            /* max-width:500px; */
            width: 80%;
            margin: auto;
        }

        input {
            margin: 30px;
            padding: 10px;
        }

        img {
            /* height:200px;     */
            width: 10rem;
            object-fit: cover;
            border-radius: 1rem;
        }

        @media screen and (max-width: 1000px) {
            body {
                width: 100%;
                /* font-size:2rem;    */
            }

            td {
                padding: 0;
            }


        }
    </style>
</head>

<body>


    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);




    $find = $_REQUEST["query"] ?? "";
    $idForm = $_REQUEST["idForm"] ?? "";
    $occ = 0;

    function sendSQL($requeteSQL, $tabAssoColonneValeur)
    {
        $database = 'MyVideos116';
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
    if ($idForm == "backup") {

        $date = date("_d-m-Y_H-i-s");
        $dir = "/volume2/Cloud_Syno/kodiDb/MesVideos$date.sql";
        echo "<p>Backing up database to '$dir'</p>";
        saveDB();
    }



    function readTableLine($nomTable, $id)
    {
        $id = intval($id);

        // ETAPE1: CONSTRUIRE LA REQUETE SQL
        $requeteSQL =
            <<<CODESQL

SELECT * FROM $nomTable
WHERE idShow = :id

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
    ?>

    <form method='get'>
        <input type='text' name='query' placeholder='Entrez votre recherche'>
        <input type='submit' value='Rechercher'>

    </form>
    <form>
        <input type='hidden' value='backup' name='idForm'>

        <button type='submit' name='backupSql'>Sauver la dB</button>
    </form>



    <table>
        <tr>
            <th>Serie</th>

            <th>Derniere saison présente</th>
            <th>Dernier épisode présent</th>
            <th>Derniere saison vue</th>
            <th>Dernier épisode vu</th>
        </tr>

        <?php


        $strSQl = "SELECT tvshow.c00 as nom, tvshow.c06  as img, episode.c12 as saison, episode.c13 as episode,  tvshow.idShow as idShow, episode.idFile from(select maxi from(select idShow,  max(`idEpisode`) as maxi from episode GROUP by idShow) as lastEp) as lastEp  inner join episode inner join tvshow where idEpisode= lastEp.maxi and tvshow.idShow=episode.idShow and tvshow.c00 like '%$find%' ORDER BY episode.idFile DESC";

        $derniersEpisode = sendSql($strSQl, []);

        foreach ($derniersEpisode as $episode => $idEpisode) {
            $occ++;
            $serie = $idEpisode["nom"];
            $saison = $idEpisode["saison"];
            $episode = $idEpisode["episode"];
            $img = $idEpisode["img"];
            $idShow = $idEpisode["idShow"];
            $idFile = $idEpisode["idFile"];
            $strSQLLastEp = sendSQL("select c12, c13, idShow as idShow from episode where `idEpisode` in (SELECT max(episode_view.`idEpisode`)FROM `episode_view` where idShow = $idShow and  playCount>= 1)", []);

            $c12 = 0;
            $c13 = 0;
            $delai = 0;
            $strFileAdd = sendSQL("SELECT `dateAdded` FROM `files` WHERE `idFile`=$idFile", []);
            $dateTime = date("Y-m-d H:i:s");
            foreach ($strFileAdd  as $laDate => $dateEpisode) {
                $dateAjout = date("d-m-Y H:i:s", strtotime($dateEpisode["dateAdded"]));
                $delai = round((strtotime($dateTime) - strtotime($dateEpisode["dateAdded"])) / 86400, 1);
            }
            foreach ($strSQLLastEp as $temp => $temp2) {
                $c12 = $temp2["c12"];
                $c13 = $temp2["c13"];
            }


            $img = strstr($img, '>https');
            $img = strstr($img, '</thumb>', true);
            $img = str_replace(">h", "h", $img);



            $strHtml = <<<STRHTML


<tr>
 <td><h2>$serie</h2>
 <img src="$img"> <p>idShow : $idShow</p></td>

 <td>$saison</td>
 <td><p>$episode<p>
<p>Ajouté le : $dateAjout</p> 
<p>$delai Jour(s)</p></td>
 <td>$c12</td>
 <td>$c13</td>
 </tr>


STRHTML;

            echo ($strHtml);
        }

        ?>
        <p>Nb de series : <?php echo ($occ) ?></p>
    </table>
</body>

</html>