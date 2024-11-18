<?php
if (!isset($_SESSION['user'])) {
    echo "Please login to submit a comment.";
    exit();
}

$comment = $_POST['comment'];
$xml = new DOMDocument();
$xml->load('comments.xml');

$root = $xml->getElementsByTagName('comments')->item(0);
$newComment = $xml->createElement('comment');
$newComment->appendChild($xml->createTextNode($comment));

$root->appendChild($newComment);
$xml->save('comments.xml');

echo "Comment submitted successfully!";
?>
