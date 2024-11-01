<?php
/*
Plugin Name: Slider Widget For Pinterest Pinboard
Description: Easy to add Slider Widget For Pinterest Pinboard on WordPress slidebar using widget.
Author: Akash soni
Version: 1.0
Author URI: http://www.akashsoni.com
* 
*/

function ac_add_plugin_style_scripts_frontend_side() {

    wp_enqueue_style( 'ac-owl-style', plugins_url( 'css/owl.carousel.css', __FILE__ ),array(),'1.0.0' );
    wp_enqueue_style( 'ac-font-awesome-style', plugins_url( 'css/font-awesome.min.css', __FILE__ ),array(),'1.0.0' );
    wp_enqueue_style( 'ac-animate-min-style', plugins_url( 'css/animate.min.css', __FILE__ ),array(),'1.0.0' );
    wp_enqueue_script( 'ac-owl-script', plugins_url( 'js/owl.carousel.js', __FILE__ ), array(), '1.0.0', true );
  
}
add_action( 'wp_enqueue_scripts', 'ac_add_plugin_style_scripts_frontend_side' );

function ac_add_plugin_style_scripts_admin_side() {

    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'ac-my-script-handle', plugins_url('js/ac_custome_script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );

}

add_action( 'admin_enqueue_scripts', 'ac_add_plugin_style_scripts_admin_side' );

class pinterest_slider_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
		'pinterest_slider_widget', 

		__('Pinterest Slider', 'pinterest_slider_data_load'), 

		array( 'description' => __( 'Widget for show pinterest slider in sidebar', 'pinterest_slider_data_load' ), ) 
		);
	}

	function widget( $args, $instance ) {

			extract( $args );
			$uniqe_id = rand();
			$instance = wp_parse_args( ( array ) $instance, array( 'title'=>'Pinterest Slider', 'pinterest_boardname'=>'Enter Pintrest Board Name', 'pinterest_username'=>'Enter Pintrest Username', 'pinterest_quantity'=>'12' ,'next_preview_button_color'=>'#000000','next_preview_button_arrow_color'=>'#ffffff' , 'slider_animation' => 'flash' , 'slider_column' => 1 ) );
			echo $before_widget;
			echo '<h2 class="widget-title">'.$instance['title'].'</h2>';

			if(empty($instance['pinterest_username'])){
				echo "<p>Please enter your pintrest username in widget</p>";
				return;			
			}
			
			if(!empty($instance['pinterest_boardname']) && $instance['pinterest_boardname'] != "Enter Pintrest Board Name"){
				$output = file_get_contents("https://api.pinterest.com/v3/pidgets/boards/" . $instance['pinterest_username'] . "/" . $instance['pinterest_boardname'] . "/pins/");
			}else{
				$output = file_get_contents("https://api.pinterest.com/v3/pidgets/users/" . $instance['pinterest_username'] . "/pins/");
			}
	        $data_have=false;
	        if ( !empty( $output ) ) {
				$pins_data=json_decode($output);		
				$pin_have=count($pins_data->data->pins);
				if($pin_have!=0){
					$data_have=true;
				}
			}	
			if ( $data_have==true ) {
						$pins_array=[];
						$count=1;
						foreach($pins_data->data->pins as $pins ){	
							$image_url="";
							foreach($pins->images as $images ){
								$image_url=$images->url;
							}				
							if(!empty($image_url) && @getimagesize($image_url)){
								if($count<=$instance['pinterest_quantity']){
									$pins_array[]=array($pins->id,$image_url,$pins->pinner->about,$pins->description);
									$count++;
								}
								
							}
						} 
				
				
					/* Css for slider */
					echo '<style>
						#ac-owl-slider-'.$uniqe_id.' .owl-item div{
						  padding:5px;
						}
						#ac-owl-slider-'.$uniqe_id.' .owl-item img{
						  display: block;
						  width: 100%;
						  height: auto;
						  -webkit-border-radius: 3px;
						  -moz-border-radius: 3px;
						  border-radius: 3px;
						}
						#ac-owl-slider-'.$uniqe_id.' .owl-buttons .owl-prev{
							left:0px;
						}	
						#ac-owl-slider-'.$uniqe_id.' .owl-buttons .owl-next{
							right:0px;
						}	
						#ac-owl-slider-'.$uniqe_id.' .owl-controls .owl-buttons [class*="owl-"] {
						  border-radius: 3px;
						  text-align: right;
						  cursor: pointer;
						  display: inline-block;
						  font-size: 14px;
						  margin: 5px;
						  background-color: '.$instance['next_preview_button_color'].' !important;
						  color: '.$instance['next_preview_button_arrow_color'].' !important;
						  padding: 4px 7px;
						  position: absolute;
						  top: 50%;
						  margin-top: -14px;
						  font-size: 20px;
						}
							</style>';
				
				echo '<div class="content"><div class="pinterest-slider">';
							
					echo '<div id="ac-owl-slider-'.$uniqe_id.'" class="owl-carousel">';
					
						foreach($pins_array as $pin){
							?>
								<div>
									<a target="_blank" href='<?php echo "https://in.pinterest.com/pin/".$pin[0]."/"; ?> '>					
										<img src="<?php echo $pin[1]; ?>" ss="<?php echo $pin[2]; ?>"  >
									</a>
								</div>
							<?php
						}
					echo " </div>";				
				echo "</div>";

				/* Script for slider create */
				$slider_animation=$instance['slider_animation'];
				echo 	"<script>
							jQuery(document).ready(function($){
									$('#ac-owl-slider-".$uniqe_id."').owlCarousel({						
										autoPlay 		: 3000,
										stopOnHover 	: true,
										navigation		:true,
										paginationSpeed : 1000,
										goToFirstSpeed 	: 2000,
										singleItem 		: true,";
								if( $slider_animation != "Select Effect" ){
								echo 	"animateIn 		: '".$slider_animation."',";
								echo 	"animateOut		: '".$slider_animation."',";
								}
								echo 	"autoHeight 		: true,
										navigationText	:['<i class=\"fa fa-angle-left\"></i>','<i class=\"fa fa-angle-right\"></i>'],
										items 			:".$instance['slider_column']."
									});
							});
						</script>";
				
			}else{
				echo "<p>Something wrong detail enter in widget.</p>";
			}
			echo 	$after_widget;
	}
		
	function form( $instance ) {

			$instance = wp_parse_args( ( array ) $instance, array( 'title'=>'Pinterest Slider', 'pinterest_boardname'=>'Enter Pintrest Board Name', 'pinterest_username'=>'Enter Pintrest Username', 'pinterest_quantity'=>'12' ,'next_preview_button_color'=>'#000000','next_preview_button_arrow_color'=>'#ffffff' , 'slider_animation' => 'flash' , 'slider_column' => 1 ) );

			$title 								= htmlspecialchars( $instance['title'] );
			$pinterest_username 				= ( $instance['pinterest_username'] );
			$pinterest_quantity 				= ( $instance['pinterest_quantity'] );
			$pinterest_boardname 				= ( $instance['pinterest_boardname'] );
			$slider_animation 					= ( $instance['slider_animation'] );
			$next_preview_button_color 			= ( $instance['next_preview_button_color'] );
			$next_preview_button_arrow_color 	= ( $instance['next_preview_button_arrow_color'] );
			$slider_column 						= ( $instance['slider_column'] );


			echo '<p style="text-align:left;"><label for="' . $this->get_field_name( 'title' ) . '">' . __( 'Title:' ) . '</label><br />
					<input style="width: 100%;" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" type="text" value="' . $title . '" /></p>';

			echo '<p style="text-align:left;"><label for="' . $this->get_field_name( 'pinterest_username' ) . '">' . __( 'Pinterest Username:' ) . '</label><br />
					<input style="width: 100%;" id="' . $this->get_field_id( 'pinterest_username' ) . '" name="' . $this->get_field_name( 'pinterest_username' ) . '" type="text" value="' . $pinterest_username . '" /></p>';

			echo '<p style="text-align:left;"><label for="' . $this->get_field_name( 'pinterest_boardname' ) . '">' . __( 'Pinterest boardname:' ) . '</label><br />
					<input style="width: 100%;" id="' . $this->get_field_id( 'pinterest_boardname' ) . '" name="' . $this->get_field_name( 'pinterest_boardname' ) . '" type="text" value="' . $pinterest_boardname . '" /></p>';
					
			echo '<p style="text-align:left;"><label for="' . $this->get_field_name( 'next_preview_button_color' ) . '">' . __( 'Next & Preview button color:' ) . '</label><br />
					<input style="width: 100%;" id="' . $this->get_field_id( 'next_preview_button_color' ) . '" name="' . $this->get_field_name( 'next_preview_button_color' ) . '" type="text" class="my-input-class"  value="' . $next_preview_button_color . '" /></p>';
			
			echo '<p style="text-align:left;"><label for="' . $this->get_field_name( 'next_preview_button_arrow_color' ) . '">' . __( 'Next & Preview button arrow color:' ) . '</label><br />
					<input style="width: 100%;" id="' . $this->get_field_id( 'next_preview_button_arrow_color' ) . '" name="' . $this->get_field_name( 'next_preview_button_arrow_color' ) . '" type="text"  class="my-input-class"  value="' . $next_preview_button_arrow_color . '" /></p>';

			echo '<p style="text-align:left;"><label for="' . $this->get_field_name( 'pinterest_quantity' ) . '">' . __( '# to Show:' ) . '</label><br />
					<input style="width: 100%;" id="' . $this->get_field_id( 'pinterest_quantity' ) . '" name="' . $this->get_field_name( 'pinterest_quantity' ) . '" type="number" value="' . $pinterest_quantity . '" min="0" max="50" /></p>';
			$animations=array('Select Effect','bounce','flash','pulse','rubberBand','shake','swing','tada','wobble','jello',
								'bounceIn','bounceInDown','bounceInLeft','bounceInUp','bounceInRight',
								'bounceOut','bounceOutDown','bounceOutLeft','bounceOutRight','bounceOutUp',
								'fadeIn','fadeInDown','fadeInDownBig','fadeInLeft','fadeInLeftBig','fadeInRight','fadeInRightBig','fadeInUp','fadeInUpBig',
								'flip','flipInX','flipInY','flipOutX','flipOutY','lightSpeedIn','lightSpeedOut','fadeInUp','fadeInUpBig',
								'rotateIn','rotateInDownLeft','rotateInDownRight','rotateInUpLeft','rotateInUpRight','rotateOut','rotateOutDownLeft',
								'rotateOutDownRight','rotateOutUpLeft','rotateOutUpRight','rotateInUpLeft','rotateInUpRight','slideInUp','slideInDown',
								'slideInLeft','slideInRight','slideOutUp','slideOutDown','slideOutLeft','slideOutRight','zoomIn','zoomInDown',
								'zoomInLeft','zoomInRight','zoomInUp','zoomOut','zoomOutDown','zoomOutLeft','zoomOutRight','zoomOutUp','hinge','rollIn','rollOut'
						);

			echo '<p style="text-align:left;"><label for="' . $this->get_field_name( 'slider_animation' ) . '">' . __( 'Slider Animation:' ) . '</label><br />
			<select name="' . $this->get_field_name( 'slider_animation' ) .'" id="' . $this->get_field_id( 'slider_animation' ) .' ">';
			foreach($animations as $animation){
				echo '<option value="'.$animation.'"';
				if($slider_animation==$animation){
					echo 'selected';
				}
				echo '>'.$animation.'</option>';
			}
			echo'</select>';
			
			$slides=array('1','2','3');
			echo '<p style="text-align:left;"><label for="' . $this->get_field_name( 'slider_column' ) . '">' . __( 'Slider Column:' ) . '</label><br />
			<select name="' . $this->get_field_name( 'slider_column' ) .'" id="' . $this->get_field_id( 'slider_column' ) .' ">';
			foreach($slides as $slide){
				echo '<option value="'.$slide.'"';
				if($slide==$slider_column){
					echo 'selected';
				}
				echo '>'.$slide.'</option>';
			}
			echo'</select>';
			
			echo '<p><b>Note</b>( maximum 50 pins show in slider. )</p>';
		
	}
	// Updating widget replacing old  data instances with new
	function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] = strip_tags( stripslashes( $new_instance['title'] ) );
			$instance['pinterest_username'] = strip_tags( stripslashes( $new_instance['pinterest_username'] ) );
			$instance['pinterest_quantity'] = strip_tags( stripslashes( $new_instance['pinterest_quantity'] ) );
			$instance['pinterest_boardname'] = strip_tags( stripslashes( $new_instance['pinterest_boardname'] ) );
			$instance['slider_animation'] = strip_tags( stripslashes( $new_instance['slider_animation'] ) );
			$instance['next_preview_button_color'] = strip_tags( stripslashes( $new_instance['next_preview_button_color'] ) );
			$instance['next_preview_button_arrow_color'] = strip_tags( stripslashes( $new_instance['next_preview_button_arrow_color'] ) );
			$instance['slider_column'] = strip_tags( stripslashes( $new_instance['slider_column'] ) );
			return $instance;
		}
} 

function ac_pinterest_slider_load() {
	register_widget( 'pinterest_slider_widget' );
}
add_action( 'widgets_init', 'ac_pinterest_slider_load' );

