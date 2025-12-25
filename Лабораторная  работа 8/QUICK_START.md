# Быстрый старт

## 1. Создание тестовых данных

Выполните в командной строке (cmd) или PowerShell:

```bash
cd Лабораторная_работа_8
php init_test_data.php
```

Это создаст:
- ✅ 5 пользователей
- ✅ 5 статей  
- ✅ 8 комментариев
- ✅ Лайки для статей и комментариев

**Важно:** Все операции будут залогированы в файл `logs/app.log`

## 2. Запуск веб-интерфейса

```bash
start-server.bat
```

Или вручную:
```bash
php -S localhost:8000 router.php
```

## 3. Откройте в браузере

http://localhost:8000

## 4. Что проверить

### Вкладка "Логи"
- ✅ Должны отображаться записи уровня **INFO** при сохранении объектов
- ✅ Каждая запись должна содержать UUID объекта
- ✅ Формат: `[timestamp] [INFO] User saved: <uuid>`

### Вкладка "Данные"
- ✅ Должны отображаться созданные пользователи, статьи, комментарии
- ✅ Проверьте корректность данных

### Вкладка "Тестирование"
- ✅ Нажмите кнопки для создания новых объектов
- ✅ Проверьте, что в логах появляются новые записи **INFO**
- ✅ Нажмите "Получить несуществующий объект" - должна появиться запись **WARNING**

## 5. Запуск модульных тестов

```bash
php tests/run_tests.php
```

Должно пройти 8 тестов:
- ✅ UsersRepository save logs INFO
- ✅ UsersRepository get not found logs WARNING
- ✅ PostsRepository save logs INFO
- ✅ PostsRepository get not found logs WARNING
- ✅ CommentsRepository save logs INFO
- ✅ CommentsRepository get not found logs WARNING
- ✅ LikesRepository save logs INFO
- ✅ CommentLikesRepository save logs INFO

## 6. Проверка файла логов

Откройте файл `logs/app.log` и убедитесь, что:
- ✅ Все записи содержат timestamp, уровень и сообщение
- ✅ UUID присутствуют в сообщениях
- ✅ Формат правильный: `[YYYY-MM-DD HH:MM:SS] [LEVEL] message`

---

**Подробная инструкция:** См. [TESTING.md](TESTING.md)

