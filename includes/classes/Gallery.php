<?php

include_once(dirname(__FILE__).'/../../includes/init.php');

class Gallery
{
	private static $thumbs = array();
	
	private static $gallery_id      = null;
	private static $gallery_title   = "";
	private static $permalink       = "";
	private static $content         = "";
	private static $date_of_project = "";
	private static $found           = false;

	public static function isFound(){
		return self::$found;
	}

	public static function getTitle(){
		return self::$gallery_title;
	}
	
	public static function getPermalink(){
		return self::$permalink;
	}

	public static function getContent(){
		return self::$content;
	}

	public static function getDateOf(){
		return self::$date_of_project;
	}

	public static function getThumbs($gallery_id = ""){
		
		// If an ID is supplied, get the specific gallery's images
		if(!empty($gallery_id))
		{
			try
			{
				self::getGalleryImages($gallery_id);
			}catch(Exception $e){
				throw new Exception($e->getMessage());
			}
		}

		return self::$thumbs;
	}

	public static function getGallery($id){

		$errors = array();
		$connection = DBConn::getInstance();
		
		try
		{
			$statement = "	SELECT ".Config::get('table_prefix')."gallery_albums.*,
								".Config::get('table_prefix')."gallery_permalinks.permalink
							FROM ".Config::get('table_prefix')."gallery_albums 
							LEFT JOIN ".Config::get('table_prefix')."gallery_permalinks ON
								".Config::get('table_prefix')."gallery_permalinks.gallery_id = ".Config::get('table_prefix')."gallery_albums.id";
			
			if(!empty($id))
				$statement .= "	WHERE ".Config::get('table_prefix')."gallery_permalinks.permalink = :id;";
			else
			{
				$statement .= "	ORDER BY ".Config::get('table_prefix')."gallery_albums.date_added DESC
								LIMIT 1;";
			}
			// =====================================================
			//	Execute query
			// =====================================================
			$query = $connection->conn->prepare($statement);
			if(!empty($id))
				$query->execute(array(":id" => $id));
			else
				$query->execute();
			
			if(!$query->rowCount())
			{
				if(!empty($id))
					throw new Exception("Unable to find the gallery that you were looking for.");
				else
					throw new Exception("There are no galleries at this time.");
			}
		}catch(Exception $e){
			throw new Exception($e->getMessage());
		}

		// Gallery info
		$data = $query->fetch(PDO::FETCH_ASSOC);
		
		self::$gallery_id      = $data['id'];
		self::$gallery_title   = $data['title'];
		self::$permalink       = $data['permalink'];
		// self::$content         = $data['content'];
		self::$found = true;

		try
		{
			self::getGalleryImages(self::$gallery_id);
		}catch(Exception $e){
			throw new Exception($e->getMessage());
		}

		return self::$gallery_id;
	}

	private static function getGalleryImages($gallery_id){

		$connection = DBConn::getInstance();
		
		// ===================================================================================
		//	Get images for gallery
		// ===================================================================================
		try
		{
			$statement = "	SELECT *
							FROM ".Config::get('table_prefix')."gallery_images
							WHERE gallery_id = :id
							ORDER BY id ASC;";
			$query_img = $connection->conn->prepare($statement);
			$query_img->execute(array(":id" => $gallery_id));

			if($query_img->rowCount())
			{
				while($data_img = $query_img->fetch(PDO::FETCH_ASSOC))
				{
					// Get thumbs
					if(!empty($data_img['filename']))
					{
						$thumb               = array();
						$thumb['gallery_id'] = $data_img['gallery_id'];
						$thumb['id']         = $data_img['id'];
						$thumb['filename']   = Config::get('absolute_url').Config::get('gallery_files').self::$permalink.'/'.$data_img['filename'];
						$thumb['thumb']   	 = Config::get('absolute_url').Config::get('gallery_files').self::$permalink.'/'.$data_img['filename'];//['thumb'];
						$thumb['caption']    = $data_img['caption'];

						self::$thumbs[] = $thumb;
					}
				}
			}
		}catch(Exception $e){
			throw new Exception("Error finding gallery images:<br>".$e->getMessage());
		}
	}

	public static function getGalleryList($max = 0){

		$connection = DBConn::getInstance();

		try
		{
			$statement = "	SELECT 	".Config::get('table_prefix')."gallery_albums.*,
									".Config::get('table_prefix')."gallery_permalinks.permalink
							FROM ".Config::get('table_prefix')."gallery_albums
							LEFT JOIN ".Config::get('table_prefix')."gallery_permalinks ON
								".Config::get('table_prefix')."gallery_permalinks.gallery_id = ".Config::get('table_prefix')."gallery_albums.id";
			if($max) $statement .= " LIMIT ".$max;
			$statement .= ";";
			$query = $connection->conn->prepare($statement);
			$query->execute();
			if(!$query->rowCount())
				throw new Exception('There are no galleries at this time.');
			else
				return $query->fetchAll(PDO::FETCH_ASSOC);

		}catch(PDOException $e){
			throw new Exception('Error finding galleries available:<br>'.$e->getMessage());
		}
	}

	public static function show_pageination($params)
	{
		$current_page      = "";
		$max_items         = 3;
		$group             = 0;
		$item_count        = -1;
		$extra_query       = "";
		$extraQueryStrings = "";

		if(isset($params['max_items']))
			$max_items = $params['max_items'];
		if(isset($params['group']))
			$group = ++$params['group'];
		if(isset($params['total']))
			$item_count = $params['total'];
		if(isset($params['extra_params']))
			$extraQueryStrings = $params['extra_params'];

		$msg = "";
		
		$group_counter = $group * $max_items;
		$max_group = ceil($item_count/$max_items);
		if($max_group == 0)
			$max_group = 1;
		$max_pages   = $max_group;								// What is the maximum page number
		$page_number = $group;
		$difference  = 2;
		$max_shown   = 5;

		$highest_page = $max_shown;
		$lowest_page  = 1;

		// ===================================================================================
		//	Figure out lowest/highest page
		// ===================================================================================
		if($group > $difference)
			$lowest_page = $group - $difference;

		if($max_pages > $group)
		{
			$highest_page = $group + $difference;

			if(($highest_page < $max_shown) && ($max_shown < $max_group))
				$highest_page = $max_shown;

			if($highest_page > $max_group)
				$highest_page = $max_group;
		}
		else
			$highest_page = $group;
		
		if($max_group > 1)
		{
			$msg .= '<div id="pagination_container" class="gallery-pagination-container">';
			$msg .= 	'<div class="pagination-pages">';

			$prev = $group - 1;
			if($prev < 1)
				$msg .= '<span class="counter prev-counter inactive icon-arrow-left"></span>';
			else
				$msg .= '<span class="counter prev-counter icon-arrow-left js-counter" data-page="'.($prev - 1).'"></span>';
			
			// if the page number is greater than 2
			if($page_number > 2)
			{
				// display the list
				for($i = $lowest_page; $i <= $highest_page; $i++)
				{
					if($i == $page_number)
						$msg .= '<span class="counter current_counter">'.$i.'</span>';
					else
						$msg .= '<span class="counter js-counter" data-page="'.($i - 1).'">'.$i.'</span>';
				}
			}
			// page number is under the minimum
			else
			{
				// display the list
				for($i = $lowest_page; $i <= $highest_page; $i++)
				{
					if($i == $page_number)
						$msg .= '<span class="counter current_counter">'.$i.'</span>';
					else
						$msg .= '<span class="counter js-counter" data-page="'.($i - 1).'">'.$i.'</span>';
				}
			}
			
			// // show last button
			// if($group < ($max_group - $difference))
			// {
			// 	$msg .= '<span class="counter';
			// 	if($group >= $max_pages)
			// 		$msg .= ' current_counter';
			// 	$msg .= '" data-page="'.$max_pages.'">&gt;&gt;</span>';
			// }

			$next = $group + 1;
			if($next <= $max_group)
				$msg .= '<span class="counter next-counter js-counter icon-arrow-right" data-page="'.($next - 1).'"></span>';
			
			// end pagination container
			$msg .= 	'</div>';
			$msg .= '</div>';
		}
		
		return $msg;
	}
}

?>