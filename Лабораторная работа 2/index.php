<?php
/**
 * Пример использования классов товарной номенклатуры
 */

require_once 'AbstractProduct.php';
require_once 'DigitalProduct.php';
require_once 'PhysicalProduct.php';
require_once 'WeightProduct.php';

// Устанавливаем заголовок для HTML
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторная работа 2: Структура классов товарной номенклатуры</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Лабораторная работа 2</h1>
            <p>Структура классов товарной номенклатуры</p>
        </div>
        
        <div class="content">
            <?php
            // Пример работы с цифровым товаром
            $digitalProduct = new DigitalProduct(1, 'Электронная книга', 1000, 'Цифровые товары', 'Книга в формате PDF');
            $quantity1 = 1;
            $quantity2 = 3;
            $finalPrice1 = $digitalProduct->calculateFinalPrice($quantity1);
            $finalPrice2 = $digitalProduct->calculateFinalPrice($quantity2);
            $revenue1 = $digitalProduct->sell($quantity1);
            ?>
            <div class="section">
                <h2 class="section-title">1. Цифровой товар (DigitalProduct)</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">ID</div>
                        <div class="info-value"><?= htmlspecialchars($digitalProduct->getId()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Название</div>
                        <div class="info-value"><?= htmlspecialchars($digitalProduct->getName()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Категория</div>
                        <div class="info-value"><?= htmlspecialchars($digitalProduct->getCategory()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Описание</div>
                        <div class="info-value"><?= htmlspecialchars($digitalProduct->getDescription()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Базовая цена</div>
                        <div class="info-value"><?= number_format($digitalProduct->getBasePrice(), 0, ',', ' ') ?> руб.</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Цена цифрового товара</div>
                        <div class="info-value price"><?= number_format($digitalProduct->getDigitalPrice(), 0, ',', ' ') ?> руб.</div>
                    </div>
                </div>
                
                <div class="calculations">
                    <h4>Расчет стоимости</h4>
                    <div class="calculation-item">
                        <div class="calculation-label">За <?= $quantity1 ?> шт.</div>
                        <div class="calculation-formula">(<?= $digitalProduct->getBasePrice() ?> / 2) × <?= $quantity1 ?></div>
                        <div class="calculation-result">= <?= number_format($finalPrice1, 2, ',', ' ') ?> руб.</div>
                    </div>
                    <div class="calculation-item">
                        <div class="calculation-label">За <?= $quantity2 ?> шт.</div>
                        <div class="calculation-formula">(<?= $digitalProduct->getBasePrice() ?> / 2) × <?= $quantity2 ?></div>
                        <div class="calculation-result">= <?= number_format($finalPrice2, 2, ',', ' ') ?> руб.</div>
                    </div>
                </div>
                
                <div class="sales-section">
                    <h4>Продажи</h4>
                    <div class="sale-item">
                        <span>Продано <?= $quantity1 ?> шт.</span>
                        <span class="price"><?= number_format($revenue1, 2, ',', ' ') ?> руб.</span>
                    </div>
                    <div class="revenue-total">
                        Общий доход: <?= number_format($digitalProduct->getTotalRevenue(), 2, ',', ' ') ?> руб.
                    </div>
                </div>
            </div>

            <?php
            // Пример работы со штучным физическим товаром
            $physicalProduct = new PhysicalProduct(2, 'Смартфон', 25000, 'Электроника', 'Современный смартфон');
            $quantity3 = 2;
            $quantity4 = 5;
            $finalPrice3 = $physicalProduct->calculateFinalPrice($quantity3);
            $finalPrice4 = $physicalProduct->calculateFinalPrice($quantity4);
            $revenue2 = $physicalProduct->sell($quantity3);
            $revenue3 = $physicalProduct->sell($quantity4);
            ?>
            <div class="section">
                <h2 class="section-title">2. Штучный физический товар (PhysicalProduct)</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">ID</div>
                        <div class="info-value"><?= htmlspecialchars($physicalProduct->getId()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Название</div>
                        <div class="info-value"><?= htmlspecialchars($physicalProduct->getName()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Категория</div>
                        <div class="info-value"><?= htmlspecialchars($physicalProduct->getCategory()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Описание</div>
                        <div class="info-value"><?= htmlspecialchars($physicalProduct->getDescription()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Цена за штуку</div>
                        <div class="info-value price"><?= number_format($physicalProduct->getPricePerUnit(), 0, ',', ' ') ?> руб.</div>
                    </div>
                </div>
                
                <div class="calculations">
                    <h4>Расчет стоимости</h4>
                    <div class="calculation-item">
                        <div class="calculation-label">За <?= $quantity3 ?> шт.</div>
                        <div class="calculation-formula"><?= $physicalProduct->getPricePerUnit() ?> × <?= $quantity3 ?></div>
                        <div class="calculation-result">= <?= number_format($finalPrice3, 2, ',', ' ') ?> руб.</div>
                    </div>
                    <div class="calculation-item">
                        <div class="calculation-label">За <?= $quantity4 ?> шт.</div>
                        <div class="calculation-formula"><?= $physicalProduct->getPricePerUnit() ?> × <?= $quantity4 ?></div>
                        <div class="calculation-result">= <?= number_format($finalPrice4, 2, ',', ' ') ?> руб.</div>
                    </div>
                </div>
                
                <div class="sales-section">
                    <h4>Продажи</h4>
                    <div class="sale-item">
                        <span>Продано <?= $quantity3 ?> шт.</span>
                        <span class="price"><?= number_format($revenue2, 2, ',', ' ') ?> руб.</span>
                    </div>
                    <div class="sale-item">
                        <span>Продано <?= $quantity4 ?> шт.</span>
                        <span class="price"><?= number_format($revenue3, 2, ',', ' ') ?> руб.</span>
                    </div>
                    <div class="revenue-total">
                        Общий доход: <?= number_format($physicalProduct->getTotalRevenue(), 2, ',', ' ') ?> руб.
                    </div>
                </div>
            </div>

            <?php
            // Пример работы с товаром на вес
            $weightProduct = new WeightProduct(3, 'Яблоки', 150, 'Продукты', 'Свежие яблоки');
            $weight1 = 2.5;
            $weight2 = 0.5;
            $finalPrice5 = $weightProduct->calculateFinalPrice($weight1);
            $finalPrice6 = $weightProduct->calculateFinalPrice($weight2);
            $revenue4 = $weightProduct->sell($weight1);
            $revenue5 = $weightProduct->sell($weight2);
            ?>
            <div class="section">
                <h2 class="section-title">3. Товар на вес (WeightProduct)</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">ID</div>
                        <div class="info-value"><?= htmlspecialchars($weightProduct->getId()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Название</div>
                        <div class="info-value"><?= htmlspecialchars($weightProduct->getName()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Категория</div>
                        <div class="info-value"><?= htmlspecialchars($weightProduct->getCategory()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Описание</div>
                        <div class="info-value"><?= htmlspecialchars($weightProduct->getDescription()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Цена за 1 кг</div>
                        <div class="info-value price"><?= number_format($weightProduct->getPricePerKilogram(), 0, ',', ' ') ?> руб.</div>
                    </div>
                </div>
                
                <div class="calculations">
                    <h4>Расчет стоимости</h4>
                    <div class="calculation-item">
                        <div class="calculation-label">За <?= $weight1 ?> кг</div>
                        <div class="calculation-formula"><?= $weightProduct->getPricePerKilogram() ?> × <?= $weight1 ?></div>
                        <div class="calculation-result">= <?= number_format($finalPrice5, 2, ',', ' ') ?> руб.</div>
                    </div>
                    <div class="calculation-item">
                        <div class="calculation-label">За <?= $weight2 ?> кг</div>
                        <div class="calculation-formula"><?= $weightProduct->getPricePerKilogram() ?> × <?= $weight2 ?></div>
                        <div class="calculation-result">= <?= number_format($finalPrice6, 2, ',', ' ') ?> руб.</div>
                    </div>
                </div>
                
                <div class="sales-section">
                    <h4>Продажи</h4>
                    <div class="sale-item">
                        <span>Продано <?= $weight1 ?> кг</span>
                        <span class="price"><?= number_format($revenue4, 2, ',', ' ') ?> руб.</span>
                    </div>
                    <div class="sale-item">
                        <span>Продано <?= $weight2 ?> кг</span>
                        <span class="price"><?= number_format($revenue5, 2, ',', ' ') ?> руб.</span>
                    </div>
                    <div class="revenue-total">
                        Общий доход: <?= number_format($weightProduct->getTotalRevenue(), 2, ',', ' ') ?> руб.
                    </div>
                </div>
            </div>

            <?php
            // Сравнение всех типов товаров
            $basePrice = 1000;
            $digital = new DigitalProduct(4, 'Цифровой товар', $basePrice);
            $physical = new PhysicalProduct(5, 'Физический товар', $basePrice);
            $weight = new WeightProduct(6, 'Весовой товар', $basePrice);
            $testQuantity = 2;
            $digitalPrice = $digital->calculateFinalPrice($testQuantity);
            $physicalPrice = $physical->calculateFinalPrice($testQuantity);
            $weightPrice = $weight->calculateFinalPrice($testQuantity);
            ?>
            <div class="section">
                <h2 class="section-title">4. Сравнение всех типов товаров (базовая цена <?= number_format($basePrice, 0, ',', ' ') ?> руб.)</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Базовая цена</div>
                        <div class="info-value price"><?= number_format($basePrice, 0, ',', ' ') ?> руб.</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Тестовое количество</div>
                        <div class="info-value"><?= $testQuantity ?></div>
                    </div>
                </div>
                
                <div class="comparison-grid">
                    <div class="comparison-card">
                        <h5>Цифровой товар</h5>
                        <div class="comparison-price"><?= number_format($digitalPrice, 2, ',', ' ') ?> руб.</div>
                        <div class="comparison-formula">(базовая цена / 2) × количество</div>
                    </div>
                    <div class="comparison-card">
                        <h5>Физический товар</h5>
                        <div class="comparison-price"><?= number_format($physicalPrice, 2, ',', ' ') ?> руб.</div>
                        <div class="comparison-formula">базовая цена × количество</div>
                    </div>
                    <div class="comparison-card">
                        <h5>Весовой товар (2 кг)</h5>
                        <div class="comparison-price"><?= number_format($weightPrice, 2, ',', ' ') ?> руб.</div>
                        <div class="comparison-formula">цена за кг × количество кг</div>
                    </div>
                </div>
            </div>

            <?php
            // Демонстрация формирования дохода с продаж
            $digital->sell(1);
            $digital->sell(2);
            $physical->sell(1);
            $physical->sell(3);
            $weight->sell(1.5);
            $weight->sell(2.3);
            ?>
            <div class="section">
                <h2 class="section-title">5. Формирование дохода с продаж</h2>
                
                <div class="sales-section">
                    <h4>Цифровой товар</h4>
                    <div class="sale-item">
                        <span>Продано 1 шт.</span>
                        <span class="price"><?= number_format($digital->calculateFinalPrice(1), 2, ',', ' ') ?> руб.</span>
                    </div>
                    <div class="sale-item">
                        <span>Продано 2 шт.</span>
                        <span class="price"><?= number_format($digital->calculateFinalPrice(2), 2, ',', ' ') ?> руб.</span>
                    </div>
                    <div class="revenue-total">
                        Общий доход: <?= number_format($digital->getTotalRevenue(), 2, ',', ' ') ?> руб.
                    </div>
                </div>
                
                <div class="sales-section">
                    <h4>Физический товар</h4>
                    <div class="sale-item">
                        <span>Продано 1 шт.</span>
                        <span class="price"><?= number_format($physical->calculateFinalPrice(1), 2, ',', ' ') ?> руб.</span>
                    </div>
                    <div class="sale-item">
                        <span>Продано 3 шт.</span>
                        <span class="price"><?= number_format($physical->calculateFinalPrice(3), 2, ',', ' ') ?> руб.</span>
                    </div>
                    <div class="revenue-total">
                        Общий доход: <?= number_format($physical->getTotalRevenue(), 2, ',', ' ') ?> руб.
                    </div>
                </div>
                
                <div class="sales-section">
                    <h4>Весовой товар</h4>
                    <div class="sale-item">
                        <span>Продано 1.5 кг</span>
                        <span class="price"><?= number_format($weight->calculateFinalPrice(1.5), 2, ',', ' ') ?> руб.</span>
                    </div>
                    <div class="sale-item">
                        <span>Продано 2.3 кг</span>
                        <span class="price"><?= number_format($weight->calculateFinalPrice(2.3), 2, ',', ' ') ?> руб.</span>
                    </div>
                    <div class="revenue-total">
                        Общий доход: <?= number_format($weight->getTotalRevenue(), 2, ',', ' ') ?> руб.
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>Конец примера</p>
        </div>
    </div>
</body>
</html>
