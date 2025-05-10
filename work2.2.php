<?php

require_once 'App/Models/User.php';

// Получаем пост по ID
$postId = $_GET['id']; // Предполагаем, что ID поста передается через параметр запроса
$postUrl = "https://jsonplaceholder.typicode.com/posts/{$postId}";

$ch = curl_init($postUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$postResponse = curl_exec($ch);
curl_close($ch);

$post = json_decode($postResponse, true);

// Проверка, успешно ли получен пост
if ($post) {
    echo "<h1>{$post['title']}</h1>";
    echo "<p>{$post['body']}</p>";

    // Получаем пользователя
    $userModel = new App\Models\User();
    $user = $userModel->getUserById($post['userId']);

    // Проверка, успешно ли получен пользователь
    if ($user) {
        echo "<h3>Автор: {$user['name']}</h3>";
        echo "<p>Email: {$user['email']}</p>";
    }
} else {
    echo "Пост не найден.";
}