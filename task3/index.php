<?php
// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {



  // В суперглобальном массиве $_GET PHP хранит все параметры, переданные в текущем запросе через URL.
  if (!empty($_GET['save'])) {
    // Если есть параметр save, то выводим сообщение пользователю.
    print('Спасибо, результаты сохранены.');
}

  // Включаем содержимое файла form.php.
  include('form.php');
  // Завершаем работу скрипта.
  exit();
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.

// Проверяем ошибки.
$errors = FALSE;
if (empty($_POST['fio']) || !preg_match('/^([а-яА-ЯЁёa-zA-Z_,.\s-]+)$/u', $_POST['fio']) || strlen($_POST['fio']) < 5 ) {
  print('Заполните корректно имя. Минимум 5 символов, только русские и английские буквы.(Не должно быть пустым.
  Должно  содержать только русские и английские буквы, запятые, точки, пробелы и дефисы.
  Должно быть длиннее 4 символов.)<br/>');
  $errors = TRUE;
}


if (empty($_POST['tel']) ||  strlen($_POST['tel']) <= 6) {
  print('Пожалуйста, введите корректный номер телефона. Номер телефона должен содержать больше 6 символов<br/>');
  $errors = TRUE;
}

if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL )) {
  print('Заполните корректно email.(Не должно быть пустым.
  Должно соответствовать формату электронной почты.)<br/>');
  $errors = TRUE;
}

if (empty($_POST['date_of_birth']) || !preg_match('%[1-2][0-9][0-9][0-9]-[0-1][0-9]-[0-3][0-9]%', $_POST['date_of_birth'])) {
  print('Заполните корректно дату рождения.<br/>');
  $errors = TRUE;
}

if (empty($_POST['gender']) || !in_array($_POST['gender'], ['w','m'])) {
  print('Выберите пол.<br/>');
  $errors = TRUE;
}


if (empty($_POST['languages'])) {
  print('Выберите хотя бы один ЯП.<br/>');
  $errors = TRUE;
}
else{
  foreach ($_POST['languages'] as $language) {
    if(!in_array($language, [1,2,3,4,5,6,7,8,9,10,11])){
      print('корректно выберите хотя бы один ЯП.<br/>');
       $errors = TRUE;
    }
  }
}


if (empty($_POST['bio']) || !preg_match('/^([а-яА-ЯЁёa-zA-Z0-9_,.\s-]+)$/u', $_POST['bio'])) {
  print('Заполните корректно биографию. (Не должно быть пустым.
  Должно содержать только русские и английские буквы, цифры, запятые, точки, пробелы и дефисы.)<br/>');
  $errors = TRUE;
 }


if (empty($_POST['checkbox'])|| $_POST['checkbox']!=1) {
  print('Вы не согласились с условиями контракта.<br/>');
  $errors = TRUE;
}


if ($errors) {
  // При наличии ошибок завершаем работу скрипта.
  exit();
}


// Сохранение в базу данных.

$user = 'u67317';
$pass = '3462139';
$db = new PDO('mysql:host=localhost;dbname=u67317', $user, $pass,
[PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); 

// Подготовленный запрос. Не именованные метки.
try {

  $stmt = $db->prepare("INSERT INTO application (name,tel,email,date_of_birth,gender,bio,checkbox) VALUES 
  (?,?,?,?,?,?,?)");
  $stmt -> execute([$_POST['fio'],$_POST['tel'], $_POST['email'], $_POST['date_of_birth'], $_POST['gender'], $_POST['bio'], $_POST['checkbox']]);
  $id = $db->lastInsertId();
  $stmt = $db->prepare("INSERT INTO app_language (id_app, id_pl) VALUES (?,?)");
    foreach ($_POST['languages'] as $ability) {
          $stmt->execute([$id, $ability]);
        }



 
}
catch(PDOException $e){
  print('Error : ' . $e->getMessage());
  exit();
}


// Делаем перенаправление.
// Если запись не сохраняется, но ошибок не видно, то можно закомментировать эту строку чтобы увидеть ошибку.
// Если ошибок при этом не видно, то необходимо настроить параметр display_errors для PHP.
header('Location: ?save=1');
