<?php
session_start();
if (isset($_GET['index']) && isset($_SESSION['articles'])) {
    $index = $_GET['index'];
    if (isset($_SESSION['articles'][$index])) {
        $article = $_SESSION['articles'][$index];
        // Display the full article content
        echo '<h1>' . $article['title'] . '</h1>';
        echo '<p>' . $article['content'] . '</p>';
        echo '<a href="' . $article['url'] . '">Read the original article</a>';
    } else {
        echo 'Article not found.';
    }
} else {
    echo isset($_SESSION['articles']);
    echo 'No article selected.';
}
?>