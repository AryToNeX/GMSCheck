<?php
/*
   Copyright 2020 AryToNeX

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/

const URL = "https://storage.googleapis.com/play_public/supported_devices.csv";

if(!isset($_GET["brand"]) && !isset($_GET["codename"])){
	http_response_code(400);
	die(json_encode(["ok" => false, "error" => "You must specify device brand and codename in the request"]));
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$csv = explode("\n", trim(str_replace("\0", "", curl_exec($ch))));
// 0=> retail branding
// 1=> marketing name
// 2=> device codename
// 3=> device model
foreach($csv as $v){
	$v = str_getcsv($v);
	if(
		mb_strtolower($v[0]) == mb_strtolower($_GET["brand"]) &&
		mb_strtolower($v[2]) == mb_strtolower($_GET["codename"])
	){
		echo json_encode([
			"ok" => true,
			"brand" => $v[0],
			"marketing_name" => $v[1],
			"codename" => $v[2],
			"device_name" => $v[3]
		]);
		return;
	}
}

echo json_encode(["ok" => false, "error" => "The device is not certified"]);
