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

include_once("head.php");
?>

<h3>Персоны</h3>
<a class="btn btn-primary" href="persons_add.php">Добавить персону</a>
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
    $stmt = $conn->prepare('SELECT * FROM persons');
    $stmt->execute(array());
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

        echo '
        <tr>
            <td>#'.$personid.'</td>
            <td>'.$row['bornyear'].' - '.$row['yearofdeath'].' </td>
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

