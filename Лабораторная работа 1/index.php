<?php
/**
 * Пример использования классов интернет-магазина
 */

require_once 'Product.php';
require_once 'Cart.php';
require_once 'Review.php';
require_once 'User.php';
require_once 'ContactForm.php';

// Устанавливаем заголовок для HTML
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторная работа 1: Классы интернет-магазина</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Лабораторная работа 1</h1>
            <p>Классы интернет-магазина</p>
        </div>
        
        <div class="content">
            <?php
            // Пример работы с классом Product
            $product = new Product(1, 'Смартфон', 'Современный смартфон с отличной камерой', 25000, 'Электроника', 10, 'phone.jpg', 4.5);
            ?>
            <div class="section">
                <h2 class="section-title">1. Работа с классом Product</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">ID</div>
                        <div class="info-value"><?= htmlspecialchars($product->getId()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Название</div>
                        <div class="info-value"><?= htmlspecialchars($product->getName()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Описание</div>
                        <div class="info-value"><?= htmlspecialchars($product->getDescription()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Цена</div>
                        <div class="info-value price"><?= number_format($product->getPrice(), 0, ',', ' ') ?> руб.</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Категория</div>
                        <div class="info-value"><?= htmlspecialchars($product->getCategory()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">На складе</div>
                        <div class="info-value"><?= $product->getStock() ?> шт.</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">В наличии</div>
                        <div class="info-value">
                            <?php if ($product->isInStock()): ?>
                                <span class="badge badge-success">Да</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Нет</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Рейтинг</div>
                        <div class="info-value">
                            <span class="rating">
                                <span class="stars"><?= str_repeat('★', floor($product->getRating())) ?><?= str_repeat('☆', 5 - floor($product->getRating())) ?></span>
                                <?= $product->getRating() ?>/5
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            // Пример работы с наследником ElectronicProduct
            $electronic = new ElectronicProduct(2, 'Ноутбук', 'Игровой ноутбук', 75000, 'Электроника', 5, 24, [
                'processor' => 'Intel i7',
                'ram' => '16 GB',
                'storage' => '512 GB SSD'
            ]);
            $electronic->addSpecification('graphics', 'NVIDIA RTX 3060');
            ?>
            <div class="section">
                <h2 class="section-title">2. Работа с классом ElectronicProduct (наследник Product)</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">ID</div>
                        <div class="info-value"><?= htmlspecialchars($electronic->getId()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Название</div>
                        <div class="info-value"><?= htmlspecialchars($electronic->getName()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Цена</div>
                        <div class="info-value price"><?= number_format($electronic->getPrice(), 0, ',', ' ') ?> руб.</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Гарантия</div>
                        <div class="info-value"><?= $electronic->getWarrantyPeriod() ?> месяцев</div>
                    </div>
                </div>
                <div class="specs-list">
                    <h4>Технические характеристики</h4>
                    <?php foreach ($electronic->getSpecifications() as $key => $value): ?>
                        <div class="spec-item">
                            <span class="spec-key"><?= htmlspecialchars(ucfirst($key)) ?></span>
                            <span class="spec-value"><?= htmlspecialchars($value) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php
            // Пример работы с классом Cart
            $cart = new Cart(1);
            $cart->addItem(1, 2);
            $cart->addItem(2, 1);
            ?>
            <div class="section">
                <h2 class="section-title">3. Работа с классом Cart</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">ID пользователя</div>
                        <div class="info-value"><?= $cart->getUserId() ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Различных товаров</div>
                        <div class="info-value"><?= $cart->getItemsCount() ?> шт.</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Общее количество</div>
                        <div class="info-value"><?= $cart->getTotalQuantity() ?> шт.</div>
                    </div>
                </div>
                <div class="cart-items">
                    <h4>Товары в корзине</h4>
                    <?php foreach ($cart->getItems() as $productId => $quantity): ?>
                        <div class="cart-item">
                            <span>Товар ID #<?= $productId ?></span>
                            <span class="badge badge-info"><?= $quantity ?> шт.</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php
            // Пример работы с классом Review
            $review = new Review(1, 1, 1, 5, 'Отличный товар! Рекомендую!', true);
            $review->addLike();
            $review->addLike();
            ?>
            <div class="section">
                <h2 class="section-title">4. Работа с классом Review</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">ID отзыва</div>
                        <div class="info-value"><?= $review->getId() ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">ID продукта</div>
                        <div class="info-value"><?= $review->getProductId() ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">ID пользователя</div>
                        <div class="info-value"><?= $review->getUserId() ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Оценка</div>
                        <div class="info-value">
                            <span class="rating">
                                <span class="stars"><?= str_repeat('★', $review->getRating()) ?></span>
                                <?= $review->getRating() ?>/5
                            </span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Комментарий</div>
                        <div class="info-value"><?= htmlspecialchars($review->getComment()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Проверен</div>
                        <div class="info-value">
                            <?php if ($review->isVerified()): ?>
                                <span class="badge badge-success">Да</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Нет</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Лайков</div>
                        <div class="info-value">
                            <span class="badge badge-success">👍 <?= $review->getLikes() ?></span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Дизлайков</div>
                        <div class="info-value">
                            <span class="badge badge-danger">👎 <?= $review->getDislikes() ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            // Пример работы с классом User
            $user = new User(1, 'user@example.com', 'password123', 'Иван', 'Иванов', '+79001234567', 'Москва, ул. Примерная, 1');
            ?>
            <div class="section">
                <h2 class="section-title">5. Работа с классом User</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">ID</div>
                        <div class="info-value"><?= $user->getId() ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= htmlspecialchars($user->getEmail()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Полное имя</div>
                        <div class="info-value"><?= htmlspecialchars($user->getFullName()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Имя</div>
                        <div class="info-value"><?= htmlspecialchars($user->getFirstName()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Фамилия</div>
                        <div class="info-value"><?= htmlspecialchars($user->getLastName()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Телефон</div>
                        <div class="info-value"><?= htmlspecialchars($user->getPhone()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Адрес</div>
                        <div class="info-value"><?= htmlspecialchars($user->getAddress()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Роль</div>
                        <div class="info-value">
                            <span class="badge badge-info"><?= htmlspecialchars($user->getRole()) ?></span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Активен</div>
                        <div class="info-value">
                            <?php if ($user->isActive()): ?>
                                <span class="badge badge-success">Да</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Нет</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Дата регистрации</div>
                        <div class="info-value"><?= htmlspecialchars($user->getRegistrationDate()) ?></div>
                    </div>
                </div>
            </div>

            <?php
            // Пример работы с классом VIPCustomer
            $vip = new VIPCustomer(2, 'vip@example.com', 'password123', 'Петр', 'Петров', 'gold');
            $vip->addPurchase(50000);
            ?>
            <div class="section">
                <h2 class="section-title">6. Работа с классом VIPCustomer (наследник User)</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">ID</div>
                        <div class="info-value"><?= $vip->getId() ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= htmlspecialchars($vip->getEmail()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Полное имя</div>
                        <div class="info-value"><?= htmlspecialchars($vip->getFullName()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Уровень VIP</div>
                        <div class="info-value">
                            <span class="badge badge-vip"><?= strtoupper($vip->getVipLevel()) ?></span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Скидка</div>
                        <div class="info-value">
                            <span class="badge badge-success"><?= $vip->getDiscount() ?>%</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Бонусные баллы</div>
                        <div class="info-value price"><?= number_format($vip->getBonusPoints(), 0, ',', ' ') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Общая сумма покупок</div>
                        <div class="info-value price"><?= number_format($vip->getTotalPurchases(), 0, ',', ' ') ?> руб.</div>
                    </div>
                </div>
            </div>

            <?php
            // Пример работы с классом ContactForm
            $contactForm = new ContactForm(1, 'Анна', 'anna@example.com', 'Вопрос о доставке', 'Когда будет доставка?', '+79007654321');
            $errors = $contactForm->validate();
            ?>
            <div class="section">
                <h2 class="section-title">7. Работа с классом ContactForm</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">ID</div>
                        <div class="info-value"><?= $contactForm->getId() ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Имя</div>
                        <div class="info-value"><?= htmlspecialchars($contactForm->getName()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= htmlspecialchars($contactForm->getEmail()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Телефон</div>
                        <div class="info-value"><?= htmlspecialchars($contactForm->getPhone()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Тема</div>
                        <div class="info-value"><?= htmlspecialchars($contactForm->getSubject()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Сообщение</div>
                        <div class="info-value"><?= htmlspecialchars($contactForm->getMessage()) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Статус</div>
                        <div class="info-value">
                            <span class="badge badge-info"><?= htmlspecialchars($contactForm->getStatus()) ?></span>
                        </div>
                    </div>
                </div>
                <?php if (empty($errors)): ?>
                    <div class="validation-success">
                        ✓ Форма валидна
                    </div>
                <?php else: ?>
                    <div class="validation-errors">
                        <h4>Ошибки валидации:</h4>
                        <?php foreach ($errors as $error): ?>
                            <div class="error-item"><?= htmlspecialchars($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="footer">
            <p>Конец примера</p>
        </div>
    </div>
</body>
</html>
