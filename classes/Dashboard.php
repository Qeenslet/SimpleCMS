<?php

/**
 * Created by PhpStorm.
 * User: GulidovEG
 * Date: 11.01.2017
 * Time: 12:14
 */
require_once('Project.php');
require_once('Dash_Model.php');
require_once('FormEditor.php');
require_once('Validator.php');
require_once('Sypher.php');

class Dashboard extends Project
{
    private $standartAttrs = [];
    protected $model;
    private $inside = '';

    public function __construct()
    {
        //TODO авторизация
        parent::__construct();
        $this->standartAttrs['{PATH_TO_ADMIN}'] = DOC_PATH . 'dash/';
        $this->model = new Dash_Model();
    }

    public function displayAdmin()
    {
        $this->roater();
    }


    private function roater()
    {
        if (!empty($_GET['edit']))
        {
            if (!empty($_POST) && empty($_GET['delete']))
            {
                $this->savePreparator($_POST, $_GET['edit']);
                $this->inside = ob_get_clean();
            }
            if (strval($_GET['edit']) == 'sections')
            {
                if (!empty($_GET['sect']) || isset($_GET['sect']))
                {
                    $this->editSingleSection($_GET['sect']);
                    exit;
                }
                $this->editSections();
            }
            else if (strval($_GET['edit']) == 'users')
            {
                if (!empty($_GET['user']) || isset($_GET['user']))
                {
                    $this->editSingleUser(intval($_GET['user']));
                    exit;
                }
                $this->editUsers();
            }
            else if (strval($_GET['edit']) == 'files')
            {
                $this->editFiles();
            }
            else if (strval($_GET['edit'] == 'news'))
            {
                if (!empty($_GET['publication']) || isset($_GET['publication']))
                {
                    $this->editSingleNews($_GET['publication']);
                    exit;
                }
                $this->editNews();
            }
        }
        else if (!empty($_GET['act']) && $_GET['act'] == 'logout')
        {
            unset($_SESSION['_smart_control']);
            unset($_SESSION['_entries']);
            header('location: index.php?section=dash');
        }
        else
        {
           $this->mainDash();
        }
    }

    private function uniter($section, $content)
    {
        $args = array();
        $args['{ADMIN_MENU}'] = $this->makeAdminMenu($section);
        if (!empty($this->inside)) $content = '<h4 style="color: red">' . $this->inside . '</h4>' . $content;
        $args['{CONTENT}'] = $content;
        $args = array_merge($args, $this->standartAttrs);
        echo Pagemaker::render('dash/index.html', $args);
    }



    private function mainDash()
    {
        $content = Pagemaker::render('dash/main_dash.html', []);
        $this->uniter('{INDEX}', $content);
    }


    private function makeAdminMenu($section)
    {
        $sections = ['{INDEX}' => '', '{USERS}' => '', '{NEWS}' => '', '{SECTIONS}' => '', '{FILES}' => ''];
        $sections[$section] = 'active-link';
        return Pagemaker::render('dash/menu.html', $sections);
    }



    private function editSections()
    {

        try
        {
            $data = $this->model->getAllSections();
            $params = array();
            $n = 1;
            foreach($data as $one)
            {
                $tmp = [];
                $tmp['{N}'] = $n;
                $tmp['{ID}'] = $one['id'];
                $tmp['{NAME}'] = $one['name'];
                $tmp['{HREF}'] = $_SERVER['SERVER_NAME'] . '/' . $one['mark'];
                $params['row'][] = $tmp;
                $n++;
            }
            //ob_start();
            //echo '<pre>'; print_r($params); echo '</pre>';
            //$content = ob_get_clean();
            $content = Pagemaker::render('dash/sections_main.html', $params);

            $this->uniter('{SECTIONS}', $content);
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
            exit;
        }
    }

    private function editUsers()
    {
        if (!empty($_POST['user']))
        {
            if ($this->model->delete('users', $_POST['user']))
            {
                echo '{result: "ok"}';
            }
            else echo '{result: "fail"}';

            return;
        }

        $data = $this->model->getAllUsers();
        $params = array();
        foreach($data as $one)
        {
            $tmp = [];
            $tmp['{ID}'] = $one['id'];
            $tmp['{UNAME}'] = $one['u_name'];
            $dt = new DateTime($one['date_added']);
            $tmp['{UDATE}'] = $dt->format('d.m.Y');
            $tmp['{UMAIL}'] = $one['u_mail'];
            $params['userline'][] = $tmp;
        }
        $content = Pagemaker::render('dash/users_main.html', $params);
        $this->uniter('{USERS}', $content);
    }


    private function editSingleSection($id)
    {
        $data = $this->model->getSection($id);
        if (empty($data)) $data = array();
        $edit = new FormEditor($data, 'Редактрирование раздела');
        $edit->addField('Название раздела', 'name', 'text');
        $edit->addField('Ключ раздела', 'mark', 'text');
        $edit->addField('Фон раздела', 'html', 'text');
        $edit->addField('Содержимое', 'insides', 'textarea');
        $edit->setParams('sections', $id);
        $content = $edit->getForm();
        $this->uniter('{SECTIONS}', $content);
    }


    private function savePreparator($post, $section)
    {
        $id = null;
        $table = null;
        $date_added = null;
        $error = '';
        if (!empty($post['table']))
        {
            if ($post['table'] == 'news')
            {
                $dt = new DateTime();
                $date_added = $dt->format('Y-m-d H:i');
            }
            else if ($post['table'] == 'users')
            {
                $dt = new DateTime();
                $date_added = $dt->format('Y-m-d H:i');
                if (!empty($post['id']))
                {
                    if (!Validator::checkOldPass($post['u_pass']))
                    {
                        unset($post['u_pass']);
                    }
                }

                if (!empty($post['u_pass']))
                {
                    if (!Validator::checkNewPass($post['u_pass']))
                    {
                        $error = 'Пароль должен содержать латинские буквы двух регистров, цифры и возможно спецсимволы @#% и быть от 8 до 12 символов';
                    }
                    $post['u_pass'] = Sypher::encode($post['u_pass']);
                }
                if (empty($post['u_name']))
                {
                    $error = 'Не указан логин пользователя!';
                }
            }
            if (!empty($post['id']))
            {
                $id = $post['id'];
            }
            $table = $post['table'];
            unset($post['table']);
            unset($post['id']);
        }
        $data = Validator::clearScripts($post);
        //echo '<pre>'; print_r($data); print_r($id); print_r($table); die;
        if ($table && empty($error))
        {
            if ($id)
            {
                $error = $this->model->updateData($data, $table, $id);
            }
            else
            {
                if (!empty($date_added)) $data['date_added'] = $date_added;
                $error = $this->model->insertData($data, $table);
            }
            if (!empty($error))
            {
                ob_start();
                echo $error;
                return;
            }
            else
            {
                $url = 'index.php?section=dash&edit=' . $section;
                header("Refresh:0; url=" . $url);
            }
        }
        else
        {
            ob_start();
            echo $error;
            return;
        }
    }


    private function editFiles()
    {
        if (!empty($_POST['to_delete']))
        {
            $filename = strval($_POST['to_delete']);
            if (file_exists('uploads/' . $filename))
            {
                unlink ('uploads/' . $filename);
            }
            return;
        }
        if (!empty($_GET['resize']) && !empty($_POST['filename']))
        {
            //print_r($_POST);
            $this->resizer($_POST);
            exit;
        }
        $params['{WARNING}'] = '';
        if (!empty($_FILES))
        {
            $res = $this->uploader();
            if ($res)
                $params['{WARNING}'] = '<h3 style="color: red">Возникла проблема: ' . $res . '</h3>';
            else
                $params['{WARNING}'] = '<span style="color:green">Успешно загружен!</span>';
        }
        $uploaded = $this->getImages();
        $tmp = array();
        $rows = array();
        $n = 1;
        foreach ($uploaded as $one)
        {
            $tmp[] = $one;
            $n++;
            if ($n > 6)
            {
                $n = 1;
                $rows[] = $this->groupInRow($tmp);
                $tmp = [];
            }
        }
        $rows[] = $this->groupInRow($tmp);
        foreach ($rows as $row)
        {
            $params['files_row'][]['{FILEROW}'] = Pagemaker::render('dash/image_row.html', $row);
        }
        $content = Pagemaker::render('dash/file_uploader.html', $params);
        $this->uniter('{FILES}', $content);
    }



    private function uploader()
    {
        $blacklist = array(".php", ".phtml", ".php3", ".php4", ".html", ".htm");
        $error = null;
        foreach ($blacklist as $item)
        {
            if(preg_match("/$item\$/i", $_FILES['filename']['name'])) $error = 'Неверный тип файла!';
        }
        if (!$error)
        {
            $type = $_FILES['filename']['type'];
            if (($type != "image/jpg") && ($type != "image/jpeg") && ($type != 'image/png'))
            {
                return 'Файл не является изображением!';
            }
            $uploadfile = "/uploads/".$_FILES['filename']['name'];
            try
            {
                move_uploaded_file($_FILES['filename']['tmp_name'], $uploadfile);
            }
            catch (Exception $e)
            {
                return $e->getMessage();
            }
        }
        return $error;
    }



    private function getImages()
    {
        $path = "uploads";
        $filelist = array();

        if($handle = opendir($path))
        {
            while($entry = readdir($handle))
            {
                if ($entry == '.' || $entry == '..') continue;
                $filelist[] = $entry;
            }
            closedir($handle);
        }
        return $filelist;
    }


    private function groupInRow($files)
    {
        $result = [];
        foreach ($files as $file)
        {
            $tmp['{SRC}'] = '/uploads/' . $file;
            $tmp['{FILE}'] = $file;
            $result['element'][] = $tmp;
        }
        return $result;
    }



    private function resizer($post)
    {
        $file = $post['filename'];
        $dims = explode('X', $post['dimensions']);
        if (!empty($dims[0]) && !empty($dims[1]))
        {
            $x = intval($dims[0]);
            $y = intval($dims[1]);
            require_once('Imaginator.php');
            $imaginator = new Imaginator($file, $post['dimensions']);
            $imaginator->saveImageResize($x, $y);
        }
    }



    private function editNews()
    {
        try
        {
            $data = $this->model->getAllNews();
            $params = [];

            foreach($data as $one)
            {
                $tmp = [];
                $dateAdded = 'не установлена';
                if (!empty($one['date_added']))
                {
                    $dt = new DateTime($one['date_added']);
                    $dateAdded = $dt->format('d.m.Y H:i');
                }
                $tmp['{DT}'] = $dateAdded;
                $tmp['{ID}'] = $one['id'];
                $tmp['{NAME}'] = $one['header'];
                $tmp['{HREF}'] = $_SERVER['SERVER_NAME'] . '/news/' . $one['mark'];
                $params['row'][] = $tmp;

            }
            //ob_start();
            //echo '<pre>'; print_r($data); echo '</pre>';
            //$content = ob_get_clean();
            $content = Pagemaker::render('dash/news_main.html', $params);

            $this->uniter('{NEWS}', $content);
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
            exit;
        }
    }


    private function editSingleNews($id)
    {
        $data = $this->model->getOneNews($id);
        if (empty($data)) $data = [];
        $edit = new FormEditor($data, 'Редактрирование материала');
        $edit->addField('Заголовок', 'header', 'text');
        $edit->addField('Адрес новости', 'mark', 'text');
        $edit->addField('Ссылка на главное изображение', 'image', 'text');
        $edit->addField('Содержимое', 'html', 'textarea');
        $edit->setParams('news', $id);
        $content = $edit->getForm();
        $this->uniter('{NEWS}', $content);
    }


    private function editSingleUser($id)
    {
        $data = $this->model->getOneUser($id);
        if (empty($data)) $data = [];
        $edit = new FormEditor($data, 'Редактрирование пользователя');
        $edit->addField('Логин', 'u_name', 'text');
        $edit->addField('Почта', 'u_mail', 'email');
        $edit->addField('Пароль', 'u_pass', 'password');
        $edit->setParams('users', $id);
        $content = $edit->getForm();
        $this->uniter('{USERS}', $content);
    }
}