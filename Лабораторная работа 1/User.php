<?php

/**
 * Класс Пользователь - представляет пользователя интернет-магазина
 */
class User
{
    // Свойства (состояние класса)
    protected $id;
    protected $email;
    protected $password;
    protected $firstName;
    protected $lastName;
    protected $phone;
    protected $address;
    protected $registrationDate;
    protected $isActive;
    protected $role; // роль пользователя (customer, admin, etc.)

    /**
     * Конструктор класса
     */
    public function __construct($id, $email, $password, $firstName = '', $lastName = '', $phone = '', $address = '', $role = 'customer')
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phone = $phone;
        $this->address = $address;
        $this->role = $role;
        $this->isActive = true;
        $this->registrationDate = date('Y-m-d H:i:s');
    }

    // Методы (поведение класса)

    /**
     * Получить ID пользователя
     */
    public function getId()
    {
        return $this->id;
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
     * Проверить пароль
     */
    public function verifyPassword($password)
    {
        return password_verify($password, $this->password);
    }

    /**
     * Установить новый пароль
     */
    public function setPassword($newPassword)
    {
        $this->password = password_hash($newPassword, PASSWORD_DEFAULT);
    }

    /**
     * Получить полное имя
     */
    public function getFullName()
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }

    /**
     * Получить имя
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Установить имя
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * Получить фамилию
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Установить фамилию
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
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
     * Получить адрес
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Установить адрес
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * Получить роль
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Установить роль
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * Проверить, активен ли пользователь
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * Активировать пользователя
     */
    public function activate()
    {
        $this->isActive = true;
    }

    /**
     * Деактивировать пользователя
     */
    public function deactivate()
    {
        $this->isActive = false;
    }

    /**
     * Получить дату регистрации
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * Получить информацию о пользователе
     */
    public function getInfo()
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'full_name' => $this->getFullName(),
            'phone' => $this->phone,
            'address' => $this->address,
            'role' => $this->role,
            'is_active' => $this->isActive,
            'registration_date' => $this->registrationDate
        ];
    }
}

/**
 * Наследник класса User - Администратор
 * Отличается расширенными правами доступа и дополнительными методами управления
 */
class Admin extends User
{
    private $permissions; // массив разрешений
    private $lastLogin;
    private $loginCount;

    public function __construct($id, $email, $password, $firstName = '', $lastName = '')
    {
        parent::__construct($id, $email, $password, $firstName, $lastName, '', '', 'admin');
        $this->permissions = ['manage_products', 'manage_users', 'manage_orders', 'view_reports'];
        $this->lastLogin = null;
        $this->loginCount = 0;
    }

    /**
     * Получить разрешения
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Добавить разрешение
     */
    public function addPermission($permission)
    {
        if (!in_array($permission, $this->permissions)) {
            $this->permissions[] = $permission;
        }
    }

    /**
     * Проверить наличие разрешения
     */
    public function hasPermission($permission)
    {
        return in_array($permission, $this->permissions);
    }

    /**
     * Зарегистрировать вход
     */
    public function recordLogin()
    {
        $this->lastLogin = date('Y-m-d H:i:s');
        $this->loginCount++;
    }

    /**
     * Получить дату последнего входа
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Получить количество входов
     */
    public function getLoginCount()
    {
        return $this->loginCount;
    }
}

/**
 * Наследник класса User - VIP клиент
 * Отличается наличием скидок, бонусов и приоритетной поддержкой
 */
class VIPCustomer extends User
{
    private $discount; // процент скидки
    private $bonusPoints; // бонусные баллы
    private $vipLevel; // уровень VIP (bronze, silver, gold, platinum)
    private $totalPurchases; // общая сумма покупок

    public function __construct($id, $email, $password, $firstName = '', $lastName = '', $vipLevel = 'bronze')
    {
        parent::__construct($id, $email, $password, $firstName, $lastName);
        $this->vipLevel = $vipLevel;
        $this->bonusPoints = 0;
        $this->totalPurchases = 0;
        $this->setDiscountByLevel($vipLevel);
    }

    /**
     * Установить скидку в зависимости от уровня
     */
    private function setDiscountByLevel($level)
    {
        $discounts = [
            'bronze' => 5,
            'silver' => 10,
            'gold' => 15,
            'platinum' => 20
        ];
        $this->discount = isset($discounts[$level]) ? $discounts[$level] : 0;
    }

    /**
     * Получить скидку
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Получить бонусные баллы
     */
    public function getBonusPoints()
    {
        return $this->bonusPoints;
    }

    /**
     * Добавить бонусные баллы
     */
    public function addBonusPoints($points)
    {
        $this->bonusPoints += $points;
    }

    /**
     * Использовать бонусные баллы
     */
    public function useBonusPoints($points)
    {
        if ($this->bonusPoints >= $points) {
            $this->bonusPoints -= $points;
            return true;
        }
        return false;
    }

    /**
     * Получить уровень VIP
     */
    public function getVipLevel()
    {
        return $this->vipLevel;
    }

    /**
     * Повысить уровень VIP
     */
    public function upgradeVipLevel()
    {
        $levels = ['bronze', 'silver', 'gold', 'platinum'];
        $currentIndex = array_search($this->vipLevel, $levels);
        if ($currentIndex !== false && $currentIndex < count($levels) - 1) {
            $this->vipLevel = $levels[$currentIndex + 1];
            $this->setDiscountByLevel($this->vipLevel);
            return true;
        }
        return false;
    }

    /**
     * Добавить сумму покупки
     */
    public function addPurchase($amount)
    {
        $this->totalPurchases += $amount;
        // Начисляем бонусы (1% от суммы)
        $this->addBonusPoints(floor($amount * 0.01));
    }

    /**
     * Получить общую сумму покупок
     */
    public function getTotalPurchases()
    {
        return $this->totalPurchases;
    }

    /**
     * Переопределенный метод получения информации
     */
    public function getInfo()
    {
        $info = parent::getInfo();
        $info['vip_level'] = $this->vipLevel;
        $info['discount'] = $this->discount;
        $info['bonus_points'] = $this->bonusPoints;
        $info['total_purchases'] = $this->totalPurchases;
        return $info;
    }
}

