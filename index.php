<?php
//Парсер PHP Simple HTML DOM Parser https://simplehtmldom.sourceforge.io/
include ('simple_html_dom.php');

//Скрипт бд
include 'db.php';

//Массив с сылками
$arr = ['https://t.me/js_ru', 'https://t.me/exclusive_music_telegram', 'https://t.me/unity_3d_home', 'https://t.me/ithumor', 'https://t.me/dartlang_jobs'];

//Подключаемся к бд
$dbhost = 'localhost';
$dbuser = '*****';
$dbpass = '*****';
$dbname = '*****';
$db = new db($dbhost, $dbuser, $dbpass, $dbname);
global $db;

//Переберем массив с ссылками
foreach ($arr as & $value)
{

    //Читаем содержимое
    $html = file_get_html($value);

    //Парсим div с классом
    foreach ($html->find('div.tgme_page') as $e)
    {

        //Читаем содержимое
        $article = file_get_html($value);

        //ищем картинки
        foreach ($html->find('img') as $photos)

        //Чистим ссылки от тегов
        $text = ($photos);
        (bool)preg_match('#<img[^>]+src=[\'"]([^\'"]+)[\'"]#', $text, $result);

        //Чистим от протокола передачи
        $str = $value;
        $str = preg_replace('#^https?\:\/\/([\w*\.]*)#', '', $str);
        $str = preg_replace('/[^ a-zа-яё\d]/ui', '', $str);

        //Создаем папку для картинок из телеграм каналов
        if (!file_exists("images"))
        {
            mkdir("images", 0700);
        }

        //Сохраняем картинки в указанную папку
        $move_dir = "images/";
        $url = $result[1];
        $img2 = '.png';
        $file = file($url);
        $rez = "$str$img2";
        $site = "http://*****/";
        $img_href = ("$site$move_dir$rez");
        $results = file_put_contents($move_dir . '/' . $rez, $file);

        //Получаем время
        $dates = (new \DateTime())->format('Y-m-d H:i:s');

        //Парсим
        $title = $article->find('div.tgme_page_title', 0)->innertext;
        $extra = $article->find('div.tgme_page_extra', 0)->innertext;
        $description = $article->find('div.tgme_page_description', 0)->innertext;

        //Сохраняем в бд, и заменяем если имеется запись
        $values = $db->escape($value);
        $sql1 = ("INSERT ignore INTO `article` (url, img_href, photo, title, extra, description, dates) VALUES ('" . $values . "', '" . $img_href . "', '" . $result[1] . "', '" . $title . "', '" . $extra . "', '" . $description . "', '" . $dates . "')");
        $db->query($sql1);
        $sql = ("UPDATE article set photo='" . $result[1] . "', img_href='" . $img_href . "', title='" . $title . "', extra='" . $extra . "', description='" . $description . "', dates='" . $dates . "' where url='" . $values . "'");
        $db->query($sql);

    }
}

//Выводи данные из бд
$article = $db->query('SELECT * FROM article')
    ->fetchAll();

foreach ($article as $pho)
{

    echo '<a href="' . $pho['url'] . '">' . $pho['title'] . '</a>';
    echo '<a href="' . $pho['url'] . '">' . '<img src="' . $pho['img_href'] . '">' . '</a>';
    echo $pho['photo'] . '<br>';
    echo $pho['img_href'] . '<br>';
    echo $pho['title'] . '<br>';
    echo $pho['description'] . '<br>';
    echo $pho['dates'] . '<br>';

}
?>
