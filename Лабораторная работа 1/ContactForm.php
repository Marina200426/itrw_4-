<?php

/**
 * Класс Форма обратной связи - обрабатывает сообщения от пользователей
 */
class ContactForm
{
    // Свойства (состояние класса)
    private $id;
    private $name;
    private $email;
    private $phone;
    private $subject;
    private $message;
    private $status; // new, in_progress, resolved, closed
    private $createdAt;
    private $updatedAt;
    private $userId; // ID пользователя, если он авторизован
    private $response; // ответ администратора

    /**
     * Конструктор класса
     */
    public function __construct($id, $name, $email, $subject, $message, $phone = '', $userId = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->setEmail($email);
        $this->phone = $phone;
        $this->subject = $subject;
        $this->message = $message;
        $this->userId = $userId;
        $this->status = 'new';
        $this->response = '';
        $this->createdAt = date('Y-m-d H:i:s');
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    // Методы (поведение класса)

    /**
     * Получить ID формы
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Получить имя отправителя
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Установить имя отправителя
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Получить email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Установить email
     */
    public function setEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = $email;
            return true;
        }
        return false;
    }

    /**
     * Получить телефон
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Установить телефон
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * Получить тему сообщения
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Установить тему сообщения
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Получить сообщение
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Установить сообщение
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Получить статус
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Установить статус
     */
    public function setStatus($status)
    {
        $allowedStatuses = ['new', 'in_progress', 'resolved', 'closed'];
        if (in_array($status, $allowedStatuses)) {
            $this->status = $status;
            $this->updatedAt = date('Y-m-d H:i:s');
            return true;
        }
        return false;
    }

    /**
     * Получить ID пользователя
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Получить ответ администратора
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Установить ответ администратора
     */
    public function setResponse($response)
    {
        $this->response = $response;
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    /**
     * Проверить, является ли форма новой
     */
    public function isNew()
    {
        return $this->status === 'new';
    }

    /**
     * Отметить как обработанную
     */
    public function markAsResolved()
    {
        $this->setStatus('resolved');
    }

    /**
     * Закрыть форму
     */
    public function close()
    {
        $this->setStatus('closed');
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
     * Валидация данных формы
     */
    public function validate()
    {
        $errors = [];

        if (empty($this->name)) {
            $errors[] = 'Имя не может быть пустым';
        }

        if (empty($this->email) || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Некорректный email';
        }

        if (empty($this->subject)) {
            $errors[] = 'Тема не может быть пустой';
        }

        if (empty($this->message)) {
            $errors[] = 'Сообщение не может быть пустым';
        }

        if (strlen($this->message) < 10) {
            $errors[] = 'Сообщение должно содержать минимум 10 символов';
        }

        return $errors;
    }

    /**
     * Получить информацию о форме
     */
    public function getInfo()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'subject' => $this->subject,
            'message' => $this->message,
            'status' => $this->status,
            'user_id' => $this->userId,
            'response' => $this->response,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}

/**
 * Наследник класса ContactForm - Форма жалобы
 * Отличается наличием категории жалобы и приоритета
 */
class ComplaintForm extends ContactForm
{
    private $complaintCategory; // категория жалобы
    private $priority; // приоритет (low, medium, high, urgent)
    private $orderId; // ID заказа, если жалоба связана с заказом

    public function __construct($id, $name, $email, $subject, $message, $complaintCategory, $phone = '', $userId = null)
    {
        parent::__construct($id, $name, $email, $subject, $message, $phone, $userId);
        $this->complaintCategory = $complaintCategory;
        $this->priority = 'medium';
        $this->orderId = null;
    }

    /**
     * Получить категорию жалобы
     */
    public function getComplaintCategory()
    {
        return $this->complaintCategory;
    }

    /**
     * Установить категорию жалобы
     */
    public function setComplaintCategory($category)
    {
        $this->complaintCategory = $category;
    }

    /**
     * Получить приоритет
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Установить приоритет
     */
    public function setPriority($priority)
    {
        $allowedPriorities = ['low', 'medium', 'high', 'urgent'];
        if (in_array($priority, $allowedPriorities)) {
            $this->priority = $priority;
            return true;
        }
        return false;
    }

    /**
     * Получить ID заказа
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Установить ID заказа
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Переопределенный метод получения информации
     */
    public function getInfo()
    {
        $info = parent::getInfo();
        $info['complaint_category'] = $this->complaintCategory;
        $info['priority'] = $this->priority;
        $info['order_id'] = $this->orderId;
        return $info;
    }
}

