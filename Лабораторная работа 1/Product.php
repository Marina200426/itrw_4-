<?php

/**
 * Класс Продукт - базовый класс для товаров в интернет-магазине
 */
class Product
{
    // Свойства (состояние класса)
    protected $id;
    protected $name;
    protected $description;
    protected $price;
    protected $category;
    protected $stock;
    protected $imageUrl;
    protected $rating;
    protected $createdAt;

    /**
     * Конструктор класса
     */
    public function __construct($id, $name, $description, $price, $category, $stock = 0, $imageUrl = '', $rating = 0.0)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->category = $category;
        $this->stock = $stock;
        $this->imageUrl = $imageUrl;
        $this->rating = $rating;
        $this->createdAt = date('Y-m-d H:i:s');
    }

    // Методы (поведение класса)

    /**
     * Получить ID продукта
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Получить название продукта
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Установить название продукта
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Получить описание продукта
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Установить описание продукта
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Получить цену продукта
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Установить цену продукта
     */
    public function setPrice($price)
    {
        if ($price >= 0) {
            $this->price = $price;
        }
    }

    /**
     * Получить категорию продукта
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Получить количество на складе
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Установить количество на складе
     */
    public function setStock($stock)
    {
        $this->stock = $stock;
    }

    /**
     * Проверить наличие товара на складе
     */
    public function isInStock()
    {
        return $this->stock > 0;
    }

    /**
     * Уменьшить количество товара на складе
     */
    public function decreaseStock($quantity)
    {
        if ($this->stock >= $quantity) {
            $this->stock -= $quantity;
            return true;
        }
        return false;
    }

    /**
     * Получить рейтинг продукта
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Установить рейтинг продукта
     */
    public function setRating($rating)
    {
        if ($rating >= 0 && $rating <= 5) {
            $this->rating = $rating;
        }
    }

    /**
     * Получить информацию о продукте
     */
    public function getInfo()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category' => $this->category,
            'stock' => $this->stock,
            'rating' => $this->rating,
            'in_stock' => $this->isInStock()
        ];
    }
}

/**
 * Наследник класса Product - Электронный товар
 * Отличается наличием технических характеристик и гарантийного срока
 */
class ElectronicProduct extends Product
{
    private $warrantyPeriod; // гарантийный срок в месяцах
    private $specifications; // технические характеристики

    public function __construct($id, $name, $description, $price, $category, $stock, $warrantyPeriod, $specifications = [])
    {
        parent::__construct($id, $name, $description, $price, $category, $stock);
        $this->warrantyPeriod = $warrantyPeriod;
        $this->specifications = $specifications;
    }

    /**
     * Получить гарантийный срок
     */
    public function getWarrantyPeriod()
    {
        return $this->warrantyPeriod;
    }

    /**
     * Получить технические характеристики
     */
    public function getSpecifications()
    {
        return $this->specifications;
    }

    /**
     * Добавить техническую характеристику
     */
    public function addSpecification($key, $value)
    {
        $this->specifications[$key] = $value;
    }

    /**
     * Переопределенный метод получения информации
     */
    public function getInfo()
    {
        $info = parent::getInfo();
        $info['warranty_period'] = $this->warrantyPeriod;
        $info['specifications'] = $this->specifications;
        return $info;
    }
}

/**
 * Наследник класса Product - Одежда
 * Отличается размерами, цветами и материалом
 */
class ClothingProduct extends Product
{
    private $sizes; // доступные размеры
    private $colors; // доступные цвета
    private $material; // материал

    public function __construct($id, $name, $description, $price, $category, $stock, $sizes = [], $colors = [], $material = '')
    {
        parent::__construct($id, $name, $description, $price, $category, $stock);
        $this->sizes = $sizes;
        $this->colors = $colors;
        $this->material = $material;
    }

    /**
     * Получить доступные размеры
     */
    public function getSizes()
    {
        return $this->sizes;
    }

    /**
     * Получить доступные цвета
     */
    public function getColors()
    {
        return $this->colors;
    }

    /**
     * Получить материал
     */
    public function getMaterial()
    {
        return $this->material;
    }

    /**
     * Проверить наличие размера
     */
    public function hasSize($size)
    {
        return in_array($size, $this->sizes);
    }

    /**
     * Переопределенный метод получения информации
     */
    public function getInfo()
    {
        $info = parent::getInfo();
        $info['sizes'] = $this->sizes;
        $info['colors'] = $this->colors;
        $info['material'] = $this->material;
        return $info;
    }
}

