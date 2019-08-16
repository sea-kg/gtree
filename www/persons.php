<?php
      include_once("gtree.php"); 
      include_once("head.php");
?>
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
            $yearsoflife = '';
            if ($row['bornyear'] > 0) {
              $yearsoflife = $row['bornyear'];
              if ($row['yearofdeath']) {
                $yearsoflife = 'Годы жизни: '.$yearsoflife.' - '.$row['yearofdeath'];
              } else {
                $yearsoflife = 'Год рождения: '.$yearsoflife;
              }
            }

            if ($row['private'] == 'yes') {
              $title = $row['firstname'];
            } else {
              $title = $row['fullname'];
              $biography = '<a href="./biography.php?uid='.$row['uid'].'" class="card-link">Биография</a>';
            }

            echo '<br><div class="card">
              <div class="card-body">
                <h5 class="card-title">#'.$personid.' '.$title.'</h5>
                <h6 class="card-subtitle mb-2 text-muted">'.$yearsoflife.'</h6>
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