<?php

require_once 'Product.php';

/**
 * Класс Отзыв - представляет отзыв о продукте
 */
class Review
{
    // Свойства (состояние класса)
    private $id;
    private $productId;
    private $userId;
    private $rating; // оценка от 1 до 5
    private $comment;
    private $createdAt;
    private $updatedAt;
    private $isVerified; // проверен ли отзыв (например, покупка подтверждена)
    private $likes;
    private $dislikes;

    /**
     * Конструктор класса
     */
    public function __construct($id, $productId, $userId, $rating, $comment = '', $isVerified = false)
    {
        $this->id = $id;
        $this->productId = $productId;
        $this->userId = $userId;
        $this->setRating($rating);
        $this->comment = $comment;
        $this->isVerified = $isVerified;
        $this->likes = 0;
        $this->dislikes = 0;
        $this->createdAt = date('Y-m-d H:i:s');
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    // Методы (поведение класса)

    /**
     * Получить ID отзыва
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Получить ID продукта
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Получить ID пользователя
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Получить оценку
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Установить оценку
     */
    public function setRating($rating)
    {
        if ($rating >= 1 && $rating <= 5) {
            $this->rating = $rating;
            $this->updatedAt = date('Y-m-d H:i:s');
        }
    }

    /**
     * Получить комментарий
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Установить комментарий
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    /**
     * Проверить, является ли отзыв проверенным
     */
    public function isVerified()
    {
        return $this->isVerified;
    }

    /**
     * Отметить отзыв как проверенный
     */
    public function verify()
    {
        $this->isVerified = true;
    }

    /**
     * Получить количество лайков
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * Получить количество дизлайков
     */
    public function getDislikes()
    {
        return $this->dislikes;
    }

    /**
     * Добавить лайк
     */
    public function addLike()
    {
        $this->likes++;
    }

    /**
     * Добавить дизлайк
     */
    public function addDislike()
    {
        $this->dislikes++;
    }

    /**
     * Получить дату создания
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Получить дату обновления
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Получить информацию об отзыве
     */
    public function getInfo()
    {
        return [
            'id' => $this->id,
            'product_id' => $this->productId,
            'user_id' => $this->userId,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'is_verified' => $this->isVerified,
            'likes' => $this->likes,
            'dislikes' => $this->dislikes,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}

/**
 * Наследник класса Review - Детальный отзыв
 * Отличается наличием фотографий и дополнительных характеристик
 */
class DetailedReview extends Review
{
    private $photos; // массив URL фотографий
    private $pros; // преимущества товара
    private $cons; // недостатки товара
    private $recommend; // рекомендует ли товар

    public function __construct($id, $productId, $userId, $rating, $comment = '', $isVerified = false)
    {
        parent::__construct($id, $productId, $userId, $rating, $comment, $isVerified);
        $this->photos = [];
        $this->pros = [];
        $this->cons = [];
        $this->recommend = false;
    }

    /**
     * Добавить фотографию
     */
    public function addPhoto($photoUrl)
    {
        $this->photos[] = $photoUrl;
    }

    /**
     * Получить фотографии
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * Добавить преимущество
     */
    public function addPro($pro)
    {
        $this->pros[] = $pro;
    }

    /**
     * Получить преимущества
     */
    public function getPros()
    {
        return $this->pros;
    }

    /**
     * Добавить недостаток
     */
    public function addCon($con)
    {
        $this->cons[] = $con;
    }

    /**
     * Получить недостатки
     */
    public function getCons()
    {
        return $this->cons;
    }

    /**
     * Установить рекомендацию
     */
    public function setRecommend($recommend)
    {
        $this->recommend = $recommend;
    }

    /**
     * Получить рекомендацию
     */
    public function getRecommend()
    {
        return $this->recommend;
    }

    /**
     * Переопределенный метод получения информации
     */
    public function getInfo()
    {
        $info = parent::getInfo();
        $info['photos'] = $this->photos;
        $info['pros'] = $this->pros;
        $info['cons'] = $this->cons;
        $info['recommend'] = $this->recommend;
        return $info;
    }
}

