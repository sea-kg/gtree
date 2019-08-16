<?php
      include_once("gtree.php"); 
      
      $uid = "";
      $personid = 0;
      $fullname = 0;
      $mother = 0;
      $father = 0;
      $private = 'yes';
      $yearsoflife = '';
      if (isset($_GET["uid"])) {
        $uid = $_GET["uid"];
      }

      $conn = GTree::dbConn();
      $stmt = $conn->prepare('SELECT * FROM persons WHERE uid = ?');
      $stmt->execute(array($uid));

      if ($row = $stmt->fetch()) {
        $personid = $row['id'];
        $fullname = $row['fullname'];
        $mother = $row['mother'];
        $father = $row['father'];
        $private = $row['private'];
        if ($private == 'yes') {
          $fullname = $row['firstname'];
        }
        
        $yearsoflife = $row['bornyear'];
        if ($row['bornyear_notexactly'] == 'yes') {
          $yearsoflife .=  ' (пр.)';
        }
        if ($row['yearofdeath'] != 0) {
          $yearsoflife .=  ' - '.$row['yearofdeath'];
          if ($row['yearofdeath_notexactly'] == 'yes') {
            $yearsoflife .=  ' (пр.)';
          }
        }

      } else {
        $personid = 0;
        $fullname = '?';
      }

      $biographies = array();
      $about_life_0 = '';

      $stmt = $conn->prepare('SELECT * FROM biographies WHERE personid = ?');
      $stmt->execute(array($personid));
      if ($row = $stmt->fetch()) {
          $biographies[] = array(
              'type' => $row['type'],
              'year' => $row['year'],
              'description' => $row['description'],
          );
          if ($row['type'] == 'about_life' && $row['year'] == 0) {
              $about_life_0 = $row['description'];
          }
      }

      include_once("head.php");
?>
      <div class="container">
      <br>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="persons.php">Персоны</a></li>
          <li class="breadcrumb-item active" aria-current="page">Биография персоны #<?php echo $personid.' '.$fullname; ?></li>
        </ol>
      </nav>

        <?php

        if ($personid == 0) {
          echo "<br><br><div class='alert alert-danger'>Персона не найдена</div>";
          exit;
        }

        echo '<br><div class="card">
          <div class="card-body">
            <h5 class="card-title"><strong>[person#'.$personid.'] '.$fullname.'</strong></h5>
            <h6 class="card-subtitle mb-2 text-muted">Годы жизни: '.$yearsoflife.'</h6>
            <!-- p class="card-text">'.$mother.'</p>
            <p class="card-text">'.$father.'</p -->
          </div>
        </div>';

        echo '<br><div class="card">
          <div class="card-body">
            <h5 class="card-title">О жизни</h5>
            <pre class="card-text">'.($private == 'yes' ? 'Данные скрыты' : $about_life_0).'</pre>
          </div>
        </div>';
    ?>
      </div>
  </body>
</html>