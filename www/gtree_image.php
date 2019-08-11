<?php
date_default_timezone_set('UTC');
$curdir_gtimg = dirname(__FILE__);
include_once($curdir_gtimg."/gtree.php");

class GTreeImage {
    static function generate() {

        // father / mother
        $conn = GTree::dbConn();
        $gt = array();
        $stmt = $conn->prepare('SELECT * FROM persons ORDER BY bornyear');
        $stmt->execute(array());
        while ($row = $stmt->fetch()) {
            if ($row['bornyear'] == 0) {
                continue;
            }

            $personid = intval($row['id']);
            $lastname = $row['lastname'];
            if ($row['bornlastname'] != '') {
                $lastname = $row['bornlastname'];
            }

            if ($row['private'] == 'yes') {
                $lastname = '';
            }

            $gt[$personid] = array(
                'firstname' => $row['firstname'],
                'lastname' => $lastname,
                'bornyear' => intval($row['bornyear']),
                'bornyear_notexactly' => $row['bornyear_notexactly'],
                'yearofdeath' => intval($row['yearofdeath']),
                'yearofdeath_notexactly' => $row['yearofdeath_notexactly'],
                'mother' => intval($row['mother']),
                'father' => intval($row['father']),
                'gtline' => intval($row['gtline']),
                'sex' => $row['sex'],
            );
        }

        $gtree_maxyear = GTree::getMaxBornYear();
        $gtree_minyear = GTree::getMinBornYear();
        $gtree_width = GTree::calculateWidth();
        $gtree_height = GTree::calculateHeight();
        
        $curdir_gtimg = dirname(__FILE__);
        $font = $curdir_gtimg.'/font/Dutch801 Rm Win95BT.ttf';
        // $font = $curdir_gtimg.'/font/17255.ttf';
        
        $my_img = imagecreate( $gtree_width, $gtree_height );
        $background = imagecolorallocate( $my_img, 255, 255, 255 );
        $text_color = imagecolorallocate( $my_img, 0, 0, 0 );
        $line_color = imagecolorallocate( $my_img, 89, 89, 89 );
        imagecolortransparent($my_img, $background);

        $female_card = imagecreatefrompng($curdir_gtimg.'/images/female_card.png');
        $male_card = imagecreatefrompng($curdir_gtimg.'/images/male_card.png');
        
        imagesetthickness ( $my_img, 3 );
        imageline( $my_img,
            GTree::$gtree_padding, GTree::$gtree_padding + 25,
            $gtree_width - GTree::$gtree_padding, GTree::$gtree_padding + 25,
        $line_color );
        
        for ($y = $gtree_maxyear; $y >= $gtree_minyear; $y = $y - 10) {
            $x1 = GTree::calcX_in_px($gtree_minyear, $y);
            $year_print = "".$y;
            // imagestring( $my_img, 25, $x1 + 6, 15, $year_print, $text_color );
            imagettftext( $my_img, 16, 0, $x1 + 6, 30, $text_color, $font, $year_print);

            imageline( $my_img,
                $x1, GTree::$gtree_padding + 10,
                $x1, GTree::$gtree_padding + 30,
            $line_color );
        }
        
        imageline( $my_img,
            $x1, GTree::$gtree_padding + 10,
            $x1, GTree::$gtree_padding + 30,
        $line_color );

        imagesetthickness ( $my_img, 2 );

        $el_r = 6;
        // parents
        foreach ($gt as $id => $p) {
          
          if ($p['mother'] > 0 && $p['father'] > 0) {
                $mo = $gt[$p['mother']];
                $fa = $gt[$p['father']];
                
                $mo_x1 = GTree::calcX_in_px($gtree_minyear, $mo['bornyear']);
                $mo_y1 = GTree::$gtree_gtline_top + $mo['gtline'] * GTree::$gtree_gtline;

                $fa_x1 = GTree::calcX_in_px($gtree_minyear, $fa['bornyear']);
                $fa_y1 = GTree::$gtree_gtline_top + $fa['gtline'] * GTree::$gtree_gtline;

                $x1 = GTree::calcX_in_px($gtree_minyear, $p['bornyear']);
                $y1 = GTree::$gtree_gtline_top + $p['gtline'] * GTree::$gtree_gtline;

                $mo_x1 += GTree::$gtree_card_width;
                $mo_y1 += GTree::$gtree_card_height / 2;
                $fa_x1 += GTree::$gtree_card_width;
                $fa_y1 += GTree::$gtree_card_height / 2;
                $y1 += GTree::$gtree_card_height / 2;

                $x2 = max($mo_x1, $fa_x1) + 20;
                $y2 = ($fa_y1 + $mo_y1) / 2;
                $x3 = $x2 + 30;

                imagefilledellipse( $my_img, $mo_x1, $mo_y1, $el_r, $el_r, $line_color );
                imagefilledellipse( $my_img, $fa_x1, $fa_y1, $el_r, $el_r, $line_color );
                imageline( $my_img, $mo_x1, $mo_y1, $x2, $mo_y1, $line_color );
                imageline( $my_img, $x2, $mo_y1, $x2, $fa_y1, $line_color );
                imageline( $my_img, $x2, $fa_y1, $fa_x1, $fa_y1, $line_color );
                
                // imagefill($image, 0, 0, $line_color);
                imagefilledellipse( $my_img, $x2, $y2, $el_r, $el_r, $line_color );
                imagefilledellipse( $my_img, $x3, $y2, $el_r, $el_r, $line_color );

                imagefilledellipse( $my_img, $x1, $y1, $el_r, $el_r, $line_color );
                imagefilledellipse( $my_img, $x3, $y1, $el_r, $el_r, $line_color );
                imageline( $my_img, $x2, $y2, $x3, $y2, $line_color );
                imageline( $my_img, $x3, $y2, $x3, $y1, $line_color );
                imageline( $my_img, $x3, $y1, $x1, $y1, $line_color );
              }
        }
        
        // cards
        foreach ($gt as $id => $p) {
            $x1 = GTree::calcX_in_px($gtree_minyear, $p['bornyear']);
            $y1 = GTree::$gtree_gtline_top + $p['gtline'] * GTree::$gtree_gtline;
            $x2 = $x1 + GTree::$gtree_card_width;
            $y2 = $y1 + GTree::$gtree_card_height;
            imagefilledrectangle($my_img, $x1, $y1, $x2, $y2, $line_color);
            imagefilledrectangle($my_img, $x1+1, $y1+1, $x2-1, $y2-1, $background);

            // imagecopymerge ( resource $dst_im , resource $src_im , int $dst_x , int $dst_y , int $src_x , int $src_y , int $src_w , int $src_h , int $pct ) : bool
            if ($p['sex'] == 'male') {
                imagecopy($my_img, $male_card, $x1, $y1-10, 0, 0, imagesx($male_card), imagesy($male_card));
            } else if ($p['sex'] == 'female') {
                imagecopy($my_img, $female_card, $x1, $y1-10, 0, 0, imagesx($female_card), imagesy($female_card));
            }

            $years_print = ''.$p['bornyear'];
            if ($p['bornyear_notexactly'] == 'yes') {
                $years_print .= '(пр.)';
            }

            if ($p['yearofdeath'] > 0) {
                $years_print .= ' - '.$p['yearofdeath'];
                if ($p['yearofdeath_notexactly'] == 'yes') {
                    $years_print .= '(пр.)';
                }
            }

            $t_x1 = $x1 + 15;
            $d = 18;
            $y1 = $y1 + $d;
            imagettftext($my_img, 12, 0, $t_x1, $y1, $text_color, $font, $years_print);
            $y1 = $y1 + $d;
            imagettftext($my_img, 14, 0, $t_x1 + 3, $y1, $text_color, $font, $p['firstname']);

            if (isset($p['lastname'])) {
              $y1 = $y1 + $d;
              imagettftext($my_img, 14, 0, $t_x1 + 3, $y1, $text_color, $font, $p['lastname']);
            }
        }


        header( "Content-type: image/png" );
        imagepng( $my_img, $curdir_gtimg.'/public/tree.png');
        imagecolordeallocate($my_img, $text_color );
        imagecolordeallocate($my_img, $line_color );
        imagecolordeallocate($my_img, $background );
        imagedestroy( $my_img );
    }
}


// GTreeImage::generate();


