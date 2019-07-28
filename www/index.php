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
    <script src="./js/index.js"></script>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <style>
.genealogical-tree {
    position: fixed;
    border: 1px solid black;
    left: 10px;
    width: calc(100% - 20px);
    height: calc(100% - 60px);
    overflow: scroll;
}
    </style>

    <title>Генеалогическое древо</title>
  </head>
  <body>
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
          <a class="navbar-brand" href="#">Генеологическое древо</a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
        
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
              <li class="nav-item active">
                <a class="nav-link" href="index.php">Дерево</a>
              </li>
              <li class="nav-item active">
                <a class="nav-link" href="persons.php">Персоны</a>
              </li>
            </ul>
          </div>
        </nav>
    <div class="genealogical-tree">
      <canvas id="gtree" width="500" height="500"></canvas>
    </div>
    <script>

    <?php
        // father / mother
        $conn = GTree::dbConn();
        $persons = array();
        $stmt = $conn->prepare('SELECT * FROM persons ORDER BY bornyear');
        $minyear = 5000;
        $maxyear = 0;
        $maxgtline = 0;
        $stmt->execute(array());
        while ($row = $stmt->fetch()) {
          if ($row['bornyear'] == 0) {
            continue;
          }
          
          $minyear = $row['bornyear'] < $minyear ? $row['bornyear'] : $minyear;
          $maxyear = $row['bornyear'] > $maxyear ? $row['bornyear'] : $maxyear;
          $maxgtline = $row['gtline'] > $maxgtline ? $row['gtline'] : $maxgtline;

          if ($row['monthofdeath'] > 0) {
            $maxyear = $row['monthofdeath'] > $maxyear ? $row['monthofdeath'] : $maxyear;
          }

          $personid = intval($row['id']);
          $lastname = $row['lastname'];
          if ($row['bornlastname'] != '') {
            $lastname = $row['bornlastname'];
          }

          if ($row['private'] == 'yes') {
            $lastname = '';
          }

          $persons[$personid] = array(
            'firstname' => $row['firstname'],
            'lastname' => $lastname,
            'bornyear' => intval($row['bornyear']),
            'mother' => intval($row['mother']),
            'father' => intval($row['father']),
            'gtline' => intval($row['gtline']),
          );
        }

        echo 'var gtree_minyear = '.$minyear.";\r\n";
        echo 'var gtree_maxyear = '.$maxyear.";\r\n";
        echo 'var gtree_maxgtline = '.$maxgtline.";\r\n";
        
        echo 'var gt = '.json_encode($persons, JSON_PRETTY_PRINT)."; \r\n";
    ?>
      gtree_minyear = gtree_minyear - gtree_minyear % 10;
      gtree_maxyear = gtree_maxyear - gtree_maxyear % 10 + 10;
      

      var gtree_padding = 10;
      var gtree_yearstep = 10;
      var gtree_width = gtree_maxyear - gtree_minyear;
      var gtree_card_width = 100;
      var gtree_card_height = 52;
      var gtree_gtline = 70;
      
      gtree_width = gtree_width * gtree_yearstep + 15*gtree_yearstep + 2*gtree_padding;
      gtree_height = gtree_maxgtline * gtree_gtline + 2*gtree_padding + 100;
      
      function calcX_in_px(year) {
        var ret = year - gtree_minyear; 
        ret = ret * gtree_yearstep + gtree_padding;
        return ret;
      }

      var canvas = document.getElementById("gtree");
      var ctx = canvas.getContext("2d");
      canvas.width  = gtree_width;
      canvas.height = gtree_height; 
      canvas.style.width  = gtree_width + 'px';
      canvas.style.height = gtree_height + 'px';

      ctx.fillStyle = "black";
      // ctx.fillRect(10, 10, 100, 100);
      ctx.lineWidth = 3;

      ctx.beginPath();
      ctx.moveTo(gtree_padding, gtree_padding + 25);
      ctx.lineTo(gtree_width - gtree_padding, gtree_padding + 25);
      ctx.stroke();

      ctx.font = "16px Arial";
      for (var y = gtree_maxyear; y >= gtree_minyear; y = y - 10) {
        x1 = calcX_in_px(y);

        ctx.beginPath();
        ctx.moveTo(x1, gtree_padding + 10);
        ctx.lineTo(x1, gtree_padding + 30);
        ctx.stroke();
        
        ctx.fillText('' + y, x1 + 3, 30);

        console.log(y);
      }

      ctx.lineWidth = 1;
      // parents
      for (var i in gt) {
        var p = gt[i];

        if (p.mother > 0 && p.father > 0) {
          var mo = gt[p.mother];
          var fa = gt[p.father];
          
          var mo_x1 = calcX_in_px(mo.bornyear);
          var mo_y1 = 50 + mo.gtline * gtree_gtline;

          var fa_x1 = calcX_in_px(fa.bornyear);
          var fa_y1 = 50 + fa.gtline * gtree_gtline;

          var x1 = calcX_in_px(p.bornyear);
          var y1 = 50 + p.gtline * gtree_gtline;

          mo_x1 += gtree_card_width;
          mo_y1 += gtree_card_height / 2;
          fa_x1 += gtree_card_width;
          fa_y1 += gtree_card_height / 2;
          y1 += gtree_card_height / 2;

          var x2 = Math.max(mo_x1, fa_x1) + 20;
          var y2 = (fa_y1 + mo_y1) / 2;
          var x3 = x2 + 30;

          ctx.beginPath();
          ctx.moveTo(mo_x1, mo_y1);
          ctx.lineTo(x2, mo_y1);
          ctx.lineTo(x2, fa_y1);
          ctx.lineTo(fa_x1, fa_y1);
          ctx.stroke();
          
          ctx.beginPath();
          ctx.moveTo(x2, y2);
          ctx.lineTo(x3, y2);
          ctx.lineTo(x3, y1);
          ctx.lineTo(x1, y1);
          ctx.stroke();
        }
      }

      // cards
      for (var i in gt) {
        var p = gt[i];
        console.log(p);
        var x1 = calcX_in_px(p.bornyear);
        var y1 = 50 + p.gtline * gtree_gtline;
        
        ctx.strokeRect(x1, y1, gtree_card_width, gtree_card_height);
        var d = 16;
        ctx.fillText('' + p.bornyear, x1 + 3, y1 + d);
        if (p.lastname) {
          d += 16;
          ctx.fillText('' + p.lastname, x1 + 3, y1 + d);
        }
        d += 16;
        ctx.fillText('' + p.firstname, x1 + 3, y1 + d);
      }

    </script>
  </body>
</html>