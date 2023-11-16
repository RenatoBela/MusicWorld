<?php
$q = "music%20events";
$apiUrl = 'https://newsapi.org/v2/everything?q='. $q .'&apiKey=8302d861ae1b425c8317ad7f6aa956e7';

$ch = curl_init(); // Initialize cURL session
curl_setopt($ch, CURLOPT_URL, $apiUrl); // Set the URL to your API endpoint

// Set the User-Agent header
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: MusicNews/1.0.0' // Replace 'YourApp/1.0' with an identifier for your application
]);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Set cURL to return the response instead of outputting it
$response = curl_exec($ch); // Execute the cURL request and store the response

if ($response === false) {
    // cURL request failed
    echo 'cURL Error: ' . curl_error($ch);
} else {
    $data = json_decode($response, true);

    if ($data && isset($data['articles'])) {
        $articleIds = [];
        foreach ($data['articles'] as $article) {
            if (isset($article['source']['id'])) {
                $articleIds[] = $article['source']['id'];
            }
        }

        if (!empty($articleIds)) {
            $articleIdsString = implode(' ', $articleIds);
            echo 'Article IDs: ' . $articleIdsString;
        } else {
            echo 'No valid article IDs found for '. $q .' query.';
        }
    } else {
        echo 'No articles found for '. $q .' query.';
    }

    curl_close($ch); // Close cURL session
}
?>
