<?php 
/*
Plugin Name: BMoney Content Tabs
Plugin URI: http://infomedia.com/
Description: Content Tabs!
Version: 1.1
Author: Brian DiChiara
Author URI: http://www.briandichiara.com
*/

define('BMCT_PLUGIN_VERSION', '1.1');

define('BMCT_PLUGIN_DIR', plugin_dir_url( __FILE__ ));
define('BMCT_PLUGIN_PATH', dirname(__FILE__));

add_action( 'init', 'bmoney_content_tabs_init', 11 );

function bmoney_content_tabs_init() {

	add_filter('the_content', 'bmoney_content_tabs', 99); // TODO: make priority an option
	add_filter('widget_text', 'bmoney_content_tabs_widget', 1);
}


function bmoney_content_tabs($content){
	
	if(strpos($content, '#tab"') !== false){ // do we have any tabs?
		
		wp_enqueue_style( 'bmoney-content-tabs',  BMCT_PLUGIN_DIR . 'css/bmoney-content-tabs.css', array(), BMCT_PLUGIN_VERSION);
		wp_enqueue_script( 'bmoney-content-tabs',  BMCT_PLUGIN_DIR . 'js/jquery.contenttabs.js', array('jquery'), BMCT_PLUGIN_VERSION);
		wp_enqueue_script( 'bmoney-content-tabs-init',  BMCT_PLUGIN_DIR . 'js/bmoney-content-tabs.js', array('jquery','bmoney-content-tabs'), BMCT_PLUGIN_VERSION);
		
		require_once(BMCT_PLUGIN_PATH . '/simple_html_dom.php');
		
		$html = str_get_html($content);
		$headings = $html->find('a[href$="#tab"]');
		
		if(count($headings) > 1){
			$tabs = array();
			$first = true;
			$current_url = $_SERVER['REQUEST_URI'];
			
			foreach($headings as $heading){
				$i=0; // get to top level element
				$href = $heading->href;
				$href = str_replace(site_url(), '', $href);
				$href = str_replace('#tab', '', $href);
				
				while( !is_null($heading->parent()) ){
					$tab = $heading->parent();
					$i++;if($i == 5){ break; } // let's make sure we don't end up in an infinite loop
				}
				
				$id = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $tab->plaintext));
				$tabs[$id] = array(
					'href' => $href,
					'label' => $tab->plaintext
				);
				
				$tab->outertext = ''; // we no longer need this heading
				if(!$first){
					$tab->outertext .= '</div>'; // close last bit of content
				}
				$tab->outertext .= '<div id="tab-content-'.$id.'" class="tab-content">'; // open div for next bit of content
				$tab->outertext .= '<a name="tab-content-'.$id.'"></a>'; // add an anchor for safekeeping
				$first = false; // no longer first!
			}
			
			if(count($tabs) > 0){ // build tab ul menu
				$tab_html = '<ul class="content-tabs">';
				foreach($tabs as $id => $tab){
					$tab_url = '#tab-content-'.$id;
					if($tab['href'] && $current_url != $tab['href']){
						$tab_url = $tab['href'];
					}
					$tab_html .= '<li><a href="'.$tab_url.'">'.$tab['label'].'</a></li>';
				}
				$tab_html .= '</ul>';
				
				$tab_content = $html->outertext;
				
				$html->outertext = $tab_html;
				$html->outertext .= '<div class="tab-content-wrapper">'; // wrap ALL the tabs in 2 divs
				$html->outertext .= '<div class="tab-content-container">';
				$html->outertext .= $tab_content;
				$html->outertext .= '</div></div></div>'; // close last tab content, container, and wrapper
			}
			
			$content = $html->outertext; // overwrite content with newly altered content
		}
	} else {
		echo '<!-- no tabs se�or... -->';
	}
	return $content;
}


function bmoney_content_tabs_widget($text){
	return bmoney_content_tabs($text);
}
