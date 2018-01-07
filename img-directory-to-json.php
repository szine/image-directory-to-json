<?php
# To prevent browser error output
//header('Content-Type: text/javascript; charset=UTF-8');

# Path to image folder
$imageFolder = 'img/';

# Show only these file types from the image folder
$imageTypes = '{*.jpg,*.JPG,*.jpeg,*.JPEG,*.png,*.PNG,*.gif,*.GIF}';

# Set to true if you prefer sorting images by name
# If set to false, images will be sorted by date
$sortByImageName = false;

# Set to false if you want the oldest images to appear first
# This is only used if images are sorted by date (see above)
$newestImagesFirst = true;

# The rest of the code is technical

# Add images to array
$images = glob($imageFolder . $imageTypes, GLOB_BRACE);

# Sort images
if ($sortByImageName) {
    $sortedImages = $images;
    natsort($sortedImages);
} else {
    # Sort the images based on its 'last modified' time stamp
    $sortedImages = array();
    $count = count($images);
    for ($i = 0; $i < $count; $i++) {
        $sortedImages[date('YmdHis', filemtime($images[$i])) . $i] = $images[$i];
    }
    # Sort images in array
    if ($newestImagesFirst) {
        krsort($sortedImages);
    } else {
        ksort($sortedImages);
    }
}

$base64Array =  array();

foreach($sortedImages as $image) {
    $type = pathinfo($image, PATHINFO_EXTENSION);
    $data = file_get_contents($image);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    $base64Array[] = $base64;
}

$fp = fopen('results.json', 'w');
$out = array_values($base64Array);

fwrite($fp, json_encode($out, JSON_FORCE_OBJECT));
fclose($fp);

//echo json_encode($out, JSON_FORCE_OBJECT);

