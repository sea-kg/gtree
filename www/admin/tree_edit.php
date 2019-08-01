<?php

$dir_persons = dirname(__FILE__);
include_once($dir_persons."/../gtree.php");
GTree::startAdminPage();

if (isset($_POST['update_gtlines_in_tree'])) {
  $data = json_decode($_POST['data'], true);
  $conn = GTree::dbConn();

  foreach ($data as $i => $r) {
    $stmt = $conn->prepare('UPDATE persons SET gtline = ? WHERE id = ?');
    $stmt = $stmt->execute(array(
      intval($r['gtline']),
      intval($r['id']),
    ));
  }
  echo "OK";
  return;
}

include_once("head.php");
?>
<br>
<div>
  <div class="btn btn-primary" id="save_tree">Сохранить измененное дерево</div>
</div>
<br>
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
        echo 'var gtree_maxgtline = '.$maxgtline." + 1;\r\n";
        
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
      var gtree_gtline_top = 50;

      gtree_width = gtree_width * gtree_yearstep + 15*gtree_yearstep + 2*gtree_padding;
      gtree_height = gtree_maxgtline * gtree_gtline + 2*gtree_padding + 100;
      
      for (var i in gt) {
        gt[i].highlight = false;
      }

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


      function update_gtree() {

        ctx.fillStyle = "white";
        ctx.fillRect(0, 0, gtree_width, gtree_height);
        ctx.strokeRect(0, 0, gtree_width, gtree_height);
        ctx.strokeStyle = "#E9F0E0";

        for (var y = 0; y <= gtree_maxgtline; y++) {
          var y1 = gtree_gtline_top + y * gtree_gtline;
          y1 = y1 - (gtree_gtline - gtree_card_height) / 2;

          ctx.beginPath();
          ctx.moveTo(0, y1);
          ctx.lineTo(gtree_width, y1);
          ctx.stroke();
          
          ctx.fillText('' + y, x1 + 3, 30);

          // console.log(y);
        }

        ctx.strokeStyle = "black";
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

          // console.log(y);
        }

        ctx.lineWidth = 1;
        // parents
        for (var i in gt) {
          var p = gt[i];
          
          if (p.mother > 0 && p.father > 0) {
            var mo = gt[p.mother];
            var fa = gt[p.father];
            
            var mo_x1 = calcX_in_px(mo.bornyear);
            var mo_y1 = gtree_gtline_top + mo.gtline * gtree_gtline;

            var fa_x1 = calcX_in_px(fa.bornyear);
            var fa_y1 = gtree_gtline_top + fa.gtline * gtree_gtline;

            var x1 = calcX_in_px(p.bornyear);
            var y1 = gtree_gtline_top + p.gtline * gtree_gtline;

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
          // console.log(p);
          var x1 = calcX_in_px(p.bornyear);
          var y1 = gtree_gtline_top + p.gtline * gtree_gtline;
          gt[i].x1 = x1;
          gt[i].y1 = y1;

          // fill          
          ctx.fillStyle = selectedCard == i ? "#E6ECDF" : "white";
          ctx.fillRect(x1, y1, gtree_card_width, gtree_card_height);
          ctx.fillStyle = "black";

          ctx.strokeRect(x1, y1, gtree_card_width, gtree_card_height);
          var d = 16;
          ctx.fillText('' + p.bornyear, x1 + 3, y1 + d);
          d += 16;
          ctx.fillText('' + p.firstname, x1 + 3, y1 + d);
          if (p.lastname) {
            d += 16;
            ctx.fillText('' + p.lastname, x1 + 3, y1 + d);
          }
        }
      }
      
      var selectedCard = -1;
      var movingEnable = false;

      canvas.onmouseover = function(event) {
          // var target = event.target;
          movingEnable = false;
      };

      canvas.onmouseout = function(event) {
          // var target = event.target;
          movingEnable = false;
      };

      canvas.onmouseup = function(event) {
        var target = event.target;
        if (movingEnable) {
          movingEnable = false;
        }
      }

      canvas.onmousedown = function(event) {
          var target = event.target;
          if (selectedCard >= 0) {
            // console.log(target);
            movingEnable = true;
          }
      };

      canvas.onmousemove = function(event) {
          var target = event.target;
          // console.log(event);
          var co = target.getBoundingClientRect();
          // console.log(co);
          var x0 = event.clientX - co.left;
          var y0 = event.clientY - co.top;

          if (movingEnable && selectedCard >= 0) {
            y0 = y0 - gtree_gtline_top;
            y0 = y0 / gtree_gtline;
            y0 = Math.floor(y0);
            // console.log(y0);
            if (gt[selectedCard].gtline != y0) {
              gt[selectedCard].gtline = y0;
              update_gtree();
            }
            return;
          }
          
          var changesExists = false;
          selectedCard = -1;
          for (var i in gt) {
            var x1 = gt[i].x1;
            var x2 = x1 + gtree_card_width;
            var y1 = gt[i].y1;
            var y2 = y1 + gtree_card_height;
            var res = false;

            if (x0 > x1 && x0 < x2 && y0 > y1 && y0 < y2) {
              res = true;
              target.style.cursor = 'pointer';
              selectedCard = i;
            }

            if (gt[i].highlight != res) {
              changesExists = true;
              gt[i].highlight = res;
            }
          }
          if (selectedCard < 0) {
            target.style.cursor = 'default';
          }
          if (changesExists) {
            update_gtree();
          }
      };

      update_gtree();


      $('#save_tree').unbind().bind('click', function() {
        var dataSend = [];
        for (var id in gt) {
          dataSend.push({
            "id": id,
            "gtline": gt[id].gtline,
          });
        }
        $.ajax({
          url: 'tree_edit.php',
          type: "POST",
          data: {
            "update_gtlines_in_tree": "",
            "data": JSON.stringify(dataSend),
          },
          // dataType: dataType
        }).done(function(data) {
          alert(data);
        })
      })
    </script>

    </tbody>
</table>

<?php



include_once("footer.php");

