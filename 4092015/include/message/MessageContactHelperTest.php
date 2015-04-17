<?php
require_once 'message/MessageHelperTest.php';

/**
 * @author FortyDegrees 3
 */
class MessageContactHelperTest {
	var $helper;
	var $userId;
	var $type;

	function MessageContactHelperTest($id, $type) {
		$this->helper = new MessageHelperTest();
		$this->userId = $id;//$_SESSION['user_id'];
		$this->type = $type;
	}

	/**
	 * Display all message list.
	 * @return multitype:object  All message
	 */
	function displayAll($offset = 0, $limit, $keyword = '') {
		$options = array('order_by'=>'message.from_date desc, message.id desc', 'group_by'=> 'contact_name', 'limit' => $offset.' , '. $limit);
		if (strlen($keyword) > 0) {
			$options['filter'] = $keyword;
		}
		return $this->helper->getAllFor($this->userId, $this->type, true, $options);
	}

	/**
	 * Display all unread message list.
	 * @return multitype:object All unread.
	 */
	function displayUnread($offset = 0, $limit, $keyword = '') {
		$options = array('order_by'=>'message.from_date desc, message.id desc', 'group_by'=> 'contact_name', 'limit' => $offset.' , '. $limit);
		if (strlen($keyword) > 0) {
			$options['filter'] = $keyword;
		}
		return $this->helper->getAllUnreadFor($this->userId, $this->type, $options);
	}

	/**
	 * Count all message
	 * @param string $keyword
	 * @return int $count
	 */
	function countAllMessage($keyword = '') {
		$options = array('order_by'=>'message.from_date desc', 'group_by'=> 'contact_name');
		if (strlen($keyword) > 0) {
			$options['filter'] = $keyword;
		}
		$count =  $this->helper->getAllFor($this->userId, $this->type,true, $options);
		return $count->numRows();
	}

	/**
	 * Count all unread message only.
	 * @param string $keyword
	 * @return int $count
	 */
	function countAllUnreadMessage($keyword = '') {
		$options = array('order_by'=>'message.from_date desc', 'group_by'=> 'contact_name', 'is_read' => false);
		if (strlen($keyword) > 0) {
			$options['filter'] = $keyword;
		}
		$count =  $this->helper->getAllFor($this->userId, $this->type,true, $options);
		return $count->numRows();
	}

	/**
	 * Page number list
	 * @param int $currPage
	 * @param int $numPages
	 * @return string
	 */
	function paginator($currentPage, $pages) {
		$pagination = '<ul>';
		if ($pages <= 6 ) {
			//display all page number without ellipsis
			for ( $ctr = 1 ; $ctr <= $pages ; $ctr++) {
				$pagination .= $this->currentPage($ctr, $currentPage);
			}
		}
		else {
			//determine the mid point
			$mid = ceil($pages/2);
			//if it's less than or equal  mid the ellipsis and the last page is on the right
			if ($currentPage <= $mid) {
				if ($currentPage < 3) {
					for ( $ctr = 1 ; $ctr <= 5 ; $ctr++) {
						$pagination .= $this->currentPage($ctr, $currentPage);
					}
				}
				else {
					//display the ellipsis and the first page on the left side
					if (($currentPage+2) > $pages) {
						for ($ctr = $pages-5; $ctr <= $pages; $ctr++) {
							$pagination .= $this->currentPage($ctr, $currentPage);
						}
					}
					else {
						for ($ctr= $currentPage - 2;$ctr <= ($currentPage+2);$ctr++){
							$pagination .= $this->currentPage($ctr, $currentPage);
						}
					}
				}
				$str = $this->pageStr($pages);
				$pagination .="<li> <span>...</span> </li>";
				$pagination .="<li> <a href=\"javascript:void(0);\">".$str."</a> </li>";
			}
			//if it's greater than mid the ellipsis and the last page is on the left
			else {
				$pagination .="<li> <a href=\"javascript:void(0);\">01</a> </li>";
				$pagination .="<li> <span>...</span> </li>";
				if (($currentPage+2) >= $pages) {
					for ($ctr = $pages-4; $ctr <= $pages; $ctr++){
						$pagination .= $this->currentPage($ctr, $currentPage);
					}
				}
				else{
					for ($ctr= $currentPage - 2;$ctr <= ($currentPage+2);$ctr++){
						$pagination .= $this->currentPage($ctr, $currentPage);
					}
				}
			}
		}
		return $pagination.'</ul>';
	}

	/**
	 * Paginator helper function
	 * @return string
	 */
	function currentPage($ctr, $currentPage) {
		//prepend 0 if less than 10.
		if ($page < 10) {
			$str = '0'.$ctr;
		}
		else {
			$str = $ctr;
		}

		if($currentPage == $ctr) {
			return "<li class=\"current\"> <span >".$str."</span> </li>";
		}
		else {
			return "<li> <a href=\"javascript:void(0);\">".$str."</a> </li>";
		}
	}
}