<?php

if($SystemStatus !== 1){
	header('Location: inbox.php');
}else{
	header('Location: maintenance.php');
}	