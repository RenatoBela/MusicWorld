<?php

echo '
<p>Choose news type:</p>
<form name="rega" method="POST">
    <select name="tip" id="tip">
        <option value="rock%20metal%20music"';
if (isset($_POST['tip']) && $_POST['tip'] === 'rock%20metal%20music') {
    echo ' selected="selected"';
}
echo '>Music news</option>
        <option value="rock%20metal%20music%20events"';
if (isset($_POST['tip']) && $_POST['tip'] === 'rock%20metal%20music%20events') {
    echo ' selected="selected"';
}
echo '>Events</option>
    </select>
    <input type="submit" value="Search">
</form>
';

if (isset($_POST['search'])) {
    $_GET['page'] = 1;
    $_SESSION['current_tip'] = $_POST['tip'];
}

if (!isset($_POST['tip'])) {
    $_POST['tip'] = $_SESSION['current_tip'];
} else {
    // Check if the search type has changed, and if so, reset the page to 1
    if (isset($_SESSION['current_tip']) && $_SESSION['current_tip'] !== $_POST['tip']) {
        $_SESSION['current_tip'] = $_POST['tip'];
        $_GET['page'] = 1;
        $_SESSION['displayedArticles'] = [];
    }
}

$apiUrl = 'https://newsapi.org/v2/everything?q=' . $_POST['tip'] . '&apiKey=8302d861ae1b425c8317ad7f6aa956e7';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: MusicNews/1.0.0' 
]);

$response = curl_exec($ch); 

if ($response === false) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    $data = json_decode($response, true);

    if ($data && isset($data['articles'])) {
        $articles = $data['articles'];

        echo '<h1>NEWS</h1>';

        $articlesPerPage = 20;  // Number of articles per page
        $totalArticles = count($articles);
        $totalPages = ceil($totalArticles / $articlesPerPage);
        $currentPage = isset($_GET['page']) ? max(1, min($totalPages, intval($_GET['page']))): 1;
        $startIndex = ($currentPage - 1) * $articlesPerPage;
        $endIndex = min($startIndex + $articlesPerPage, $totalArticles);
        $_SESSION['displayedArticles'] = [];// Array to store article information
        // For non-logged-in users, display articles without comment forms
        for ($index = $startIndex; $index < $endIndex; $index++) {
            $article = $articles[$index];
            
            $_SESSION['displayedArticles'][$index] = $article;
            echo '<div class="news">';
            echo '<img src="' . $article['urlToImage'] . '" alt="' . $article['title'] . '" title="' . $article['title'] . '">';
            echo '<h2>' . $article['title'] . '</h2>';
            echo '<p>' . $article['description'] . '</p>';
            echo '<time datetime="' . $article['publishedAt'] . '">' . date('F j, Y, g:i a', strtotime($article['publishedAt'])) . '</time>';
            echo '<a href="index.php?menu=2&page=' . $currentPage . '&read_more=' . $index . '"> Read more</a>';
            echo '<hr>';
            echo '</div>';
        }

        if (isset($_GET['read_more'])) {
            $articleIndex = $_GET['read_more'];
            $article =  $_SESSION['displayedArticles'][$articleIndex];
            // Call the function to handle the redirection
            handleReadMoreClick($article);
        }

        

        // Pagination links
        echo '<div class="pagination" style="text-align: center;">';
        for ($page = 1; $page <= $totalPages; $page++) {
            if ($page == $currentPage) {
                echo '<span style="margin: 0 5px;">' . $page . '</span>';
            } else {
                echo '<a href="index.php?menu=2&page=' . $page . '" style="margin: 0 5px;">' . $page . '</a>';
            }
        }
        echo '</div>';
    } else {
        echo 'No news found.';
    }

    curl_close($ch); 
}


// Function to check if an article with the same ID exists in the database
function handleReadMoreClick($article)
{
    $articleId = $article['title'];
    $title = $article['title'];
    $description = $article['description'];
    $imageurl = $article['urlToImage'];
    $articleurl = $article['url'];
    $MySQL = mysqli_connect("localhost", "root", "", "projekt") or die('Error connecting to MySQL server.');
    
    // Check connection
    if ($MySQL->connect_error) {
        die("Connection failed: " . $MySQL->connect_error);
    }

    // SQL query to check if the article with the given ID exists
    $sql = "SELECT * FROM articles WHERE article_id = '" . $MySQL->real_escape_string($articleId) . "'";
    $result = $MySQL->query($sql);

    if (!$result) {
        die("Query failed: " . $MySQL->error);
    }
    
    // Check if any rows are returned
    if ($result->num_rows > 0) {
        // Article with the same ID exists in the database
        // Redirect the user to the article page
        echo '<script>window.location.href = "index.php?menu=2&article_id=' . urlencode($articleId) . '";</script>';
        exit();
    } else {
        echo 'Result: Tu sem';
        $insertSql = "INSERT INTO articles (article_id, title, description, imageurl, articleurl) 
              VALUES ('" . $MySQL->real_escape_string($articleId) . "', 
                      '" . $MySQL->real_escape_string($title) . "', 
                      '" . $MySQL->real_escape_string($description) . "', 
                      '" . $MySQL->real_escape_string($imageurl) . "', 
                      '" . $MySQL->real_escape_string($articleurl) . "')";
        
        if ($MySQL->query($insertSql) === TRUE) {
            echo '<script>window.location.href = "index.php?menu=2&article_id=' . urlencode($articleId) . '";</script>';
            //header("Location: index.php?menu=2&article_id=" . urlencode($articleId));
        } else {
            die("Error: " . $insertSql . "<br>" . $MySQL->error);
        }
    }

    // Close the database connection
    $MySQL->close();
}
?>