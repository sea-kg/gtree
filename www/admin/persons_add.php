<?php

$dir_users = dirname(__FILE__);
include_once($dir_users."/../gtree.php");
GTree::startAdminPage();

$error = '';

if (isset($_POST['do_person_add'])) {
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
    $gtline = $_POST['gtline'];
    $borndate = $bornday.'-'.$bornmonth.'-'.$bornyear;
    
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
    $stmt = $conn->prepare('INSERT INTO persons(
            fullname,
            firstname,
            secondname,
            lastname,
            bornlastname,
            sex,
            bornyear,
            bornmonth,
            bornday,
            yearofdeath,
            monthofdeath,
            dayofdeath,
            mother,
            father,
            `private`,
            gtline,
            bornyear_notexactly,
            yearofdeath_notexactly
        ) VALUES(
            ?,?,?,?,?,
            ?,?,?,?,?,
            ?,?,?,?,?,
            ?,?,?
        );');
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
    );
    if (!$stmt->execute($values)) {
        $error = 'Что то пошло не так.';
        error_log(print_r($stmt->errorInfo(), true));
    } else {
        $newpersonid = $conn->lastInsertId();
        GTLog::info('loggined', '[admin#'.GTree::$USERID.'] added new [person#'.$newpersonid.']');
        header('Location: ./persons.php');
		exit;
    }
}

$persons_list_male = '';
$persons_list_female = '';

$conn = GTree::dbConn();
$stmt = $conn->prepare('SELECT * FROM persons');
$stmt->execute(array());
while ($row = $stmt->fetch()) {
    if ($row['sex'] == 'male') {
        $persons_list_male .= '<option value="'.$row['id'].'">('.$row['bornyear'].') '.$row['fullname'].'</option>';
    }

    if ($row['sex'] == 'female') {
        $persons_list_female .= '<option value="'.$row['id'].'">('.$row['bornyear'].') '.$row['fullname'].'</option>';
    }
}

include_once("head.php");
?>

<h3>Добавить новую персону</h3>
<form action="persons_add.php" method="POST">
    <div class="form-group">
        <label>Фамилия</label>
        <input class="form-control" name="lastname" type="text"/>
    </div>
    <div class="form-group">
        <label>Фамилия при рождении</label>
        <input class="form-control" name="bornlastname" type="text"/>
    </div>
    <div class="form-group">
        <label>Имя</label>
        <input class="form-control" name="firstname" type="text"/>
    </div>
    <div class="form-group">
        <label for="password">Отчество</label>
        <input class="form-control" name="secondname" type="text"/>
    </div>
    <div class="form-group">
        <label for="password">Пол</label>
        <select class="form-control" name="sex">
            <option value="male">Мужской</option>
            <option value="female">Женский</option>
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
                                echo '<option value="'.$i.'">'.$i.'</option>';
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
                                echo '<option value="'.$k.'">'.$v.'</option>';
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
                                echo '<option value="'.$i.'">'.$i.'</option>';
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
                                echo '<option value="'.$i.'">'.$i.'</option>';
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
                                echo '<option value="'.$k.'">'.$v.'</option>';
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
                                echo '<option value="'.$i.'">'.$i.'</option>';
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
            <option value="no">Нет</option>    
            <option value="yes">Да</option>
        </select>
    </div>
    <div class="form-group">
        <label for="password">Строка в дереве</label>
        <input type="number" class="form-control" name="gtline" value="0"/>
    </div>

    <button class="btn btn-primary" name="do_person_add" >Добавить</button>
    <?php 
    if ($error != '') {
        echo '<div class="alert alert-danger" style="margin-top: 20px">'.$error.'</div>';
    }
    ?>
</form>

<?php include_once("footer.php");