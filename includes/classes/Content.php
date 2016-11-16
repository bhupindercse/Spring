<?php

class Content
{

	public static function getMainNavMenu(){

		$connection = DBConn::getInstance();
		try
		{
			$statement = "SELECT * FROM ".Config::get('table_prefix')."sub_nav_categories
							WHERE
							parent_permalink IS NULL;";
			$content_results = array();
			$query = $connection->conn->prepare($statement);
			$query->execute();
			if($query->rowCount()){		
				return $query->fetchAll(PDO::FETCH_ASSOC);
			}	
		}catch(Exception $e){
			throw $e;
		}
	}

	
	public static function getEmployeeStatus($employee_id){

		$connection = DBConn::getInstance();
		try
		{
			$statement = "SELECT * FROM ".Config::get('table_prefix')."employee
							WHERE
							id = :employee_id;";
			$content_results = array();
			$query = $connection->conn->prepare($statement);
			$query->execute(array(":employee_id" => $employee_id));
			if($query->rowCount()){		
				return $query->fetch(PDO::FETCH_ASSOC);
			}	
		}catch(Exception $e){
			throw $e;
		}
	}

	public static function getSubNavMenu($category){

		$connection = DBConn::getInstance();
		try
		{
			$statement        = "SELECT * FROM ".Config::get('table_prefix')."sub_nav_categories WHERE parent_permalink = ?;";
			$query_vars   	  = array();
			$query_vars[0]    = $category;
			$query            = $connection->conn->prepare($statement);
			$query->execute($query_vars);
			if($query->rowCount()){
				return $query->fetchAll(PDO::FETCH_ASSOC);
			}
		}catch(Exception $e){
			throw $e;
		}
	}

	public static function getSubNavMenuForOtherLinks($category){

		$connection = DBConn::getInstance();
		try
		{
			$statement        = "SELECT * FROM ".Config::get('table_prefix')."dropdown_files WHERE parent_category = ?;";
			$query_vars   	  = array();
			$query_vars[0]    = $category;
			$query            = $connection->conn->prepare($statement);
			$query->execute($query_vars);
			if($query->rowCount()){
				return $query->fetchAll(PDO::FETCH_ASSOC);
			}
		}catch(Exception $e){
			throw $e;
		}
	}

	public static function getStaffAnnouncementsCategories(){

		$connection = DBConn::getInstance();
		try
		{
			$statement        = "SELECT * FROM ".Config::get('table_prefix')."staff_announcement_categories;";
			$query            = $connection->conn->prepare($statement);
			$query->execute();
			if($query->rowCount()){
				return $query->fetchAll(PDO::FETCH_ASSOC);
			}
		}catch(Exception $e){
			throw $e;
		}
	}


	public static function getQuickLinks(){

		$connection = DBConn::getInstance();
		try
		{
			$statement = "SELECT * FROM ".Config::get('table_prefix')."quick_links;";
			$content_results = array();
			$query = $connection->conn->prepare($statement);
			$query->execute();
			if($query->rowCount()){		
				return $query->fetchAll(PDO::FETCH_ASSOC);
			}	
		}catch(Exception $e){
			throw $e;
		}
	}

	public static function getFeaturesInfo(){

		$connection = DBConn::getInstance();
		try
		{
			$statement = "SELECT * FROM ".Config::get('table_prefix')."features_images;";
			$content_results = array();
			$query = $connection->conn->prepare($statement);
			$query->execute();
			if($query->rowCount()){		
				return $query->fetchAll(PDO::FETCH_ASSOC);
			}	
		}catch(Exception $e){
			throw $e;
		}
	}  

	public static function getReviewsInfo(){

		$connection = DBConn::getInstance();
		try
		{
			$statement = "SELECT * FROM ".Config::get('table_prefix')."reviews ORDER BY date_posted DESC LIMIT 3 ;";
			$content_results = array();
			$query = $connection->conn->prepare($statement);
			$query->execute();
			if($query->rowCount()){		
				return $query->fetchAll(PDO::FETCH_ASSOC);
			}	
		}catch(Exception $e){
			throw $e;
		}
	} 


	public static function getAllAbout(){
		$connection = DBConn::getInstance();
		try
		{
			$statement        = " SELECT * FROM ".Config::get('table_prefix')."about;";
			$query_vars   	  = array();
			$query            = $connection->conn->prepare($statement);
			$query->execute();
			if($query->rowCount()){
				return $query->fetchAll(PDO::FETCH_ASSOC);
			}
		}catch(Exception $e){
			throw $e;
		}
	} 



	public static function getStaffAnnouncement($id){

		$connection = DBConn::getInstance();
		try
		{
			$statement = "SELECT * FROM 
							".Config::get('table_prefix')."staff_announcements as sa 
							inner join 
							".Config::get('table_prefix')."employee as e 
							on 
							sa.employee_id = e.id WHERE sa.sa_id = :id;";

			$query = $connection->conn->prepare($statement);
			$query->execute(array(":id" => $id));
			
			if($query->rowCount()){
				return $query->fetch(PDO::FETCH_ASSOC);
			}
		}catch(Exception $e){
			throw $e;
		}
	}

	public static function getAllStaffAnnouncements(){

		$connection = DBConn::getInstance();
		try
		{
			$statement = "SELECT * FROM 
							".Config::get('table_prefix')."staff_announcements as sa 
							inner join 
							".Config::get('table_prefix')."employee as e 
							on 
							sa.employee_id = e.id ORDER BY sa.sa_date DESC;";

			$query     = $connection->conn->prepare($statement);
			$query->execute();
			if($query->rowCount()){
				return $query->fetchAll(PDO::FETCH_ASSOC);
			}
		}catch(Exception $e){
			throw $e;
		}
	}

	public static function getAllGalleries(){

		$connection = DBConn::getInstance();
		try
		{
			$statement = "SELECT * FROM 
							".Config::get('table_prefix')."gallery_albums ;";

			$query     = $connection->conn->prepare($statement);
			$query->execute();
			if($query->rowCount()){
				return $query->fetchAll(PDO::FETCH_ASSOC);
			}
		}catch(Exception $e){
			throw $e;
		}
	}

	public static function getGalleryDetails($id){

		$connection = DBConn::getInstance();
		try
		{
			$statement = "SELECT * FROM 
							".Config::get('table_prefix')."gallery 
							WHERE 
							id = :id;";

			$query = $connection->conn->prepare($statement);
			$query->execute(array("id" => $id));
			if($query->rowCount()){
				return $query->fetch(PDO::FETCH_ASSOC);
			}
		}catch(Exception $e){
			throw $e;
		}
	}

	public static function getStaffComments($id){
		$connection = DBConn::getInstance();
		try
		{
			$statement = "SELECT * FROM 
							".Config::get('table_prefix')."staff_announcement_comments as sac 
							inner join 
							".Config::get('table_prefix')."employee as e 
							on 
							sac.employee_id = e.id 
							WHERE
							sac.announcement_id = :id;";

			$query = $connection->conn->prepare($statement);
			$query->execute(array(":id" => $id));
			
			if($query->rowCount()){
				return $query->fetchAll(PDO::FETCH_ASSOC);
			}
		}catch(Exception $e){
			throw $e;
		}
	}

	public static function getNewEmployeeDetails(){

		$connection = DBConn::getInstance();
		try
		{
			$diffDate = date('Y/m/d', strtotime(' -15 day'));

			$statement = "SELECT * FROM ".Config::get('table_prefix')."employee as e 
							INNER JOIN ".Config::get('table_prefix')."departments as d 
							ON e.`department_Id` = d.`id`
							WHERE 
							e.startdate > ?";

			$content_results = array();
			$query_vars   	 = array();
			$query_vars[0]   = $diffDate;

			$query = $connection->conn->prepare($statement);
			$query->execute($query_vars);
				
			if($query->rowCount()){	
					return $query->fetchAll(PDO::FETCH_ASSOC);
				}
		}catch(Exception $e){
			throw $e;
		}
	}

	public static function getContent($permalink){

		$connection = DBConn::getInstance();

		try
		{
			$statement = "SELECT * FROM ".Config::get('table_prefix')."sub_nav_categories WHERE permalink = :permalink;";
			$query = $connection->conn->prepare($statement);
			$query->execute(array(":permalink" => $permalink));

			if(!$query->rowCount())
				throw new Exception("Unable to find the Content page you were looking for.");

			return $query->fetch(PDO::FETCH_ASSOC);
		}catch(Exception $e){
			throw $e;
		}
	}


	public static function readmoreNews($id){

		$connection = DBConn::getInstance();

		try
		{
			$statement = "SELECT * FROM ".Config::get('table_prefix')."news WHERE id = :id;";
			$query = $connection->conn->prepare($statement);
				$query->execute(array(
					':id'  => $id
				));
			if(!$query->rowCount())
				throw new Exception("Unable to find the News you are looking for.");

			return $query->fetch(PDO::FETCH_ASSOC);
		}catch(Exception $e){
			throw $e;
		}
	}


	public static function readAllNews(){

		$connection = DBConn::getInstance();

		try
		{
			$max_items =5;
			$page = "";
			$return_msg = "";
			$group = Input::hasValue('pg', 'get') ? Input::get('pg', 'get') : 1;

			$data_point = ($group - 1) * $max_items;

			$statement = "SELECT SQL_CALC_FOUND_ROWS ".Config::get('table_prefix')."news.* 
							FROM 
							".Config::get('table_prefix')."news 
							WHERE active=1 
							ORDER BY 
							date DESC LIMIT ".$max_items." OFFSET ".$data_point.";";


			$query = $connection->conn->prepare($statement);
				$query->execute();
			if(!$query->rowCount())
				throw new Exception("Unable to find the News you are looking for.");

				// ===================================================================================
				//  Get count
				// ===================================================================================
				$query_rows = $connection->conn->prepare('SELECT FOUND_ROWS();');
				$query_rows->execute();
				$fetch_count = $query_rows->fetch(PDO::FETCH_ASSOC);
				
				$return_msg .= '<div id="search_wrapper">';
				$return_msg .= '	<div id="search-ajax-msg"></div>';
				$return_msg .= '</div>';
				
				$group--;
				$i = $group * $max_items + 1;
				while($data = $query->fetch(PDO::FETCH_ASSOC))
				{
					$section = Sections::createNewsElement($data);
					$return_msg .= $section;
					$i++;
				}
				$group++;
				
				// =====================================================
				//  Show pagination
				// =====================================================
				$return_msg .= Pagination::show_pageination(array(
					"page"      => $page,
					"max_items" => $max_items,
					"group"     => $group,
					"total"     => $fetch_count['FOUND_ROWS()'],
					"extra_params" => "pg=".$pg
				));

				return $return_msg;

		}catch(Exception $e){
			throw $e;
		}
	}


	public static function readmoreAnnouncements($id){
		$connection = DBConn::getInstance();
		try
		{
			$statement = "SELECT * FROM ".Config::get('table_prefix')."agency_announcements WHERE id = :id;";
			$query = $connection->conn->prepare($statement);
				$query->execute(array(
					':id'  => $id
				));
			if(!$query->rowCount())
				throw new Exception("Unable to find the Announcements you are looking for.");

			return $query->fetch(PDO::FETCH_ASSOC);
		}catch(Exception $e){
			throw $e;
		}
	}


	public static function readAllAnnouncements(){
		$connection = DBConn::getInstance();
		try
		{
			$statement = "SELECT * FROM ".Config::get('table_prefix')."agency_announcements 
							WHERE active=1 
							ORDER BY 
							date DESC;";

			$query = $connection->conn->prepare($statement);
				$query->execute();
			if(!$query->rowCount())
				throw new Exception("Unable to find the Announcements.");

			return $query->fetchAll(PDO::FETCH_ASSOC);
		}catch(Exception $e){
			throw $e;
		}
	}

	
	public static function  getBreadCrumbTrail($pp){
		$str = explode('/', $_SERVER['PHP_SELF']);
		$ppUppercase = strtoupper($pp);
		$url = '/';
		$breadCrumbTrailString = "<span class='bread'><a href='$url'>HOME</a>";
		for($i=1; $i<count($str)-1; $i++){
			$url.=$str[$i].'/';
			$text = strtoupper($str[$i]);
			$breadCrumbTrailString .= " > </span> <span class='bread'><a href='$url'>$text</a>";
		}
		$breadCrumbTrailString .= " > </span> <span class='bread'><a href='$url$pp'>$ppUppercase</a>";
		return $breadCrumbTrailString.'</span>';
	}
		
	public static function getContentFiles($permalink){

		$connection = DBConn::getInstance();

		try
		{
			if(!empty($permalink)){
				$statement = "	SELECT *
								FROM cmha_content_files
								WHERE permalink = :permalink;";
				$query = $connection->conn->prepare($statement);
				$query->execute(array(":permalink" => $permalink));
				return $query->fetchAll(PDO::FETCH_ASSOC);
			}		
		}catch(Exception $e){
			throw $e;
		}
	}

	public static function getEmployeeDetails($searchField){

		$connection = DBConn::getInstance();
		try
		{
			$statement = "SELECT * FROM ".Config::get('table_prefix')."employee as e 
							INNER JOIN ".Config::get('table_prefix')."departments as d 
							ON e.`department_Id` = d.`id`
							WHERE 
							e.firstname LIKE ? OR 
							e.lastname LIKE ? OR 
							e.username LIKE ?;";


			$query = $connection->conn->prepare($statement);

			$query_vars   	 = array();
			$query_vars[0]   = "%".$searchField."%";
			$query_vars[1]   = "%".$searchField."%";
			$query_vars[2]   = "%".$searchField."%";
			$query->execute($query_vars);

				if(!$query->rowCount())
					throw new Exception("No Such Employee.");

				return $query->fetchAll(PDO::FETCH_ASSOC);
		}catch(Exception $e){
			throw $e;
		}
	}

	public static function getEvents($id){

		$connection = DBConn::getInstance();
		try
		{
		   $statement = "SELECT * FROM ".Config::get('table_prefix')."events 
		   					WHERE id = :id AND 
		   					active = 1 
		   					ORDER BY 
		   					date_modified DESC;";

			$query = $connection->conn->prepare($statement);

			$query_vars   	 = array();
			$query_vars[0]   = $id;
		
			$query->execute($query_vars);

				if(!$query->rowCount())
					throw new Exception("No Events.");

				return $query->fetch(PDO::FETCH_ASSOC);
		}catch(Exception $e){
			throw $e;
		}
	}


	public static function getEmployeeProfileDetails($permalink){
		$connection = DBConn::getInstance();
		try
		{
			$statement        = "SELECT * FROM ".Config::get('table_prefix')."employee as e 
								INNER JOIN 
								".Config::get('table_prefix')."departments as d 
								ON 
								e.`department_Id` = d.`id`
								WHERE 
								e.permalink = ?;";
								
			$query_vars   	  = array();
			$query_vars[0]    = $permalink;
			$query            = $connection->conn->prepare($statement);
			$query->execute($query_vars);
			if($query->rowCount()){
				return $query->fetchAll(PDO::FETCH_ASSOC);
			}
		}catch(Exception $e){
			throw $e;
		}
	}

}

?>