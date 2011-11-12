<?php
$zip = '<mt:EntryIfField field="weather_zip"><$mt:EntryFieldValue field="weather_zip" encode_php="q"$><mt:Else>53211</mt:EntryIfField>';

function json_cache($url) {
  $json_path = '/var/www/smaczna.us/www/rsc/json/';
  $cache_life = 60*7.5; //caching time, in seconds
  if (!file_exists($json_path.$url) || (time() - filemtime($json_path.$url) >= $cache_life)) {
    file_put_contents($json_path.$url,file_get_contents('http://api.wunderground.com/api/b6782bf90b6a98e0/conditions/q/IA/'.$url));
  } 
  return file_get_contents($json_path.$url);
}

function offset( $local_tz_short ){
  switch ( $local_tz_short ) {
    case "CDT" : return -5; break;
    case "CST" : return -6; break;
  }
}

function daylight( $lat, $lon ){
  $now = date( "Hi" );
  $set = date( "Hi", date_sunset( time(), SUNFUNCS_RET_TIMESTAMP, $lat, $lon, 90 ) );
  $rise = date( "Hi", date_sunrise( time(), SUNFUNCS_RET_TIMESTAMP, $lat, $lon, 90 ) );
  return ( $now > $rise ) && ( $now < $set );
}

function weather( $condition, $temp_f, $lat, $lon, $local_tz_short ) {
  $w = array( "img" => array( "src", "alt" ), "title", "subtitle", "patio_open" );
  $cond_lower = strtolower($condition);
  $cond_proper = substr_replace($cond_lower, strtoupper(substr($cond_lower, 0, 1)), 0, 1);
  $w['img']['alt'] = $cond_proper;
  $w['patio_open'] = false;
  /* Set title and subtitle for general conditions */
  switch ( $condition ) {
    case preg_match( "/Cloud/", $condition ) ? true : false :
      $w['patio_open'] = true;
      $w['title']      = '<$mt:EntryFieldValue field="weather_title_cloudy" encode_php="q"$>';
      $w['subtitle']   = '<$mt:EntryFieldValue field="weather_subtitle_cloudy" encode_php="q"$>';
      break;
    case preg_match( "/Thunderstorm/", $condition ) ? true : false :
      $w['title']      = '<$mt:EntryFieldValue field="weather_title_storms" encode_php="q"$>';
      $w['subtitle']   = '<$mt:EntryFieldValue field="weather_subtitle_storms" encode_php="q"$>';
      break;
    case preg_match( "/Snow/", $condition ) ? true : false :
      $w['title']      = '<$mt:EntryFieldValue field="weather_title_snow" encode_php="q"$>';
      $w['subtitle']   = '<$mt:EntryFieldValue field="weather_subtitle_snow" encode_php="q"$>';
      break;
    case preg_match( "/(Drizzle|Rain|Mist|Ice|Hail)/", $condition ) ? true : false :
      $w['title']      = '<$mt:EntryFieldValue field="weather_title_wet" encode_php="q"$>';
      $w['subtitle']   = '<$mt:EntryFieldValue field="weather_subtitle_wet" encode_php="q"$>';
      break;
    case preg_match( "/(Fog|Smoke|Ash|Dust|Sand|Haze|Spray)/", $condition ) ? true : false :
      $w['title']      = '<$mt:EntryFieldValue field="weather_title_particulate" encode_php="q"$>';
      $w['subtitle']   = '<$mt:EntryFieldValue field="weather_subtitle_particulate" encode_php="q"$>';
      break;
  }
  /* Set graphics for all conditions and remaining titles and subtitles */
  switch ( $condition ) {
    case preg_match( "/Clear/", $condition ) ? true : false :
      $w['patio_open'] = true;
      if( daylight( $lat, $lon ) ){
        $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_sunny"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
        $w['title']      = '<$mt:EntryFieldValue field="weather_title_daylight" encode_php="q"$>';
        $w['subtitle']   = '<$mt:EntryFieldValue field="weather_subtitle_daylight" encode_php="q"$>';
      } else {
        $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_moony"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
        $w['title']      = '<$mt:EntryFieldValue field="weather_title_nighttime" encode_php="q"$>';
        $w['subtitle']   = '<$mt:EntryFieldValue field="weather_subtitle_nighttime" encode_php="q"$>';
      } break;
    case preg_match( "/Overcast/", $condition ) ? true : false :
      $w['patio_open'] = true;
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_cloudy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      $w['title']      = '<$mt:EntryFieldValue field="weather_title_overcast" encode_php="q"$>';
      $w['subtitle']   = '<$mt:EntryFieldValue field="weather_subtitle_overcast" encode_php="q"$>';
      break;
    case preg_match( "/Partly Cloudy/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_cloudy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/Mostly Cloudy/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_cloudy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/Scattered Clouds/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_cloudy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Thunderstorm/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_rainy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Thunderstorms and Rain/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_rainy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Thunderstorms and Snow/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_rainy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Thunderstorms and Ice Pellets/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_rainy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Thunderstorms with Hail/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_rainy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Thunderstorms with Small Hail/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_rainy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Snow/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_snowy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Snow Grains/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_snowy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Low Drifting Snow/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_snowy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Blowing Snow/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_snowy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Snow Showers/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_rainy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Drizzle/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_rainy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Rain/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_rainy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Rain Mist/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_rainy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Rain Showers/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_rainy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Mist/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_rainy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Freezing Drizzle/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_rainy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Freezing Rain/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_rainy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Ice Crystals/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_rainy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Ice Pellets/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_rainy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Ice Pellet Showers/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_rainy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Hail/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_rainy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Hail Showers/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_rainy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Small Hail Showers/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_rainy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Freezing Fog/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_cloudy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Fog/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_cloudy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Smoke/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_cloudy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Volcanic Ash/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_cloudy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Widespread Dust/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_cloudy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Sand/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_cloudy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Haze/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_cloudy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Spray/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_cloudy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Dust Whirls/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_cloudy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Sandstorm/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_cloudy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Low Drifting Widespread Dust/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_cloudy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Low Drifting Sand/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_cloudy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Blowing Widespread Dust/", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_cloudy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
    case preg_match( "/(Light |Heavy )?Blowing Sand  /", $condition ) ? true : false :
      $w['img']['src'] = '<mt:EntryLinkedAssets field="weather_graphic_cloudy"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>';
      break;
  }
  /* Override title and subtitle if temperature issues */
  if( $temp_f < <mt:EntryIfField field="weather_threshold_cold"><$mt:EntryFieldValue field="weather_threshold_cold"$><mt:Else>50</mt:EntryIfField> ){
    $w['patio_open'] = false;
    $w['title']    = str_replace('%',round($temp_f,0)."&deg;F",'<$mt:EntryFieldValue field="weather_title_cold" encode_php="q"$>');
    $w['subtitle'] = str_replace('%',round($temp_f,0)."&deg;F",'<$mt:EntryFieldValue field="weather_subtitle_cold" encode_php="q"$>');
  } else if( $temp_f > <mt:EntryIfField field="weather_threshold_hot"><$mt:EntryFieldValue field="weather_threshold_hot"$><mt:Else>105</mt:EntryIfField> ){
    $w['patio_open'] = false;
    $w['title']    = str_replace('%',round($temp_f,0)."&deg;F",'<$mt:EntryFieldValue field="weather_title_hot" encode_php="q"$>');
    $w['subtitle'] = str_replace('%',round($temp_f,0)."&deg;F",'<$mt:EntryFieldValue field="weather_subtitle_hot" encode_php="q"$>');
  }
  return $w;
}

$json_string = json_cache($zip.".json");
$parsed_json = json_decode($json_string);
$lat = $parsed_json->{'current_observation'}->{'observation_location'}->{'latitude'};
$lon = $parsed_json->{'current_observation'}->{'observation_location'}->{'longitude'};
$local_tz_short = $parsed_json->{'current_observation'}->{'local_tz_short'};
$temp_f = $parsed_json->{'current_observation'}->{'temp_f'};
$wind_mph = $parsed_json->{'current_observation'}->{'wind_mph'};
$wind_gust_mph = $parsed_json->{'current_observation'}->{'wind_gust_mph'};
$condition = $parsed_json->{'current_observation'}->{'weather'};

?>
<div id="weather" class="grid_5">
  <div class="holder">
    <?php $w = weather( $condition, $temp_f, $lat, $lon, $local_tz_short ); $i = $w['img']; ?>
    <h2><img src="<?=$i['src'];?>" height="64" width="64" alt="<?=$i['alt'];?>" title="<?=$i['alt'];?>" /></h2>
    <p class="title"><?=$w['title'];?></p>
    <p class="subtitle"><?=$w['subtitle'];?></p>
    <?php
      if( $w['patio_open'] ){
        echo '<img id="weather-image" src="<mt:EntryLinkedAssets field="weather_image_open"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>" height="118" width="260" title="<$mt:EntryFieldValue field="weather_patio_open" encode_php="q"$>" alt="<$mt:EntryFieldValue field="weather_patio_open" encode_php="q"$>" />';
      } else {
        echo '<img id="weather-image" src="<mt:EntryLinkedAssets field="weather_image_closed"><$mt:AssetURL encode_php="q"$></mt:EntryLinkedAssets>" height="118" width="260" title="<$mt:EntryFieldValue field="weather_patio_closed" encode_php="q"$>" alt="<$mt:EntryFieldValue field="weather_patio_closed" encode_php="q"$>" />';
      }
    ?>
  </div>
</div><!-- #weather .grid_5 -->