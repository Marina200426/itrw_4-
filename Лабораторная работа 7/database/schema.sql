-- Создание таблицы пользователей
CREATE TABLE IF NOT EXISTS users (
    uuid TEXT PRIMARY KEY NOT NULL,
    username TEXT NOT NULL UNIQUE,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL
);

-- Создание таблицы статей
CREATE TABLE IF NOT EXISTS posts (
    uuid TEXT PRIMARY KEY NOT NULL,
    author_uuid TEXT NOT NULL,
    title TEXT NOT NULL,
    text TEXT NOT NULL,
    FOREIGN KEY (author_uuid) REFERENCES users(uuid)
);

-- Создание таблицы комментариев
CREATE TABLE IF NOT EXISTS comments (
    uuid TEXT PRIMARY KEY NOT NULL,
    posts_uuid TEXT NOT NULL,
    author_uuid TEXT NOT NULL,
    text TEXT NOT NULL,
    FOREIGN KEY (posts_uuid) REFERENCES posts(uuid),
    FOREIGN KEY (author_uuid) REFERENCES users(uuid)
);

-- Создание таблицы лайков для статей
CREATE TABLE IF NOT EXISTS post_likes (
    uuid TEXT PRIMARY KEY NOT NULL,
    post_uuid TEXT NOT NULL,
    user_uuid TEXT NOT NULL,
    FOREIGN KEY (post_uuid) REFERENCES posts(uuid),
    FOREIGN KEY (user_uuid) REFERENCES users(uuid),
    UNIQUE(post_uuid, user_uuid)
);

-- Создание таблицы лайков для комментариев
CREATE TABLE IF NOT EXISTS comment_likes (
    uuid TEXT PRIMARY KEY NOT NULL,
    comment_uuid TEXT NOT NULL,
    user_uuid TEXT NOT NULL,
    FOREIGN KEY (comment_uuid) REFERENCES comments(uuid),
    FOREIGN KEY (user_uuid) REFERENCES users(uuid),
    UNIQUE(comment_uuid, user_uuid)
);


