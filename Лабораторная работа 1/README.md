# Лабораторная работа 1: Классы интернет-магазина

## Описание

Реализованы классы, описывающие сущности интернет-магазина с их свойствами, методами и наследниками.

## Структура классов

### 1. Product (Продукт)
**Свойства:**
- id, name, description, price, category, stock, imageUrl, rating, createdAt

**Методы:**
- Геттеры и сеттеры для всех свойств
- isInStock() - проверка наличия товара
- decreaseStock() - уменьшение количества на складе
- getInfo() - получение полной информации о продукте

**Наследники:**
- **ElectronicProduct** - электронный товар с гарантийным сроком и техническими характеристиками
- **ClothingProduct** - одежда с размерами, цветами и материалом

### 2. Cart (Корзина)
**Свойства:**
- userId, items (массив товаров), createdAt, updatedAt

**Методы:**
- addItem() - добавление товара
- removeItem() - удаление товара
- updateQuantity() - изменение количества
- getItemsCount() - количество различных товаров
- getTotalQuantity() - общее количество товаров
- clear() - очистка корзины
- isEmpty() - проверка на пустоту

**Наследники:**
- **SavedCart** - сохраненная корзина с возможностью сохранения/загрузки из БД

### 3. Review (Отзыв)
**Свойства:**
- id, productId, userId, rating, comment, createdAt, updatedAt, isVerified, likes, dislikes

**Методы:**
- Геттеры и сеттеры
- verify() - отметка как проверенного
- addLike() / addDislike() - управление лайками
- getInfo() - получение информации

**Наследники:**
- **DetailedReview** - детальный отзыв с фотографиями, преимуществами, недостатками и рекомендацией

### 4. User (Пользователь)
**Свойства:**
- id, email, password, firstName, lastName, phone, address, registrationDate, isActive, role

**Методы:**
- Геттеры и сеттеры
- verifyPassword() - проверка пароля
- getFullName() - получение полного имени
- activate() / deactivate() - управление активностью
- getInfo() - получение информации

**Наследники:**
- **Admin** - администратор с разрешениями и статистикой входов
- **VIPCustomer** - VIP клиент со скидками, бонусными баллами и уровнями VIP

### 5. ContactForm (Форма обратной связи)
**Свойства:**
- id, name, email, phone, subject, message, status, createdAt, updatedAt, userId, response

**Методы:**
- Геттеры и сеттеры
- setStatus() - установка статуса (new, in_progress, resolved, closed)
- validate() - валидация данных формы
- markAsResolved() / close() - управление статусом
- getInfo() - получение информации

**Наследники:**
- **ComplaintForm** - форма жалобы с категорией, приоритетом и связью с заказом

## Использование

Запустите файл `index.php` для просмотра примеров использования всех классов:

```bash
php index.php
```

## Файлы

- `Product.php` - классы Product, ElectronicProduct, ClothingProduct
- `Cart.php` - классы Cart, SavedCart
- `Review.php` - классы Review, DetailedReview
- `User.php` - классы User, Admin, VIPCustomer
- `ContactForm.php` - классы ContactForm, ComplaintForm
- `index.php` - примеры использования

