<?php

namespace App\Models;

use Model; // Предполагаем, что класс Model уже существует и используется

class User extends Model
{
    protected $model = 'https://jsonplaceholder.typicode.com/users';

    public function getUserById($userId)
    {
        $url = $this->model . '/' . $userId;

        // Инициализация cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Выполнение запроса
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true); // Возвращаем массив данных пользователя
    }
}