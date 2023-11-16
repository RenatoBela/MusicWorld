<?php
$MySQL = mysqli_connect("localhost", "root", "", "projekt") or die('Error connecting to MySQL server.');
// Use the provided article ID to fetch and display the specific article content
$articleId = isset($_GET['article_id']) ? urldecode($_GET['article_id']) : '';

// Fetch article content based on $articleId from your data source (e.g., database)
$articleContent = fetchArticleContent($articleId);  // Implement this function

echo '<h1>' . $articleContent['title'] . '</h1>';
echo '<p>' . $articleContent['description'] . '</p>';
echo '<a href="' . $articleContent['articleurl'] . '">Read full article</a></br></br>';
echo '<img src="' . $articleContent['imageurl'] . '" alt="' . $articleContent['title'] . '" style="max-width: 500px;">';
// Add other relevant content and styling

include("comments.php");

function fetchArticleContent($articleId)
{
    $MySQL = mysqli_connect("localhost", "root", "", "projekt") or die('Error connecting to MySQL server.');
    // Sanitize the input to prevent SQL injection
    $articleId = $MySQL->real_escape_string($articleId);

    // Fetch article content from the database based on the provided article ID
    $sql = "SELECT title, description, imageurl, articleurl FROM articles WHERE article_id = '$articleId'";
    $result = $MySQL->query($sql);

    // Check if the query was successful
    if ($result) {
        // Fetch the article content
        $row = $result->fetch_assoc();

        // Return the article content as an associative array
        return [
            'title' => $row['title'],
            'description' => $row['description'],
            'imageurl' => $row['imageurl'],
            'articleurl' => $row['articleurl']
        ];
    } else {
        // Handle the case where the query failed
        die("Error fetching article content: " . $MySQL->error);
    }
    $MySQL->close();
}
?>
