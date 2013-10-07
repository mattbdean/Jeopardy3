<?php

function getConfigJson($name) {
	return get_object_vars(json_decode(file_get_contents("cfg/$name.json")));
}
?>