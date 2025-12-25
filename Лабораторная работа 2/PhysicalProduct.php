<?php

require_once 'AbstractProduct.php';

/**
 * Класс Штучный физический товар - наследник AbstractProduct
 * Стоимость зависит от количества штук
 */
class PhysicalProduct extends AbstractProduct
{
    /**
     * Конструктор класса
     */
    public function __construct($id, $name, $basePrice, $category = '', $description = '')
    {
        parent::__construct($id, $name, $basePrice, $category, $description);
    }

    /**
     * Подсчет финальной стоимости штучного товара
     * Стоимость = базовая цена * количество штук
     * 
     * @param int $quantity Количество штук
     * @return float Финальная стоимость
     */
    public function calculateFinalPrice($quantity)
    {
        if ($quantity <= 0) {
            return 0;
        }
        // Для штучного товара: цена за штуку * количество
        return $this->basePrice * $quantity;
    }

    /**
     * Получить тип товара
     */
    protected function getProductType()
    {
        return 'physical';
    }

    /**
     * Получить стоимость за одну штуку
     */
    public function getPricePerUnit()
    {
        return $this->basePrice;
    }
}

