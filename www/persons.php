<?php
      include_once("gtree.php"); 

?><!doctype html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <script src="./js/jquery-3.2.1.slim.min.js"></script>
    <script src="./js/popper.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./data.js"></script>
    <script src="./js/index.js"></script>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./index.css">

    <title>Генеологическое дерево</title>
  </head>
  <body>
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
          <a class="navbar-brand" href="#">Генеологическое древо</a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
        
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                  <a class="nav-link" href="persons.php">Дерево</a>
                </li>
                <li class="nav-item active">
                  <a class="nav-link" href="persons.php">Персоны</a>
                </li>
              </ul>
            </div>
          </div>
        </nav>
      <div class="container">
      
      
        <?php
        
        // father / mother
        $conn = GTree::dbConn();
        $persons = array();
        $stmt = $conn->prepare('SELECT * FROM persons');
        $stmt->execute(array());
        while ($row = $stmt->fetch()) {
          $personid = $row['id'];
          if ($row['private'] == 'yes') {
            $persons[$personid] = $row['firstname'];
          } else {
            $persons[$personid] = $row['fullname'];
          }
        }

        
        $stmt = $conn->prepare('SELECT * FROM persons ORDER BY bornyear');
        $stmt->execute(array());
        while ($row = $stmt->fetch()) {
            $personid = $row['id'];
            
            $motherid = $row['mother'];
            $mother = '';
            if ($motherid != 0) {
                $mother = '#'.$motherid;
                if (isset($persons[$motherid])) {
                  $mother .= ' '.$persons[$motherid];
                }
                $mother = 'Мать: '.$mother;
            }
    
            $fatherid = $row['father'];
            $father = '';
            if ($fatherid != 0) {
                $father = '#'.$fatherid;
                if (isset($persons[$fatherid])) {
                  $father .= ' '.$persons[$fatherid];
                }
                $father = 'Отец: '.$father;
            }

            $title = '';
            $biography = '';
            if ($row['private'] == 'yes') {
              $title = $row['firstname'];
            } else {
              $title = $row['fullname'];
              $biography = '<a href="#" class="card-link">Биография</a>';
            }

            echo '<br><div class="card">
              <div class="card-body">
                <h5 class="card-title">#'.$personid.' '.$title.'</h5>
                <h6 class="card-subtitle mb-2 text-muted">Годы жизни: '.$row['bornyear'].' - '.$row['yearofdeath'].'</h6>
                <p class="card-text">'.$mother.'</p>
                <p class="card-text">'.$father.'</p>
                '.$biography.'
              </div>
            </div>';
        }
    ?>
      </div>
  </body>
</html>