<?php
// Устанавливаем кодировку UTF-8
header('Content-Type: text/html; charset=UTF-8');

// Инициализируем массивы для ошибок и введённых данных
$errors = [];
$values = [
    'fio' => '',
    'phone' => '',
    'email' => '',
    'dob' => '',
    'gender' => '',
    'languages' => [],
    'bio' => '',
    'contract' => false
];

// Если метод GET – просто отображаем форму
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_GET['save'])) {
        print('Спасибо, результаты сохранены.');
    }
    include('form.php');
    exit();
}

// Сохранение введённых данных
$values['fio'] = $_POST['fio'] ?? '';
$values['phone'] = $_POST['phone'] ?? '';
$values['email'] = $_POST['email'] ?? '';
$values['dob'] = $_POST['dob'] ?? '';
$values['gender'] = $_POST['gender'] ?? '';
$values['languages'] = $_POST['languages'] ?? [];
$values['bio'] = $_POST['bio'] ?? '';
$values['contract'] = isset($_POST['contract']) ? true : false;

// Проверка поля ФИО
if (empty($values['fio'])) {
    $errors[] = 'Заполните ФИО.';
} elseif (!preg_match('/^[a-zA-Zа-яА-Я\s]{1,150}$/u', $values['fio'])) {
    $errors[] = 'ФИО должно содержать только буквы и пробелы и быть не длиннее 150 символов.';
}

// Проверка поля Телефон
if (empty($values['phone'])) {
    $errors[] = 'Заполните телефон.';
} elseif (!preg_match('/^\+?\d{10,15}$/', $values['phone'])) {
    $errors[] = 'Телефон должен быть в формате +7XXXXXXXXXX или XXXXXXXXXX.';
}

// Проверка поля Email
if (empty($values['email'])) {
    $errors[] = 'Заполните email.';
} elseif (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Некорректный email.';
}

// Проверка поля Дата рождения
if (empty($values['dob'])) {
    $errors[] = 'Заполните дату рождения.';
} elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $values['dob'])) {
    $errors[] = 'Некорректный формат даты рождения.';
}

// Проверка поля Пол
if (empty($values['gender'])) {
    $errors[] = 'Выберите пол.';
} elseif (!in_array($values['gender'], ['male', 'female'])) {
    $errors[] = 'Некорректное значение пола.';
}

// Проверка поля Любимый язык программирования
$allowedLanguages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go'];
if (empty($values['languages'])) {
    $errors[] = 'Выберите хотя бы один язык программирования.';
} else {
    foreach ($values['languages'] as $language) {
        if (!in_array($language, $allowedLanguages)) {
            $errors[] = 'Некорректный язык программирования.';
            break;
        }
    }
}

// Проверка поля Биография
if (empty($values['bio'])) {
    $errors[] = 'Заполните биографию.';
}

// Проверка чекбокса "С контрактом ознакомлен"
if (!$values['contract']) {
    $errors[] = 'Необходимо ознакомиться с контрактом.';
}

// Если есть ошибки, передаем их в form.php и не завершаем выполнение скрипта
if (!empty($errors)) {
    include('form.php');
    exit();
}

// Подключение к базе данных
$user = 'u68818';
$pass = '9972335';
$db = new PDO('mysql:host=localhost;dbname=u68818', $user, $pass, [
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

try {
    $db->beginTransaction();

    $stmt = $db->prepare("INSERT INTO applications (fio, phone, email, dob, gender, bio, contract) 
                          VALUES (:fio, :phone, :email, :dob, :gender, :bio, :contract)");
    $stmt->execute([
        ':fio' => $values['fio'],
        ':phone' => $values['phone'],
        ':email' => $values['email'],
        ':dob' => $values['dob'],
        ':gender' => $values['gender'],
        ':bio' => $values['bio'],
        ':contract' => $values['contract']
    ]);

    $application_id = $db->lastInsertId();

    $stmt = $db->prepare("SELECT id FROM programming_languages WHERE name = :name");
    $insertLang = $db->prepare("INSERT INTO programming_languages (name) VALUES (:name)");
    $linkStmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (:application_id, :language_id)");

    foreach ($values['languages'] as $language) {
        $stmt->execute([':name' => $language]);
        $languageData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$languageData) {
            $insertLang->execute([':name' => $language]);
            $language_id = $db->lastInsertId();
        } else {
            $language_id = $languageData['id'];
        }

        $linkStmt->execute([
            ':application_id' => $application_id,
            ':language_id' => $language_id
        ]);
    }

    $db->commit();

    header('Location: ?save=1');
} catch (PDOException $e) {
    $db->rollBack();
    print('Ошибка при сохранении данных: ' . $e->getMessage());
    exit();
}
?>
