<?php
$MySQL = mysqli_connect("localhost", "root", "", "projekt") or die('Error connecting to MySQL server.');
// Assuming $articleId is the unique identifier for the article (retrieve it from the URL or session)
$articleId = isset($_GET['article_id']) ? urldecode($_GET['article_id']) : '';

// Function to fetch comments for the given article from the database
function fetchComments($articleId) {
    global $MySQL;

    // Implement database query to retrieve comments for the article
    $query = "SELECT * FROM comments WHERE article_id = ?";
    $statement = $MySQL->prepare($query);
    $statement->bind_param("s", $articleId);
    $statement->execute();
    $result = $statement->get_result();
    $comments = $result->fetch_all(MYSQLI_ASSOC);

    return $comments;
}

// Function to add a new comment to the database
function addComment($articleId, $username, $commentText) {
    global $MySQL;

    // Implement database query to insert a new comment
    $query = "INSERT INTO comments (article_id, username, comment_text) VALUES (?, ?, ?)";
    $statement = $MySQL->prepare($query);
    $statement->bind_param("sss", $articleId, $username, $commentText);

    // Execute the statement and handle errors
    if (!$statement->execute()) {
        echo "Error adding comment: " . $statement->error;
    } else {
        // Redirect to the same page after adding the comment
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    }
}

// Check if the user is logged in
$userLoggedIn = isset($_SESSION['user']['valid']) && $_SESSION['user']['valid'] === 'true';

// Handle adding a new comment if the form is submitted
if ($userLoggedIn && isset($_POST['submit_comment'])) {
    $username = $_SESSION['user']['username']; // Replace with the actual username of the logged-in user
    $commentText = $_POST['comment_text'];
    addComment($articleId, $username, $commentText);
}

// Fetch existing comments for the article
$comments = fetchComments($articleId);

// Display existing comments
echo '<div class="comments-container">';
echo '<h2>Comments</h2>';
echo '<ul class="comments-list">';
foreach ($comments as $comment) {
    echo '<li class="comment">';
    echo '<div class="comment-header"><strong>' . $comment['username'] . ':</strong></div>';
    echo '<div class="comment-text">' . $comment['comment_text'] . '</div>';
    echo '</li>';
}
echo '</ul>';
echo '</div>';

// Display comment form if the user is logged in
if ($userLoggedIn) {
    echo '
    <h2>Add a Comment</h2>
    <form method="post" action="">
        <label for="comment_text">Your Comment:</label>
        <textarea name="comment_text" rows="4" cols="50" required></textarea>
        <br>
        <input type="submit" name="submit_comment" value="Submit Comment">
    </form>';
} else {
    echo '<p>Login to leave a comment.</p>';
}

$MySQL->close();
?>
<style>
    /* Add this CSS to your stylesheet or within a <style> tag in your HTML file */

    .comments-container {
    background-color: lightgray;
    border-radius: 10px;
    padding: 15px;
    margin-top: 20px;
    width: 95%;
}

.comments-list {
    background-color: lightgray;
    list-style-type: none;
    padding: 0;
    margin-left: auto;
    margin-right: auto;
    text-align: left; /* Center the text inside the container */
}

.comment {
    margin-bottom: 15px;
    clear: both;
    padding: 5px;
    border-radius: 5px;
    background-color: #ffffff;
    width: 90%;
    border: 2px solid black;
}

.comment-header {
    font-weight: bold;
    background-color: #ffffff;
    margin-bottom: 5px;
}

.comment-text {
    background-color: #ffffff;
    padding: 10px;
}
</style>
