<?php

require_once 'AbstractProduct.php';

/**
 * Класс Товар на вес - наследник AbstractProduct
 * Стоимость зависит от продаваемого количества в килограммах
 */
class WeightProduct extends AbstractProduct
{
    /**
     * Конструктор класса
     * 
     * @param float $basePrice Базовая цена за 1 килограмм
     */
    public function __construct($id, $name, $basePrice, $category = '', $description = '')
    {
        parent::__construct($id, $name, $basePrice, $category, $description);
    }

    /**
     * Подсчет финальной стоимости весового товара
     * Стоимость = базовая цена за кг * количество килограмм
     * 
     * @param float $quantity Количество в килограммах
     * @return float Финальная стоимость
     */
    public function calculateFinalPrice($quantity)
    {
        if ($quantity <= 0) {
            return 0;
        }
        // Для весового товара: цена за кг * количество кг
        return $this->basePrice * $quantity;
    }

    /**
     * Получить тип товара
     */
    protected function getProductType()
    {
        return 'weight';
    }

    /**
     * Получить стоимость за 1 килограмм
     */
    public function getPricePerKilogram()
    {
        return $this->basePrice;
    }

    /**
     * Переопределенный метод получения информации
     */
    public function getInfo()
    {
        $info = parent::getInfo();
        $info['price_per_kg'] = $this->basePrice;
        return $info;
    }
}

