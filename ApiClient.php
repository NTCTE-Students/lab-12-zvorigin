<?php

namespace App\Services;

use Exception;

/**
 * Класс ApiClient
 * 
 * Простой HTTP-клиент для выполнения API-запросов с использованием cURL.
 */
class ApiClient
{
    /**
     * @var string $baseUrl Базовый URL для API.
     */
    private string $baseUrl;

    /**
     * @var array $defaultOptions Стандартные параметры cURL.
     */
    private array $defaultOptions = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,  
    ];

    /**
     * @var array $headers HTTP-заголовки, которые будут отправлены с запросом.
     */
    private array $headers = [];

    /**
     * @var array $allowedMethods Разрешенные HTTP-методы.
     */
    private array $allowedMethods = [
        'POST', 'GET', 'PUT', 'DELETE', 'PATCH',
    ];

    /**
     * @var ?string $answer Ответ от API.
     */
    private ?string $answer = null;

    /**
     * Конструктор класса ApiClient.
     *
     * @param string $baseUrl Базовый URL для API.
     */
    public function __construct(string $baseUrl = '')
    {
        $this -> baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * Устанавливает базовый URL для API.
     *
     * @param string $baseUrl Базовый URL для API.
     * @return self
     */
    public function setBaseUrl(string $baseUrl): self
    {
        $this -> baseUrl = rtrim($baseUrl, '/');
        return $this;
    }

    /**
     * Устанавливает HTTP-заголовок для запроса.
     *
     * @param string $key Ключ заголовка.
     * @param string $value Значение заголовка.
     * @return self
     */
    public function setHeader(string $key, string $value): self
    {
        $this -> headers[] = "{$key}: {$value}";
        return $this;
    }

    /**
     * Выполняет HTTP-запрос.
     *
     * @param string $method HTTP-метод (POST, GET, PUT, DELETE, PATCH).
     * @param string $endpoint Конечная точка API.
     * @param ?array $data Данные для отправки в запросе (опционально).
     * @param array $params Параметры запроса (опционально).
     * @return self
     * @throws Exception Если HTTP-метод не разрешен.
     */
    public function request(string $method, string $endpoint, ?array $data = null, array $params = []): self
    {
        if (!in_array($method, $this -> allowedMethods)) {
            throw new Exception('Method is not allowed.');
        }

        $ch = curl_init($this -> buildUrl($endpoint, $params));

        foreach ($this -> defaultOptions as $key => $value) {
            curl_setopt($ch, $key, $value);
        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $this -> setHeader('Content-Type', 'application/json')
                -> setHeader('Accept', 'application/json');
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $this -> headers);

        $this -> answer = curl_exec($ch);
        curl_close($ch); 
        
        return $this;
    }

    /**
     * Возвращает необработанный ответ от API.
     *
     * @return string
     */
    public function getRaw(): string
    {
        return $this -> answer;
    }

    /**
     * Возвращает ответ от API в виде массива.
     *
     * @return array
     */
    public function getJson(): array
    {
        return json_decode($this -> answer, true);
    }

    /**
     * Строит полный URL для запроса.
     *
     * @param string $uri URI конечной точки API.
     * @param array $params Параметры запроса (опционально).
     * @return string
     */
    private function buildUrl(string $uri, array $params = []): string
    {
        $url = $this -> baseUrl ? "{$this -> baseUrl}/" . ltrim($uri, '/') : $uri;

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }
}