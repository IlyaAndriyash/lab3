Создание таблицы заявок (applications):

```SQL
CREATE TABLE IF NOT EXISTS applications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fio VARCHAR(150) NOT NULL,
  phone VARCHAR(15) NOT NULL,
  email VARCHAR(100) NOT NULL,
  dob DATE NOT NULL,
  gender ENUM('male', 'female') NOT NULL,
  bio TEXT NOT NULL,
  contract TINYINT(1) NOT NULL
);
```

Создание таблицы языков программирования (programming_languages):

```SQL
CREATE TABLE IF NOT EXISTS programming_languages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE
);
```

Создание таблицы связи между заявками и языками программирования (application_languages):

```SQL
CREATE TABLE IF NOT EXISTS application_languages (
  application_id INT NOT NULL,
  language_id INT NOT NULL,
  PRIMARY KEY (application_id, language_id),
  FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
  FOREIGN KEY (language_id) REFERENCES programming_languages(id) ON DELETE CASCADE
);
```
