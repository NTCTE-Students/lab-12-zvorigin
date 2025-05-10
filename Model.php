<?php

namespace App\Base;

use App\Services\ApiClient;

/**
 * Абстрактный класс Model
 * 
 * Этот класс предоставляет базовые методы для работы с API.
 */
abstract class Model
{
    /**
     * @var string $model Название модели.
     */
    protected string $model;

    /**
     * @var array|null $data Данные, полученные из API.
     */
    protected ?array $data = null;

    /**
     * @var ApiClient $apiClient Экземпляр клиента для работы с API.
     */
    protected ApiClient $apiClient;

    /**
     * Конструктор класса.
     * 
     * Инициализирует экземпляр ApiClient с базовым URL.
     */
    public function __construct()
    {
        $this -> apiClient = new ApiClient('https://jsonplaceholder.typicode.com');
    }

    /**
     * Поиск данных по идентификатору.
     * 
     * @param int $id Идентификатор для поиска.
     * @return self Возвращает текущий экземпляр класса.
     */
    public function searchById(int $id): self
    {
        $this -> data = $this
            -> apiClient
            -> request('GET', "/{$this -> model}/{$id}")
            -> getJson();

        return $this;
    }

    /**
     * Поиск всех данных.
     * 
     * @return self Возвращает текущий экземпляр класса.
     */
    public function searchAll(): self
    {
        $this -> data =  $this
            -> apiClient
            -> request('GET', "/{$this -> model}")
            -> getJson();

        return $this;
    }

    /**
     * Получение данных.
     * 
     * @param int|null $chunk Размер чанка для разбиения данных.
     * @param int|null $page Номер страницы для получения данных.
     * @return array|null Возвращает массив данных или null.
     */
    public function getData(?int $chunk = null, ?int $page = null): ?array
    {
        if ($chunk !== null && $page !== null) {
            return array_chunk($this -> data, $chunk)[$page];
        }

        return $this -> data;
    }
}