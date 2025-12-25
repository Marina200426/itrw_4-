<?php

/**
 * Абстрактный класс Товар - базовый класс для всех типов товаров
 */
abstract class AbstractProduct
{
    // Свойства (состояние класса)
    protected $id;
    protected $name;
    protected $basePrice; // базовая цена за единицу
    protected $category;
    protected $description;
    protected $totalRevenue; // общий доход с продаж

    /**
     * Конструктор класса
     */
    public function __construct($id, $name, $basePrice, $category = '', $description = '')
    {
        $this->id = $id;
        $this->name = $name;
        $this->basePrice = $basePrice;
        $this->category = $category;
        $this->description = $description;
        $this->totalRevenue = 0;
    }

    // Методы (поведение класса)

    /**
     * Получить ID товара
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Получить название товара
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Установить название товара
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Получить базовую цену
     */
    public function getBasePrice()
    {
        return $this->basePrice;
    }

    /**
     * Установить базовую цену
     */
    public function setBasePrice($basePrice)
    {
        if ($basePrice >= 0) {
            $this->basePrice = $basePrice;
        }
    }

    /**
     * Получить категорию
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Получить описание
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Получить общий доход с продаж
     */
    public function getTotalRevenue()
    {
        return $this->totalRevenue;
    }

    /**
     * Добавить доход от продажи
     */
    protected function addRevenue($amount)
    {
        $this->totalRevenue += $amount;
    }

    /**
     * Абстрактный метод подсчета финальной стоимости
     * Должен быть реализован в каждом наследнике
     * 
     * @param float $quantity Количество товара
     * @return float Финальная стоимость
     */
    abstract public function calculateFinalPrice($quantity);

    /**
     * Метод продажи товара
     * Вычисляет финальную стоимость и добавляет к доходу
     * 
     * @param float $quantity Количество товара
     * @return float Финальная стоимость продажи
     */
    public function sell($quantity)
    {
        $finalPrice = $this->calculateFinalPrice($quantity);
        $this->addRevenue($finalPrice);
        return $finalPrice;
    }

    /**
     * Получить информацию о товаре
     */
    public function getInfo()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'base_price' => $this->basePrice,
            'category' => $this->category,
            'description' => $this->description,
            'total_revenue' => $this->totalRevenue,
            'type' => $this->getProductType()
        ];
    }

    /**
     * Получить тип товара (для информации)
     */
    abstract protected function getProductType();
}

