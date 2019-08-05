<?php

$dir_users = dirname(__FILE__);
include_once($dir_users."/../gtree.php");
GTree::startAdminPage();

$error = '';

$personid = 0;
$firstname = '';
$secondname = '';
$lastname = '';
$bornlastname = '';
$bornyear = 0;
$bornmonth = 0;
$bornday = 0;
$yearofdeath = 0;
$monthofdeath = 0;
$dayofdeath = 0;
$mother = 0;
$father = 0;
$private = 'no';
$gtline = 0;
$bornyear_notexactly = 'no';
$yearofdeath_notexactly = 'no';
$uid = '';

$sex = 'male';

if (isset($_GET['personid'])) {
    $personid = intval($_GET['personid']);
    $conn = GTree::dbConn();
    $stmt = $conn->prepare('SELECT * FROM persons WHERE id = ?;');
    $stmt->execute(array($personid));

    if ($row = $stmt->fetch()) {
        $firstname = $row['firstname'];
        $secondname = $row['secondname'];
        $lastname = $row['lastname'];
        $bornlastname = $row['bornlastname'];
        $sex = $row['sex'];
        $bornyear = $row['bornyear'];
        $bornmonth = $row['bornmonth'];
        $bornday = $row['bornday'];
        $yearofdeath = $row['yearofdeath'];
        $monthofdeath = $row['monthofdeath'];
        $dayofdeath = $row['dayofdeath'];
        $mother = $row['mother'];
        $father = $row['father'];
        $private = $row['private'];
        $gtline = intval($row['gtline']);
        $bornyear_notexactly = $row['bornyear_notexactly'];
        $yearofdeath_notexactly = $row['yearofdeath_notexactly'];
        $uid = $row['uid'];
    } else {
        $error = 'Пeрсона не найдена';
    }
}

if (isset($_POST['do_person_update'])) {
    $personid = intval($_POST['personid']);
    $firstname = $_POST['firstname'];
    $secondname = $_POST['secondname'];
    $lastname = $_POST['lastname'];
    $bornlastname = $_POST['bornlastname'];
    $sex = $_POST['sex'];
    $bornyear = intval($_POST['bornyear']);
    $bornmonth = intval($_POST['bornmonth']);
    $bornday = intval($_POST['bornday']);
    $yearofdeath = intval($_POST['yearofdeath']);
    $monthofdeath = intval($_POST['monthofdeath']);
    $dayofdeath = intval($_POST['dayofdeath']);
    $mother = intval($_POST['mother']);
    $father = intval($_POST['father']);
    $private = $_POST['private'];
    $gtline = intval($_POST['gtline']);
    $bornyear_notexactly = $_POST['bornyear_notexactly'];
    $yearofdeath_notexactly = $_POST['yearofdeath_notexactly'];

    if (!$bornyear_notexactly) {
        $bornyear_notexactly = 'no';
    } else if ($bornyear_notexactly == 'on') {
        $bornyear_notexactly = 'yes';
    }

    if (!$yearofdeath_notexactly) {
        $yearofdeath_notexactly = 'no';
    } else if ($yearofdeath_notexactly == 'on') {
        $yearofdeath_notexactly = 'yes';
    }

    $fullname = $lastname;
    if ($bornlastname != '') {
        $fullname .= ' ('.$bornlastname.')';
    }
    $fullname .= ' '.$firstname.' '.$secondname;

    $conn = GTree::dbConn();
    $stmt = $conn->prepare('UPDATE persons
        SET
            fullname = ?,
            firstname = ?,
            secondname = ?,
            lastname = ?,
            bornlastname = ?,
            sex = ?,
            bornyear = ?,
            bornmonth = ?,
            bornday = ?,
            yearofdeath = ?,
            monthofdeath = ?,
            dayofdeath = ?,
            mother = ?,
            father = ?,
            `private` = ?,
            gtline = ?,
            bornyear_notexactly = ?,
            yearofdeath_notexactly = ?
        WHERE
            id = ?
        ');
    $values = array(
        $fullname,
        $firstname,
        $secondname,
        $lastname,
        $bornlastname,
        $sex,
        $bornyear,
        $bornmonth,
        $bornday,
        $yearofdeath,
        $monthofdeath,
        $dayofdeath,
        $mother,
        $father,
        $private,
        $gtline,
        $bornyear_notexactly,
        $yearofdeath_notexactly,
        $personid,
    );
    if (!$stmt->execute($values)) {
        $error = 'Что то пошло не так.';
        error_log(print_r($stmt->errorInfo(), true));
    } else {
        GTLog::info('loggined', '[admin#'.GTree::$USERID.'] update [person#'.$personid.']');
        header('Location: ./persons.php');
		exit;
    }
}

$persons_list_male = '';
$persons_list_female = '';

$conn = GTree::dbConn();
$stmt = $conn->prepare('SELECT * FROM persons ORDER BY bornyear;');
$stmt->execute(array());
while ($row = $stmt->fetch()) {
    if ($row['sex'] == 'male' && $row['id'] != $personid) {
        $selected = $row['id'] == $father ? 'selected' : '';
        $persons_list_male .= '<option value="'.$row['id'].'" '.$selected.'>('.$row['bornyear'].') '.$row['fullname'].'</option>';
    }

    if ($row['sex'] == 'female' && $row['id'] != $personid) {
        $selected = $row['id'] == $mother ? 'selected' : '';
        $persons_list_female .= '<option value="'.$row['id'].'" '.$selected.'>('.$row['bornyear'].') '.$row['fullname'].'</option>';
    }
}

include_once("head.php");
?>

<h3>Изменить данные о персоне</h3>
<form action="persons_edit.php" method="POST">
    <input name="personid" value="<?php echo $personid; ?>" type="hidden"/>
    <div class="form-group">
        <label>Уникальный Идентификатор</label>
        <input class="form-control" readonly name="lastname" value="<?php echo $uid; ?>" type="text"/>
    </div>
    <div class="form-group">
        <label>Фамилия</label>
        <input class="form-control" name="lastname" value="<?php echo $lastname; ?>" type="text"/>
    </div>
    <div class="form-group">
        <label>Фамилия при рождении</label>
        <input class="form-control" name="bornlastname" value="<?php echo $bornlastname; ?>" type="text"/>
    </div>
    <div class="form-group">
        <label>Имя</label>
        <input class="form-control" name="firstname" value="<?php echo $firstname; ?>" type="text"/>
    </div>
    <div class="form-group">
        <label for="password">Отчество</label>
        <input class="form-control" name="secondname" value="<?php echo $secondname; ?>" type="text"/>
    </div>
    <div class="form-group">
        <label for="password">Пол</label>
        <select class="form-control" name="sex">
            <option value="male" <?php echo $sex == 'male' ? 'selected' : '' ?> >Мужской</option>
            <option value="female" <?php echo $sex == 'female' ? 'selected' : '' ?> >Женский</option>
        </select>
    </div>
    <hr>
    <div class="form-group">
        <label for="password">Дата рождения</label>
        <div class="row">
            <div class="col-sm">
                <div class="form-group">
                    <label for="password">День</label>
                    <select class="form-control" name="bornday">
                        <?php 
                            echo '<option value="0">-</option>';
                            for ($i = 1; $i <= 31; $i++) {
                                echo '<option value="'.$i.'" '.($bornday == $i ? 'selected' : '').'>'.$i.'</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-sm">
                <div class="form-group">
                    <label for="password">Месяц</label>
                    <select class="form-control" name="bornmonth">
                        <?php 
                            echo '<option value="0">-</option>';
                            foreach(GTree::$MONTHES as $k => $v) {
                                echo '<option value="'.$k.'" '.($bornmonth == $k ? 'selected' : '').'>'.$v.'</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-sm">
                <div class="form-group">
                    <label for="password">Год</label>
                    <select class="form-control" name="bornyear">
                        <?php 
                            echo '<option value="0">-</option>';
                            for ($i = 1800; $i < 2050; $i++) {
                                echo '<option value="'.$i.'" '.($bornyear == $i ? 'selected' : '').'>'.$i.'</option>';
                            }
                        ?>
                    </select>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input"
                            id="bornyear_notexactly" name="bornyear_notexactly" 
                            <?php echo $bornyear_notexactly == 'yes' ? 'checked' : '' ?>
                        />
                        <label class="custom-control-label" for="bornyear_notexactly">Год рождения не точен</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="form-group">
        <label for="password">Дата смерти</label>
        <div class="row">
            <div class="col-sm">
                <div class="form-group">
                    <label for="password">День</label>
                    <select class="form-control" name="dayofdeath">
                        <?php 
                            echo '<option value="0">-</option>';
                            for ($i = 1; $i <= 31; $i++) {
                                echo '<option value="'.$i.'" '.($dayofdeath == $i ? 'selected' : '').'>'.$i.'</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-sm">
                <div class="form-group">
                    <label for="password">Месяц</label>
                    <select class="form-control" name="monthofdeath">
                        <?php 
                            echo '<option value="0">-</option>';
                            foreach(GTree::$MONTHES as $k => $v) {
                                echo '<option value="'.$k.'" '.($monthofdeath == $k ? 'selected' : '').'>'.$v.'</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-sm">
                <div class="form-group">
                    <label for="password">Год</label>
                    <select class="form-control" name="yearofdeath">
                        <?php 
                            echo '<option value="0">-</option>';
                            for ($i = 1800; $i < 2050; $i++) {
                                echo '<option value="'.$i.'" '.($yearofdeath == $i ? 'selected' : '').'>'.$i.'</option>';
                            }
                        ?>
                    </select>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" 
                            id="yearofdeath_notexactly" name="yearofdeath_notexactly" 
                            <?php echo $yearofdeath_notexactly == 'yes' ? 'checked' : '' ?>
                        />
                        <label class="custom-control-label" for="yearofdeath_notexactly">Год смерти не точен</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <label for="password">Мать</label>
        <select class="form-control" name="mother">
            <?php
                echo '<option value="0">-</option>';
                echo $persons_list_female;
            ?>
        </select>
    </div>

    <div class="form-group">
        <label for="password">Отец</label>
        <select class="form-control" name="father">
            <?php 
                echo '<option value="0">-</option>';
                echo $persons_list_male;
            ?>
        </select>
    </div>
    
    <div class="form-group">
        <label for="password">Приватные данные</label>
        <select class="form-control" name="private">
            <option value="yes" <?php echo ($private == 'yes' ? ' selected ' : ''); ?>>Да</option>
            <option value="no" <?php echo ($private == 'no' ? ' selected ' : ''); ?>>Нет</option>
        </select>
    </div>
    <div class="form-group">
        <label for="password">Строка в дереве</label>
        <input type="number" class="form-control" name="gtline" value="<?php echo $gtline; ?>"/>
    </div>

    <button class="btn btn-primary" name="do_person_update" >Обновить</button>
    <?php 
    if ($error != '') {
        echo '<div class="alert alert-danger" style="margin-top: 20px">'.$error.'</div>';
    }
    ?>
</form>

<?php include_once("footer.php");