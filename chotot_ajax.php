<?php

function swap_array(&$array, $key1, $key2) {
    $temp = array();
    foreach ($array as $key => $value) {
        if ($key != $key1 && $key != $key2) {
                $temp[$key] = $value;
        } else {
            if ($key == $key1) {
                $temp[$key2] = $array[$key2];
            }
            if ($key == $key2) {
                $temp[$key1] = $array[$key1];
            }
        }
    }
    $array = $temp;
}

$data = json_decode(file_get_contents('chotot_data.json'), true);

if (isset($_POST['ad1']) && isset($_POST['ad2'])) {
    swap_array($data, $_POST['ad1'], $_POST['ad2']);
    file_put_contents('chotot_data.json', json_encode($data));
    exit();
}

if (isset($_GET)) {
    $choTotHtml = file_get_contents('http://www.chotot.vn/tp_ho_chi_minh');

    $doc = new DOMDocument();
    @$doc->loadHTML($choTotHtml);

    $imgTags = $doc->getElementsByTagName('img');
    $newAds = array();

    foreach ($imgTags as $imgTag) {
        if ($imgTag->getAttribute('class') == 'thumbnail') {
            if (!empty($imgTag->getAttribute('data-original'))) {
                $src = $imgTag->getAttribute('data-original');
            } else {
                $src = $imgTag->getAttribute('src');
            }
            $tempArray = explode('/', $src);
            $size = sizeof($tempArray);
            $fileName = $tempArray[$size-1];
            $folder = $tempArray[$size-2];
            $tempArray2 = explode('.', $fileName);
            
            if (!array_key_exists($tempArray2[0], $data)) {
                $newAds[$tempArray2[0]] = $folder;
            }
        }
    }
    
    if (sizeof($newAds)) {
        $data = $newAds + $data;
    }
    
    file_put_contents('chotot_data.json', json_encode($data));
    echo json_encode($newAds);
    exit();
}
