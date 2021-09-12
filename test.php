<?php
$json = file_get_contents('https://api.tmdb.org/3/tv/66840?api_key=6a5be4999abf74eba1f9a8311294c267&language=fr&append_to_response=content_rating');
$obj = json_decode($json);
var_dump($obj);
