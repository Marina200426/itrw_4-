<?php

require_once 'AbstractProduct.php';

/**
 * Класс Цифровой товар - наследник AbstractProduct
 * Стоимость всегда в 2 раза меньше, чем у реального товара
 */
class DigitalProduct extends AbstractProduct
{
    /**
     * Конструктор класса
     */
    public function __construct($id, $name, $basePrice, $category = '', $description = '')
    {
        parent::__construct($id, $name, $basePrice, $category, $description);
    }

    /**
     * Подсчет финальной стоимости цифрового товара
     * Стоимость в 2 раза меньше базовой цены
     * 
     * @param float $quantity Количество товара (для цифрового товара обычно 1)
     * @return float Финальная стоимость
     */
    public function calculateFinalPrice($quantity = 1)
    {
        // Для цифрового товара количество обычно не влияет на цену за единицу
        // Но если покупают несколько копий, цена умножается на количество
        $digitalPrice = $this->basePrice / 2; // цена в 2 раза меньше
        return $digitalPrice * $quantity;
    }

    /**
     * Получить тип товара
     */
    protected function getProductType()
    {
        return 'digital';
    }

    /**
     * Получить цену за единицу цифрового товара
     */
    public function getDigitalPrice()
    {
        return $this->basePrice / 2;
    }
}

