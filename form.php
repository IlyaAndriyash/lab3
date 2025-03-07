<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма</title>
    <style>
        /* Ваши стили */
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Форма</h2>
        <form action="index.php" method="POST">
            <!-- Поле ФИО -->
            <label for="fio">ФИО:</label>
            <input type="text" name="fio" id="fio" required>

            <!-- Поле Телефон -->
            <label for="phone">Телефон:</label>
            <input type="tel" name="phone" id="phone" required>

            <!-- Поле Email -->
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <!-- Поле Дата рождения -->
            <label for="dob">Дата рождения:</label>
            <input type="date" name="dob" id="dob" required>

            <!-- Поле Пол -->
            <label>Пол:</label>
            <div class="radio-group">
                <input type="radio" name="gender" value="male" id="male" required>
                <label for="male">Мужской</label>
                <input type="radio" name="gender" value="female" id="female" required>
                <label for="female">Женский</label>
            </div>

            <!-- Поле Любимый язык программирования -->
            <label for="languages">Любимый язык программирования:</label>
            <select name="languages[]" id="languages" multiple required>
                <option value="Pascal">Pascal</option>
                <option value="C">C</option>
                <option value="C++">C++</option>
                <option value="JavaScript">JavaScript</option>
                <option value="PHP">PHP</option>
                <option value="Python">Python</option>
                <option value="Java">Java</option>
                <option value="Haskell">Haskell</option>
                <option value="Clojure">Clojure</option>
                <option value="Prolog">Prolog</option>
                <option value="Scala">Scala</option>
                <option value="Go">Go</option>
            </select>

            <!-- Поле Биография -->
            <label for="bio">Биография:</label>
            <textarea name="bio" id="bio" rows="5" cols="40" required></textarea>

            <!-- Чекбокс "С контрактом ознакомлен" -->
            <div class="checkbox-group">
                <input type="checkbox" name="contract" id="contract" required>
                <label for="contract">С контрактом ознакомлен:</label>
            </div>

            <!-- Кнопка отправки формы -->
            <input type="submit" value="Сохранить">
        </form>
    </div>
</body>
</html>
