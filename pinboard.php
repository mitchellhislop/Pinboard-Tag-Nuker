<?php
// Pinboard Tag Nuker
//
// @author: Mitchell Hislop
// @version: 1
// @desc: Allows you to remove all the tags from your pinboard.in account
// 
function getAllTags($user, $pass)
{
		$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://'.$user.':'.$pass.'@api.pinboard.in/v1/tags/get');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$buffer = curl_exec($ch);
	$tags = new SimpleXMLElement($buffer);
			foreach($tags->tag as $tag)
			{
				echo "tag: ".$tag['tag'];
				echo "\n";
				$tags_to_nuke[]=$tag['tag'];

			}
			return $tags_to_nuke;
}
function nukeTags($user, $pass, $tags_to_nuke)
{
	foreach($tags_to_nuke as $tag)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://'.$user.':'.$pass.'@api.pinboard.in/v1/tags/delete&tag='.$tag);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$buffer=curl_exec($ch);
		try{
		$result = new SimpleXMLElement($buffer);
		if ($result[0]=='done')
		{
			echo "Tag nuked: ".$tag."\n";
		}
		else
		{
			//this is a simple script. If it fails, it tries again twice. If it still fails, it gives up
			$count = array_count_values($tags_to_nuke);
			if ($count[$tag] <= 3)
			{
				$tags_to_nuke[]=$tag;
			}

		}
		}
		catch(Exception $e)
		{
			echo "Tag FAILED: ".$tag."\n";
		}
		sleep(4); //respect the API

	}

}

$user = 'YOUR USERNAME';
$pass = 'YOUR PASSWORD';


$to_nuke =  getAllTags($user, $pass);
echo "sleeping...\n";
sleep(5);
nukeTags($user, $pass, $to_nuke);
