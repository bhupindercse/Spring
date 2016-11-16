<?php

class News
{
	public static function getLatest($limit = 1){

		$connection = DBConn::getInstance();

		try
		{
			$statement = "	SELECT 	".Config::get('table_prefix')."news.*,
									".Config::get('table_prefix')."news_permalinks.permalink
							FROM ".Config::get('table_prefix')."news
							LEFT JOIN ".Config::get('table_prefix')."news_permalinks ON
								".Config::get('table_prefix')."news_permalinks.item_id = ".Config::get('table_prefix')."news.id
								WHERE active=1 
							ORDER BY date_posted DESC
							LIMIT ".$limit.";";
			$query = $connection->conn->prepare($statement);
			$query->execute();
			if(!$query->rowCount())
				throw new Exception('Check back soon for new updates!');
			
			return $query->fetchAll(PDO::FETCH_ASSOC);

		}catch(Exception $e){
			throw $e;
		}
	}

	public static function getPostByPermalink($permalink){

		$connection = DBConn::getInstance();

		try
		{
			$statement = "	SELECT 	".Config::get('table_prefix')."news.*,
									".Config::get('table_prefix')."news_permalinks.permalink
							FROM ".Config::get('table_prefix')."news
							LEFT JOIN ".Config::get('table_prefix')."news_permalinks ON
								".Config::get('table_prefix')."news_permalinks.item_id = ".Config::get('table_prefix')."news.id
							WHERE permalink = :permalink and active = 1;";
			$query = $connection->conn->prepare($statement);
			$query->execute(array(":permalink" => $permalink));
			if(!$query->rowCount())
				throw new Exception('Unable to find the story you were looking for.');
			
			return $query->fetch(PDO::FETCH_ASSOC);

		}catch(Exception $e){
			throw $e;
		}
	}

	public static function getStoriesAtIndex($index, $limit = 4){

		$connection = DBConn::getInstance();

		try
		{
			$statement = "	SELECT 	SQL_CALC_FOUND_ROWS ".Config::get('table_prefix')."news.*,
									".Config::get('table_prefix')."news_permalinks.permalink
							FROM ".Config::get('table_prefix')."news
							LEFT JOIN ".Config::get('table_prefix')."news_permalinks ON
								".Config::get('table_prefix')."news_permalinks.item_id = ".Config::get('table_prefix')."news.id
								WHERE active=1 
							ORDER BY date_posted DESC
							LIMIT ".$limit."
							OFFSET ".$index.";";
			$query = $connection->conn->prepare($statement);
			$query->execute();
			if(!$query->rowCount())
				throw new Exception('There are no stories to display');

			// ===================================================================================
			//  Get count
			// ===================================================================================
			$query_rows = $connection->conn->prepare('SELECT FOUND_ROWS();');
			$query_rows->execute();
			$fetch_count = $query_rows->fetch(PDO::FETCH_ASSOC);

			$return_message['count']   = $fetch_count['FOUND_ROWS()'];
			$return_message['stories'] =  $query->fetchAll(PDO::FETCH_ASSOC);
			return $return_message;

		}catch(Exception $e){
			throw $e;
		}
	}
}

?>