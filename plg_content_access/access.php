<?php
/**
 * @copyright	Copyright (C) 2012 Ammonite Networks. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.ammonitenetworks.com
 * @author 		Chris French chris@ammonitenetworks.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import library dependencies
jimport('joomla.event.plugin');

class plgContentAccess extends JPlugin {

	// Constructor
	function __construct(&$subject, $params) {
		parent::__construct($subject, $params);
	}

	
	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{
		// Don't run this plugin when the content is being indexed
		//if ($context == 'com_finder.indexer') {
		//	return true;
		//}
		$app = &JFactory::getApplication();
		if ($app -> isAdmin())
			return;
		
		//access, this means they only have to authenicate ones, and not on each ID
		$session =& JFactory::getSession();
		$dostuff = $session->get("hasaccess", "0");
		if($dostuff) return;
		
		if($this->params->get('menuorcontent')) {
			$ids = explode(',', $this->params->get('menuids'));
		//check if is a menu item

		$menu = &JSite::getMenu();
		$active = $menu -> getActive();
			$id = $active->id;
		} else {
			$ids = explode(',', $this->params->get('contentids'));
			$id = $row->id;
		
		}
		

		//check if is a menu item

		$menu = &JSite::getMenu();
		$active = $menu -> getActive();
		
		if(in_array($id, $ids)){
			$this->checkLogin();
			ob_start();
			include 'tmpl/default.php';
			$html = ob_get_contents();
			ob_end_clean();
			$row->text = $html;
		}
	}
	
	public function checkLogin() {
		 $post = JRequest::get( 'post' );
		if(isset($post['access_username'])) {
			
			if(trim($post['access_username']) == $this->params->get('access_username')){
				$username = TRUE;
				//do redirect
			}
			if(trim($post['access_password']) == $this->params->get('access_password')){
				$password = TRUE;
				//do redirect	
			}
			
			if($password && $username){
				
				$this->createSession();
				
			}
			
			
		} 
	}
	
	public function createSession() {
		$session =& JFactory::getSession();
		
		$session->set("hasaccess", "1");
	}

}