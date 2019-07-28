<!doctype html>
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
                <a class="nav-link" href="persons.php">Дерево</a>
              </li>
              <li class="nav-item active">
                <a class="nav-link" href="persons.php">Персоны</a>
              </li>
            </ul>
          </div>
        </nav>
    <div id="gen_tree" class="genealogical-tree">
      <div class="gt-title">Генеалогическое древо</div>
      <div class="gt-years" id="gt_years"></div>
      <div class="gt-persons" id="gt_persons"></div>
    </div>
    <script>
      var gt_years_start = 1900;
      var gt_years_end = 2050;
      var gt_years_step = 10; // in years
      var gt_years_step_px = 100; // in px
      var gt_years_padding = 20;
      var gt_years_width = ((gt_years_end - gt_years_start) / gt_years_step) * gt_years_step_px + gt_years_step_px + 2*gt_years_padding;

      function calcX_in_px(year) {
        var x = year - gt_years_start;
        var k = gt_years_step_px / gt_years_step;
        var x = x*k + gt_years_padding;
        return x;
      }
      // gt_years
      $('#gt_years').html('');
      $('#gt_years').css({'width': gt_years_width + 'px'});
      for (var i = gt_years_end; i >= gt_years_start; i = i - gt_years_step) {
        $('#gt_years').append('<div class="gt-year-mark" style="left: ' + calcX_in_px(i) + 'px">' + i + ' г.</div>');
      }

      for (var i = 0; i < gt.length; i++) {
        var p = gt[i];
        var years_of_life = '' + p.born_year;
        if (p.year_of_death) {
          years_of_life += ' - ' + p.year_of_death;
        }
        var title = p.name;
        p.left = calcX_in_px(p.born_year);
        
        $('#gt_persons').append(''
          + '<div class="gt-person" style="left: ' + p.left + 'px; top: ' + p.top + 'px">'
          + '   <div class="gt-person-years-of-life">'  + years_of_life + '</div>'
          + '   <div class="gt-person-name">'  + title + '</div>'
          + '</div>'
        );
      }
      
    </script>
  </body>
</html>