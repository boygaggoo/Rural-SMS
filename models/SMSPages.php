<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Abhiroop Bhatnagar <bhatnagarabhiroop@gmail.com>
 */
class SMSPages
{	
	public static function constructServiceListPages(array $list,$type)
	{
		$prefix=($type=='SERVICES')?SERVICE_OPTIONS_PREFIX:GROUP_OPTIONS_PREFIX;
		$pages=array();
		$pages['page1']=array();
		$pages['page1']['head'] = ($type=='SERVICES')?SERVICE_LIST_INFO_TEXT_1:GROUP_LIST_INFO_TEXT_1;
		$pages['page1']['tail'] = ($type=='SERVICES')?SERVICE_LIST_INFO_TEXT_2:GROUP_LIST_INFO_TEXT_2;
		$characterCount=strlen(html_entity_decode($pages['page1']['head']))+
					strlen(html_entity_decode($pages['page1']['tail']));
		$i=1;
		$pageNumber=1;	
		foreach($list as $key=>$value)
		{
			$smsBlock=$prefix.$key.'-'.$value.';';
			$characterCount=$characterCount+strlen(html_entity_decode($smsBlock));			
			if($characterCount>=(int)SMS_CHARACTER_LIMIT)
			{
				$pageNumber++;
				$pages['page'.$pageNumber]=array();
				$pages['page'.$pageNumber]['head'] = ($type=='SERVICES')?SERVICE_LIST_INFO_TEXT_1:GROUP_LIST_INFO_TEXT_3;
				$pages['page'.$pageNumber]['tail'] = ($type=='SERVICES')?SERVICE_LIST_INFO_TEXT_2:GROUP_LIST_INFO_TEXT_2;
				$characterCount=strlen(html_entity_decode($pages['page'.$pageNumber]['head']))
							+strlen(html_entity_decode($pages['page'.$pageNumber]['tail']));
				$characterCount=$characterCount+strlen(html_entity_decode($smsBlock));
				$i=1;	
			}
			
			$pages['page'.$pageNumber][$i++] = $smsBlock;  
		}
		self::removeIfLastPageNotRequired($pages,$pageNumber,$characterCount);
		$pages['page'.$pageNumber]['tail']='';	
		return $pages;
	}	
	public static function constructMetadataResponsePages($attributeList,$attributeNumber)
	{	
		$pages=array();
		$attributeProperties=$attributeList[$attributeNumber];
		$datatype=strtoupper($attributeProperties['datatype']);
			
		$pages['page1']['head']=constant($datatype.'_DATATYPE_RESPONSE_TEXT_1');
		$pages['page1']['head'].=$attributeProperties['description'];
		$pages['page1']['head'].=constant($datatype.'_DATATYPE_RESPONSE_TEXT_2');
		$pages['page1']['tail'] = MORE_OPTIONS_TEXT;
		$characterCount=strlen(html_entity_decode($pages['page1']['head']))+
					strlen(html_entity_decode($pages['page1']['tail']));
		
		$pageNumber=1;		
		if(($attributeProperties['datatype']=='singlevaluelist')||($attributeProperties['datatype']=='multivaluelist'))
		{
			$j=1;
			$i=1;
			foreach($attributeProperties['values'] as $key => $value)
			{
				$smsBlock=constant($datatype.'_OPTIONS_PREFIX').$j.'-'.$value.';';
				$characterCount=$characterCount+strlen(html_entity_decode($smsBlock));
				if($characterCount>=(int)SMS_CHARACTER_LIMIT)
				{
					$pageNumber++;
					$pages['page'.$pageNumber]=array();
					$pages['page'.$pageNumber]['head'] = OPTIONS_TEXT;
					$pages['page'.$pageNumber]['tail'] = MORE_OPTIONS_TEXT;
					$characterCount=strlen(html_entity_decode($pages['page'.$pageNumber]['head']))
								+strlen(html_entity_decode($pages['page'.$pageNumber]['tail']));
					$characterCount=$characterCount+strlen(html_entity_decode($smsBlock));
					$i=1;	
				}
				$pages['page'.$pageNumber][++$i] = $smsBlock;  
				++$j;
			}
			self::removeIfLastPageNotRequired($pages,$pageNumber,$characterCount);			
		}
		$pages['page'.$pageNumber]['tail']='';	
		return $pages;
	}
	public static function returnHelpPages($interactionMode,$pageNumber)
	{
		$helpPages=array(
				1=>array(),
				2=>array(),
				3=>array() );
		//helpPages description: helpPages[InteractionMode][PageNumber]

		if(GET_SERVICE_LIST_RESPONSE=='GROUPS')
		{
			$helpPages[1]=self::gatherHelpPages($helpPages[1],'HELP_GET_SERVICE_CODES_GROUPS_PAGE_');
		}
		else
		{
			$helpPages[1]=self::gatherHelpPages($helpPages[1],'HELP_GET_SERVICE_CODES_SERVICES_PAGE_');
		}
		
		$helpPages[2]=self::gatherHelpPages($helpPages[2],'HELP_SUBMIT_REQUEST_PAGE_');
		$helpPages[3]=self::gatherHelpPages($helpPages[3],'HELP_CHECK_REQUEST_STATUS_PAGE_');
		
		return $helpPages[$interactionMode][$pageNumber];
	}
	private static function gatherHelpPages($pages,$languageConstant)
	{	
		$pages=array();
		$i=1;
		while(defined($languageConstant.$i))
		{
			$pages[$i]=constant($languageConstant.$i);
			$i++;
		}
		return $pages;
	}
	private static function removeIfLastPageNotRequired(&$pages,&$lastPage,$characterCountLastPage)
	{
		if(count($pages)>1)
		{
			$lastPageOptionsLength=$characterCountLastPage-strlen(html_entity_decode($pages['page'.$lastPage]['head']))-strlen(html_entity_decode($pages['page'.$lastPage]['tail']));
			$secondLastPageOptionsLength=0;
			foreach($pages['page'.($lastPage-1)] as $key=>$value)
			{
				if(($key!='head')&&($key!='tail'))
					$secondLastPageOptionsLength=$secondLastPageOptionsLength+strlen(html_entity_decode($value));
			}
			if(($lastPageOptionsLength+$secondLastPageOptionsLength+
				strlen(html_entity_decode($pages['page'.($lastPage-1)]['head'])))<=(int)SMS_CHARACTER_LIMIT)
			{
				$optionsCount=count($pages['page'.($lastPage-1)])-2;
				foreach($pages['page'.($lastPage)] as $key=>$value)
				{
					if(($key!='head')&&($key!='tail'))
					{
						$pages['page'.($lastPage-1)][($optionsCount+2)]=$value;
						++$optionsCount;
					}
				}
				$pages['page'.$lastPage]=NULL;	
				--$lastPage;
			}	
		}
	}
}
?>