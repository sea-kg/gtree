<?php

$dir_persons = dirname(__FILE__);
include_once($dir_persons."/../gtree.php");
GTree::startAdminPage();

if (isset($_POST['do_remove_person'])) {
    $personid = intval($_POST['personid']);
    
    $conn = GTree::dbConn();
    $stmt = $conn->prepare('DELETE FROM persons WHERE id = ?;');
    if (!$stmt->execute(array($personid))) {
        $error = 'Что то пошло не так.';
    } else {
        GTLog::info('users', '[admin#'.GTree::$USERID.'] removed [person#'.$personid.']');
        header('Location: ./persons.php');
		exit;
    }
}

$filter = '';

if (isset($_GET['filter'])) {
    $filter = trim($_GET['filter']);
}

include_once("head.php");
?>

<h3>Персоны</h3>
<a class="btn btn-primary" href="persons_add.php">Добавить персону</a>
<a class="btn btn-primary" target="_blank" href="persons_export.php">Экспорт данных</a>
<a class="btn btn-primary" href="persons_import.php">Импорт данных</a>
<hr>
<form method="GET" action="persons.php" class="form-inline">
    <div class="form-group mb-2">
        <input type="text" readonly class="form-control-plaintext" id="staticEmail2" value="Фильтр:">
    </div>
    <div class="form-group mx-sm-3 mb-2">
        <input type="text" autofocus class="form-control" name="filter" value="<?php echo htmlspecialchars($filter); ?>" id="inputPassword2">
    </div>
    <button type="submit" class="btn btn-primary mb-2">Применить фильтр</button>
</form>
<br/><br/>
<table class="table">
    <thead class="thead-dark">
        <tr>
            <th>#</th>
            <th>Годы жизни</th>
            <th>ФИО</th>
            <th>Отец</th>
            <th>Мать</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
  
    <?php

    $conn = GTree::dbConn();
    $filters = array();
    $values = array();
    if ($filter != '') {
        $filters[] = '(fullname LIKE ?)';
        $values[] = '%'.$filter.'%';

        $filters[] = '(bornlastname LIKE ?)';
        $values[] = '%'.$filter.'%';

        $filters[] = '(lastname LIKE ?)';
        $values[] = '%'.$filter.'%';

        $filters[] = '(firstname LIKE ?)';
        $values[] = '%'.$filter.'%';

        $filters[] = '(secondname LIKE ?)';
        $values[] = '%'.$filter.'%';
        if (is_numeric($filter)) {
            $filter_int = intval($filter);
            
            $filters[] = '(bornyear = ?)';
            $values[] = $filter_int;

            $filters[] = '(id = ?)';
            $values[] = $filter_int;

            $filters[] = '(father = ?)';
            $values[] = $filter_int;

            $filters[] = '(mother = ?)';
            $values[] = $filter_int;

            $filters[] = '(yearofdeath = ?)';
            $values[] = $filter_int;
        }
    }

    if (count($filters) > 0) {
        $filters = ' WHERE '.implode(' OR ', $filters);
    } else {
        $filters = '';
    }
    
    $query = 'SELECT * FROM persons '.$filters.' ORDER BY bornyear';
    // echo $query;
    // $filter
    $stmt = $conn->prepare($query);
    $stmt->execute($values);
    while ($row = $stmt->fetch()) {
        $personid = $row['id'];
        $mother = $row['mother'];
        if ($mother != 0) {
            $mother = '[person#'.$mother.']';
        } else {
            $mother = '-';
        }

        $father = $row['father'];
        if ($father != 0) {
            $father = '[person#'.$father.']';
        } else {
            $father = '-';
        }

        $bornyear_notexactly = '';
        if ($row['bornyear_notexactly'] == 'yes') {
            $bornyear_notexactly = ' (не точно)';
        }

        $yearofdeath_notexactly = '';
        if ($row['yearofdeath_notexactly'] == 'yes') {
            $yearofdeath_notexactly = ' (не точно)';
        }

        echo '
        <tr>
            <td>#'.$personid.'</td>
            <td>'.$row['bornyear'].$bornyear_notexactly.' - '.$row['yearofdeath'].$yearofdeath_notexactly.' </td>
            <td>'.$row['fullname'].' <a class="btn btn-primary" href="persons_edit.php?personid='.$personid.'">Изменить</a></td>
            <td>'.$father.'</td>
            <td>'.$mother.'</td>
            <td>
                <form action="persons.php" method="POST">
                    <input type="hidden" name="personid" value="'.$personid.'"/>
                    <button class="btn btn-danger" name="do_remove_person">Удалить</button>
                </form>
            </td>
        </tr>
        ';
    }
?>
    </tbody>
</table>

<?php



include_once("footer.php");

