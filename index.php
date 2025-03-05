<?php
// Устанавливаем кодировку UTF-8
header('Content-Type: text/html; charset=UTF-8');

// Если метод GET – просто отображаем форму
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_GET['save'])) {
        print('Спасибо, результаты сохранены.');
    }
    include('form.php');
    exit();
}

// Инициализируем массив для ошибок
$errors = [];

// Проверка поля ФИО
if (empty($_POST['fio'])) {
    $errors[] = 'Заполните ФИО.';
} elseif (!preg_match('/^[a-zA-Zа-яА-Я\s]{1,150}$/u', $_POST['fio'])) {
    $errors[] = 'ФИО должно содержать только буквы и пробелы и быть не длиннее 150 символов.';
}

// Проверка поля Телефон
if (empty($_POST['phone'])) {
    $errors[] = 'Заполните телефон.';
} elseif (!preg_match('/^\+?\d{10,15}$/', $_POST['phone'])) {
    $errors[] = 'Телефон должен быть в формате +7XXXXXXXXXX или XXXXXXXXXX.';
}

// Проверка поля Email
if (empty($_POST['email'])) {
    $errors[] = 'Заполните email.';
} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Некорректный email.';
}

// Проверка поля Дата рождения
if (empty($_POST['dob'])) {
    $errors[] = 'Заполните дату рождения.';
} elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['dob'])) {
    $errors[] = 'Некорректный формат даты рождения.';
}

// Проверка поля Пол
if (empty($_POST['gender'])) {
    $errors[] = 'Выберите пол.';
} elseif (!in_array($_POST['gender'], ['male', 'female'])) {
    $errors[] = 'Некорректное значение пола.';
}

// Проверка поля Любимый язык программирования
if (empty($_POST['languages'])) {
    $errors[] = 'Выберите хотя бы один язык программирования.';
} else {
    $allowedLanguages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go'];
    foreach ($_POST['languages'] as $language) {
        if (!in_array($language, $allowedLanguages)) {
            $errors[] = 'Некорректный язык программирования.';
            break;
        }
    }
}

// Проверка поля Биография
if (empty($_POST['bio'])) {
    $errors[] = 'Заполните биографию.';
}

// Проверка чекбокса "С контрактом ознакомлен"
if (empty($_POST['contract'])) {
    $errors[] = 'Необходимо ознакомиться с контрактом.';
}

// Если есть ошибки, выводим их и завершаем выполнение
if (!empty($errors)) {
    foreach ($errors as $error) {
        print($error . '<br>');
    }
    exit();
}

// Подключение к базе данных
$user = 'u68818'; // Логин по умолчанию
$pass = '9972335'; // Пароль по умолчанию
$db = new PDO('mysql:host=localhost;dbname=u68818', $user, $pass, [
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

try {
    // Сохранение основной информации о заявке
    $stmt = $db->prepare("INSERT INTO application (fio, phone, email, dob, gender, bio, contract) 
                          VALUES (:fio, :phone, :email, :dob, :gender, :bio, :contract)");
    $stmt->execute([
        ':fio' => $_POST['fio'],
        ':phone' => $_POST['phone'],
        ':email' => $_POST['email'],
        ':dob' => $_POST['dob'],
        ':gender' => $_POST['gender'],
        ':bio' => $_POST['bio'],
        ':contract' => $_POST['contract'] ? 1 : 0
    ]);

    // Получение ID последней вставленной записи
    $application_id = $db->lastInsertId();

    // Сохранение выбранных языков программирования
    $stmt = $db->prepare("INSERT INTO languages (application_id, language) VALUES (:application_id, :language)");
    foreach ($_POST['languages'] as $language) {
        $stmt->execute([
            ':application_id' => $application_id,
            ':language' => $language
        ]);
    }

    // Перенаправление на страницу с сообщением об успешном сохранении
    header('Location: ?save=1');
} catch (PDOException $e) {
    print('Ошибка при сохранении данных: ' . $e->getMessage());
    exit();
}
?>
