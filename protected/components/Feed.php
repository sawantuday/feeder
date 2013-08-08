<?php
class Feed extends CApplicationComponent
{
	private $last_modified;		//If-Modified-Since
	private $eTag;				//If-None-Match
	private $content_encoding=false;
	
	private $isRSS;
	private $items;
	private $title;
	private $link;
	private $last_build_date;
	
	private $errors;
	private $short_desc_length = 150; 
	
	//private $atom_tags = array('feed','updated','link','title', 'entry');
	//private $rss_tags = array('channel','lastBuildDate','link','title', 'item');
	
	private function parse($rss)
	{
		$this->title = $this->isRSS ? $rss->channel->title : $rss->feed->title;
		$this->link  = $this->isRSS ? $rss->channel->link : $rss->feed->link;
		$this->last_build_date = $this->isRSS ? $rss->channel->lastBuildDate : $rss->feed->updated;
		
		if(empty($this->last_modified))
			$this->last_modified = $this->last_build_date;
		
		$entries =  $this->isRSS ? $rss->channel->item : $rss->feed->entry;
		
		//now process entried/items
		foreach($entries as $entry)
		{
			$item['title'] = $entry->title;
			$item['link'] = $entry->link;
			$item['comments'] = $entry->comments;
			$item['author'] = $entry->author;
			$item['category'] = $entry->category;
			$item['guid'] = $this->isRSS ? $entry->guid : $entry->id;
			$item['pubDate'] = date('Y-m-d h:i:s', strtotime($this->isRSS ? $entry->pubDate : $entry->published));
			
			if($this->isRSS)
			{
				$item['description'] = $entry->description;
			}
			elseif(isset($entry->summary)) 
			{ 
				$item['description'] = $entry->summary; 
			}
			elseif (isset($entry->content))
			{
				$item['description'] = $entry->content;
			}
			
			//set items short description
			$item['short_desc'] = $this->getExtract($item['description']);
			
			$this->items[] = (object)$item;
			unset($item);
		}
		
	}

	public function get($url)
	{
		//while get request you dont have last modified and etags set
		//simply call curl and get contents
		
		//first process url
		$path_info = parse_url($url);
		if(!isset($path_info['scheme']))
			$url = 'http://'.$url;
		
		//try to fetch data
		$xml = $this->curlCall($url);
		file_put_contents('temp', $xml);		
		//$xml = file_get_contents('temp');		//echo $xml;exit;
		
		if(empty($xml))	return false;
		
		libxml_use_internal_errors(true);
		$rss = simplexml_load_string($xml);

		if(!$rss)
		{
			foreach (libxml_get_errors() as $error)
				$this->errors .= $error->message."\r\n";
			return false;
		}
		
		libxml_clear_errors();
		
		if($rss->getName() != 'rss' && $rss->getName() != 'atom')
		{
			//this is not an rss feed. Try to get rss link from the received doc
			$link = $this->autoDiscover($xml);
			if(!$link)	return false;
		}
		
		$this->isRSS = $rss->getName() == 'rss';
		
		$this->parse($rss);
		
		return count($this->items);
	}
	
	private function autoDiscover($xml)
	{
		preg_match_all('/<link.*rel="alternate".*/', $xml, $matches);
		if(!isset($matches[0]))	return false;
			
		//check if the link have type set to application/atom+xml or application/rss+xml
		$links = preg_grep('/type="application\/(atom|rss)\+xml"/', $matches[0]);	//type="application\/atom+xml"
		if(!isset($links[0])) return false;
		
		//get the link
		preg_match('/href="(.*?)"/', $links[0], $matches);
		if(!isset($matches[1]))	return false;
		$link = $matches[1];
			
		//now check if this is a full link (with FQDN)
		if(filter_var($link, FILTER_VALIDATE_URL) === FALSE)
		{
			$path_info = parse_url($url);
			$link = $path_info['scheme'].'://'.$path_info['host'].$link;
		}
			
		//now try to get rss contents with this link
		if(is_string($link))
		{
			$this->get($link);
		}
	}	
	
	public function getLink(){ return $this->link; }
	public function getETag(){ return $this->eTag; }
	public function getItems(){ return $this->items; }
	public function getTitle(){ return $this->title; }
	public function getErrors(){ return $this->errors; }
	public function getIsRSS(){ return $this->isRSS; }
	public function getLastModified(){ return $this->last_modified; }
	public function getLastBuildDate(){ return $this->last_build_date; }
	public function getItemCount(){return count($this->items); }
	
	public function setLastModified($time){ $this->last_modified = $time; return $this; }
	public function setETag($ETag){ $this->eTag = $ETag; return $this; }
	
	
	/**
	 * Set If-None-Match(eTag) and If-Modified-Since(last-modified) 
	 * headers set accept encoding 
	 * @param string $url
	 */
	private function curlCall($url)
	{
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'If-None-Match:'.$this->eTag,
			'If-Modified-Since:'.$this->last_modified,
		));
		
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		//this is to get response headers
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		//to get the request headers
		//curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		
		$response = curl_exec($ch); 

		//this is to get response headers
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$headers = substr($response, 0, $header_size);	
		$headers = explode("\r\n", $headers);
		$contents = substr($response, $header_size);
		
		foreach ($headers as $key=>$val)
		{  
			$val = explode(': ', $val);
			$headers[$val[0]] = @$val[1]; 
			unset($headers[$key]);
		}
		
		if(isset($headers['Content-Encoding']))
			$this->content_encoding = $headers['Content-Encoding'];
		
		$this->eTag = array_key_exists('ETag', $headers) ? $headers['ETag'] : '';
		$this->last_modified = array_key_exists('Last-Modified', $headers) ? $headers['Last-Modified'] : '';
		
		//$info = curl_getinfo($ch);
		curl_close($ch);
		//var_dump($info, $contents);exit;
		return $contents;
	}
	
	private function getExtract($html)
	{
		$str = strip_tags($html);
  		$str = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $str);
  		return substr($str, 0, $this->short_desc_length);
	}

}