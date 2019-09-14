<?php

/*
    Plugin Name: Caroussel
    Author: The D
    Version: Alpha
    Description: Este es un plug in que usa bootstrap 3 para crear slideshows personalizados.
*/

//Database------------------------------
function createTableStructure(){
    global $wpdb;
    $charset_collate=$wpdb->get_charset_collate();
    $prefix=$wpdb->prefix."b3_";
    /*$sql="CREATE TABLE {$prefix}caroussel(
    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    title varchar(250),
    description text,
    slides int,    
    PRIMARY KEY  (id)       
    )$charset_collate;";            
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);   
    */
    $sql="CREATE TABLE {$prefix}diapositiva(
    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    title varchar(250),
    description text,
    url varchar(255),
    slide varchar(255),
    PRIMARY KEY  (id)    
    )$charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);
    
    /*$sql="CREATE TABLE {$prefix}caroussel_detail(    
    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    carousselid bigint(20) UNSIGNED NOT NULL,
    diapositivaid bigint(20) UNSIGNED NOT NULL,
    PRIMARY KEY  (id)    
    )$charset_collate;";    
    dbDelta($sql);  */
    
}
function addFirstSlideshow(){
    global $wpdb;
    $prefix=$wpdb->prefix."b3_";
    $address=plugin_dir_url(__FILE__);
    $home=get_bloginfo('url');
    
    //$wpdb->query("INSERT INTO {$prefix}caroussel(id,title,description,slides)         values(1,'Principal', 'Este es el slider principal', 0);");
    
    $wpdb->query("INSERT INTO {$prefix}diapositiva(id,title,description, url,slide)
    values(1,'Ejemplo', 'Esta es una diapositiva de ejemplo','{$home}', '{$address}/slide.png');");
    
    //$wpdb->query("INSERT INTO {$prefix}caroussel_detail values(1, 1 , 1)");    
}
register_activation_hook(__FILE__,'createTableStructure');
register_activation_hook(__FILE__,'addFirstSlideshow');
//------------------------------

//Admin Page----------------------------
function wporg_options_page_html() {
    wp_enqueue_media();
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap" ng-app="b3_caroussel">      
          <div class="container-fluid" ng-controller="mainSlider as ctrl">
              <div class="navbar-header">
                  <h1>
                  <?php echo get_admin_page_title();?> 
                  <button type="button" class="btn btn-primary" aria-label="Left Align" ng-click="ctrl.addSlide()">
                      <span class="glyphicon glyphicon-plus" aria-hidden="true"> 
                      </span>
                      Nueva diapositiva
                  </button>                  
                  </h1>                      
                  <br>                  
                  <input type="hidden" id="homeAddress" value="<?php echo get_home_url(); ?>">
                  <img src="<?php echo get_home_url()."/wp-content/plugins/caroussel4ursite/loading.gif"; ?>" alt="Loading" ng-show="ctrl.loadingSlides">                  
                  <br>                 
                  <div ng-show="!ctrl.loadingSlides">
                    <div ng-repeat="slide in ctrl.items">
                        <div class="panel panel-primary pan">
                           <div class="panel-heading clearfix">                 
                                <span class="pull-left" style="padding-top: 5px;">Imagen de la diapositiva</span>
                                  <div class="btn-group pull-right">
                                     <button aria-label="Left Align" class="btn btn-primary btn-sm" ng-click="ctrl.mediaLibrary($index)">
                                         <span class="glyphicon glyphicon-pencil"></span>
                                     </button>                                     <button aria-label="Left Align" class="btn btn-primary btn-sm" ng-click="ctrl.removeSlide($index)">
                                         <span class="glyphicon glyphicon-remove" ></span>
                                     </button>                                   
                                  </div>
                           </div>                                                 
                           <div class="panel-body">                           
                           <div class="row">
                               <div class="col-xs-3 col-md-3">                   
                                   <img ng-src="{{slide.slide}}" alt="Diapositiva" class="pic">         
                               </div>
                               <div class="col-xs-9 col-md-9">
                                   <label for="titulo">Titulo </label>
                                       <input type="text" class="form-control" ng-model="slide.title">     
                                       <label for="Description">Descripcion</label>    
                                       <textarea cols="30" rows="5" class="form-control" ng-model="slide.description"></textarea>                      
                                       <input type="text" class="form-control" ng-model="slide.url" style="margin-top:3px;">             
                               </div>
                           </div>
                           </div>                           
                        </div>
                    </div>
                    <br>
                     <button type="button" class="btn btn-success" aria-label="Left Align" ng-click="ctrl.saveChanges()">
                      <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"> 
                      </span>
                      Guardar
                  </button>                      
                  </div> 
              </div>
          </div>
      </div>          
    <?php
}
function wporg_options_page() {
    add_menu_page(
        'Configurar slider principal',
        'Slider principal',
        'manage_options',
        'slider_principal',
        'wporg_options_page_html',
        '',
        100
    );
}
add_action( 'admin_menu', 'wporg_options_page' );
//------------------------------

//REST API------------------------------
//Get elements
function getSlideshowSlides($data){
    global $wpdb;
    $prefix=$wpdb->prefix."b3_";
    $results= $wpdb->get_results("SELECT * FROM {$prefix}diapositiva");    
    if(empty($results))
    {
        return null;
    }
    return $results;    
} 
//Create elements
function postSlideshowSlides($data){
    global $wpdb;    
    $prefix=$wpdb->prefix."b3_";
    $resultArray=json_decode($data["request"], true);
    //$queryArray=array();
    
    foreach($resultArray as $key=>$value){
        /*$wpdb->query("INSERT INTO {$prefix}diapositiva(title, description, url, slide)
        values('{$value["title"]}', '{$value["description"]}', '{$value["url"]}', '{$value["slide"]}');");*/
        /*$queryArray[]="INSERT INTO {$prefix}diapositiva(title, description, url, slide)
        values('{$value["title"]}', '{$value["description"]}', '{$value["url"]}', '{$value["slide"]}');";*/
        $wpdb->query("INSERT INTO {$prefix}diapositiva(title, description, url, slide)
        values('{$value["title"]}', '{$value["description"]}', '{$value["url"]}', '{$value["slide"]}');");
    }
    
    return;    
}
//Delete elements
function deleteSlideshowSlides($data){
    global $wpdb;
    $prefix=$wpdb->prefix."b3_";
    $resultArray=json_decode($data["request"], true);        
    $queryArray=array();
    
    foreach($resultArray as $key=>$value){
        /*$wpdb->query("INSERT INTO {$prefix}diapositiva(title, description, url, slide)
        values('{$value["title"]}', '{$value["description"]}', '{$value["url"]}', '{$value["slide"]}');");*/
        /*$queryArray[]="DELETE FROM {$prefix}diapositiva WHERE id={$value["id"]};";*/
        $wpdb->query("DELETE FROM {$prefix}diapositiva WHERE id={$value["id"]};");
    }
    
    return $resultArray;
    
    
}
//Update elements
function putSlideshowSlides($data){
    global $wpdb;    
    $prefix=$wpdb->prefix."b3_";
    $resultArray=json_decode($data["request"], true);    
    $queryArray=array();
    
    foreach($resultArray as $key=>$value){
        /*$wpdb->query("INSERT INTO {$prefix}diapositiva(title, description, url, slide)
        values('{$value["title"]}', '{$value["description"]}', '{$value["url"]}', '{$value["slide"]}');");*/
        /*$wpdb->query("UPDATE TABLE {$prefix}diapositiva         
        set title= '{$value["title"]}', 
        description='{$value["description"]}',
        url= '{$value["url"]}', 
        slide= '{$value["slide"]}')
        WHERE id={$value["id"]};");
        */
        $wpdb->query("UPDATE {$prefix}diapositiva set title= '{$value["title"]}',
        description='{$value["description"]}',url= '{$value["url"]}',
        slide= '{$value["slide"]}'
        WHERE id={$value["id"]};");
    }
    
    return $queryArray;    
}
add_action( 'rest_api_init', function () {
  register_rest_route( 'b3caroussel/v1', '/slideshow', array(
    'methods' => 'GET',
    'callback' => 'getSlideshowSlides',
  ) );
} );
add_action( 'rest_api_init', function () {
  register_rest_route( 'b3caroussel/v1', '/slideshow', array(
    'methods' => 'POST',
    'callback' => 'postSlideshowSlides',
  ) );
} );
add_action( 'rest_api_init', function () {
  register_rest_route( 'b3caroussel/v1', '/slideshow', array(
    'methods' => 'DELETE',
    'callback' => 'deleteSlideshowSlides',
  ) );
} );
add_action( 'rest_api_init', function () {
  register_rest_route( 'b3caroussel/v1', '/slideshow', array(
    'methods' => 'PUT',
    'callback' => 'putSlideshowSlides',
  ) );
} );
//------------------------------

//Style-------------------------
function styleAdding(){
    wp_enqueue_style( 'my-js', plugin_dir_url(__FILE__).'node_modules/bootstrap/dist/css/bootstrap.css', false );
    wp_enqueue_style( 'b3_caroussel_theme', plugin_dir_url(__FILE__).'/b3_caroussel_theme.css', false );
}
add_action('wp_enqueue_scripts', 'styleAdding');
//------------------------------

//Shortcode---------------------
function b3_caroussel_shortcodes_init(){
    function b3_caroussel_shortcode(){
        global $wpdb;
        $content="";
        $diapositivas=$wpdb->get_results('
            SELECT * FROM wp_b3_diapositiva;            
        ');
        
        if(empty($diapositivas))
        {
            echo "No hay diapositivas";
            return null;
        }        
        //echo $wpdb->num_rows;
        $content.="<div  id='carousel-example-generic' class='carousel slide' data-ride='carousel'>";
        $content.=" <ol class='carousel-indicators'>";
        $content.="<li data-target='#carousel-example-generic' data-slide-to='0' class='active'></li>";
        for($i=1;$i<$wpdb->num_rows;$i++){
            $content.="<li data-target='#carousel-example-generic' data-slide-to='{$i}'></li>";
        }
        
        $content.="</ol>";
        $content.=" <div class='carousel-inner' role='listbox'>";
        $first=true;
        foreach($diapositivas as $diapositiva){
            $diapositivaURL=str_replace("-150x150","",$diapositiva->slide);
            if($first){
            $content.="
            <div class='item active'>
                <img class='b3_caroussel_img' src='{$diapositivaURL}' alt='Slide'>
                 <div class='carousel-caption'>
                    <a href='{$diapositiva->url}'><h1 class='b3_slide_title'>{$diapositiva->title}</h1></a>
                    <p>{$diapositiva->description}</p>
                 </div>
            </div>
            ";   
            $first=false;
            }else{
            $content.="
            <div class='item'>
                <img class='b3_caroussel_img' src='{$diapositivaURL}' alt='Slide'>
                 <div class='carousel-caption'>
                    <a href='{$diapositiva->url}'><h1 class='b3_slide_title'>{$diapositiva->title}</h1></a>
                    <p>{$diapositiva->description}</p>
                 </div>
            </div>
            ";
        }
        }
        $content.=" </div>";        
        $content.="
        <a class='left carousel-control' href='#carousel-example-generic' role='button' data-slide='prev'>
            <span class='glyphicon glyphicon-chevron-left' aria-hidden='false'></span>
            <span class='sr-only'>Previous</span>
        </a>
        <a class='right carousel-control' href='#carousel-example-generic' role='button' data-slide='next'>
            <span class='glyphicon glyphicon-chevron-right' aria-hidden='true'>
            </span>
            <span class='sr-only'>Previous</span>
        </a>
    </div>
        ";        
        echo $content;
        
        return $content;
    }
    add_shortcode('b3_caroussel', 'b3_caroussel_shortcode');
}
add_action('init', 'b3_caroussel_shortcodes_init');

//------------------------------


//Scipts------------------------------
function addScripts(){    
wp_enqueue_style('b3',plugin_dir_url(__FILE__).'node_modules/bootstrap/dist/css/bootstrap.css');    
wp_enqueue_style('b3_style',plugin_dir_url(__FILE__).'b3_caroussel_style.css');   
wp_enqueue_script('Jquery',plugin_dir_url(__FILE__).'node_modules/jquery/dist/jquery.js');     
wp_enqueue_script('angularjs',plugin_dir_url(__FILE__).'node_modules/angular/angular.js');     
wp_enqueue_script('ngResource',plugin_dir_url(__FILE__).'node_modules/angular-resource/angular-resource.js');
wp_enqueue_script('app',plugin_dir_url(__FILE__).'b3_caroussel_app.js');    
wp_enqueue_script('mediaLibrary',plugin_dir_url(__FILE__).'imgscript.js');    
}
add_action('admin_enqueue_scripts', 'addScripts');
//------------------------------



?>