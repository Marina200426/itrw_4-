<?php

require_once 'Product.php';

/**
 * Класс Корзина - управляет товарами, добавленными пользователем
 */
class Cart
{
    // Свойства (состояние класса)
    private $userId;
    private $items; // массив товаров [productId => quantity]
    private $createdAt;
    private $updatedAt;

    /**
     * Конструктор класса
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
        $this->items = [];
        $this->createdAt = date('Y-m-d H:i:s');
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    // Методы (поведение класса)

    /**
     * Получить ID пользователя
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Добавить товар в корзину
     */
    public function addItem($productId, $quantity = 1)
    {
        if ($quantity > 0) {
            if (isset($this->items[$productId])) {
                $this->items[$productId] += $quantity;
            } else {
                $this->items[$productId] = $quantity;
            }
            $this->updatedAt = date('Y-m-d H:i:s');
            return true;
        }
        return false;
    }

    /**
     * Удалить товар из корзины
     */
    public function removeItem($productId)
    {
        if (isset($this->items[$productId])) {
            unset($this->items[$productId]);
            $this->updatedAt = date('Y-m-d H:i:s');
            return true;
        }
        return false;
    }

    /**
     * Изменить количество товара в корзине
     */
    public function updateQuantity($productId, $quantity)
    {
        if ($quantity > 0) {
            if (isset($this->items[$productId])) {
                $this->items[$productId] = $quantity;
                $this->updatedAt = date('Y-m-d H:i:s');
                return true;
            }
        } elseif ($quantity == 0) {
            return $this->removeItem($productId);
        }
        return false;
    }

    /**
     * Получить количество товара в корзине
     */
    public function getItemQuantity($productId)
    {
        return isset($this->items[$productId]) ? $this->items[$productId] : 0;
    }

    /**
     * Получить все товары в корзине
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Получить количество различных товаров в корзине
     */
    public function getItemsCount()
    {
        return count($this->items);
    }

    /**
     * Получить общее количество товаров в корзине
     */
    public function getTotalQuantity()
    {
        $total = 0;
        foreach ($this->items as $quantity) {
            $total += $quantity;
        }
        return $total;
    }

    /**
     * Очистить корзину
     */
    public function clear()
    {
        $this->items = [];
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    /**
     * Проверить, пуста ли корзина
     */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * Получить дату создания корзины
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Получить дату последнего обновления корзины
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}

/**
 * Наследник класса Cart - Сохраненная корзина
 * Отличается возможностью сохранения и загрузки из базы данных
 */
class SavedCart extends Cart
{
    private $cartId;
    private $isSaved;
    private $saveDate;

    public function __construct($userId, $cartId = null)
    {
        parent::__construct($userId);
        $this->cartId = $cartId;
        $this->isSaved = false;
        $this->saveDate = null;
    }

    /**
     * Сохранить корзину
     */
    public function save()
    {
        $this->isSaved = true;
        $this->saveDate = date('Y-m-d H:i:s');
        // Здесь была бы логика сохранения в БД
        return true;
    }

    /**
     * Загрузить корзину
     */
    public function load()
    {
        // Здесь была бы логика загрузки из БД
        $this->isSaved = true;
        return true;
    }

    /**
     * Проверить, сохранена ли корзина
     */
    public function isSaved()
    {
        return $this->isSaved;
    }

    /**
     * Получить ID корзины
     */
    public function getCartId()
    {
        return $this->cartId;
    }

    /**
     * Получить дату сохранения
     */
    public function getSaveDate()
    {
        return $this->saveDate;
    }
}

