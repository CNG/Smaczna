<$mt:Var name="hours_image_override" value="0"$>
<mt:EntryIfLinkedAssets field="hours_open_image"><$mt:Var name="hours_image_override" value="1"$></mt:EntryIfLinkedAssets>
<mt:EntryIfLinkedAssets field="hours_closed_image"><$mt:Var name="hours_image_override" value="1"$></mt:EntryIfLinkedAssets>
<mt:If name="hours_image_override"><mt:SetVarBlock name="html_header" append="1">
  <style type="text/css">
    <mt:EntryIfLinkedAssets field="hours_closed_image">#hours .closed { background:url(<mt:EntryLinkedAssets field="hours_closed_image"><$mt:AssetURL$></mt:EntryLinkedAssets>) no-repeat top center; }</mt:EntryIfLinkedAssets>
    <mt:EntryIfLinkedAssets field="hours_open_image">#hours .open { background:url(<mt:EntryLinkedAssets field="hours_open_image"><$mt:AssetURL$></mt:EntryLinkedAssets>) no-repeat top center; }</mt:EntryIfLinkedAssets>
  </style>
</mt:SetVarBlock></mt:If>
<mt:SetVarBlock name="hours_module">
<div id="hours" class="grid_5 alpha">
  <div class="holder">
    <h2><$mt:EntryFieldValue field="hours_banner_text"$></h2>
    <?php
      $ts = strtotime("-5 hours");
      $day = date("N",$ts);
      $time = date("His",$ts);
      $open = false;
      $hours = array(1,2,3,4,5,6,7);
      $hours[1] = array(<$mt:EntryFieldValue field="hours_mon_open"$>00,<$mt:EntryFieldValue field="hours_mon_close"$>00);
      $hours[2] = array(<$mt:EntryFieldValue field="hours_tue_open"$>00,<$mt:EntryFieldValue field="hours_tue_close"$>00);
      $hours[3] = array(<$mt:EntryFieldValue field="hours_wed_open"$>00,<$mt:EntryFieldValue field="hours_wed_close"$>00);
      $hours[4] = array(<$mt:EntryFieldValue field="hours_thu_open"$>00,<$mt:EntryFieldValue field="hours_thu_close"$>00);
      $hours[5] = array(<$mt:EntryFieldValue field="hours_fri_open"$>00,<$mt:EntryFieldValue field="hours_fri_close"$>00);
      $hours[6] = array(<$mt:EntryFieldValue field="hours_sat_open"$>00,<$mt:EntryFieldValue field="hours_sat_close"$>00);
      $hours[7] = array(<$mt:EntryFieldValue field="hours_sun_open"$>00,<$mt:EntryFieldValue field="hours_sun_close"$>00);
      switch ( $day ) {
        case 1: if ( $hours[1][0] <= $time && $time < $hours[1][1] ) { $open = true; } break;
        case 2: if ( $hours[2][0] <= $time && $time < $hours[2][1] ) { $open = true; } break;
        case 3: if ( $hours[3][0] <= $time && $time < $hours[3][1] ) { $open = true; } break;
        case 4: if ( $hours[4][0] <= $time && $time < $hours[4][1] ) { $open = true; } break;
        case 5: if ( $hours[5][0] <= $time && $time < $hours[5][1] ) { $open = true; } break;
        case 6: if ( $hours[6][0] <= $time && $time < $hours[6][1] ) { $open = true; } break;
        case 7: if ( $hours[7][0] <= $time && $time < $hours[7][1] ) { $open = true; } break;
      }
      function formatTime( $ts ) {
        $ts = $ts / 100; // remove seconds
        $m = $ts % 100; // store minutes
        $h = ( $ts - $m ) / 100; // store hours
        if ( $m != 0 ) return $h . ":" . str_pad( $m, 2, "0");
        else return $h;
      }
      $opentime = formatTime( $hours[$day][0] ) . " a.m.";
      $closetime = formatTime( $hours[$day][1] - 120000 ) . " p.m.";
      if($open)
      echo '<h3 class="open"><$mt:EntryFieldValue field="hours_open_header" encode_php="q"$></h3><p>'.str_replace('%',$closetime,'<$mt:EntryFieldValue field="hours_open_text" encode_php="q"$>').'</p>';
      else
      echo '<h3 class="closed"><$mt:EntryFieldValue field="hours_closed_header" encode_php="q"$></h3><p>'.str_replace('%',$opentime,'<$mt:EntryFieldValue field="hours_closed_text" encode_php="q"$>').'</p>';
    ?>
    <hr />
    <h4><$mt:EntryFieldValue field="hours_label"$></h4>
    <p><$mt:EntryFieldValue field="hours_line_1"$></p>
    <p><$mt:EntryFieldValue field="hours_line_2"$></p>
  </div>
</div><!-- #hours .grid_5 alpha -->
</mt:SetVarBlock>
