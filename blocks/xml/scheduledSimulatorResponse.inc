<?php
/**
 * @copyright 2007-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Abhiroop Bhatnagar <bhatnagarabhiroop@gmail.com>
 * @param $this->DispatchList
 */

$data=$this->DispatchList;
$i=1;
foreach($data as $message)
{
 	echo'<User>';
		echo'<Sms>'.$message.'</Sms>';
	echo'</User>';
	$i++;
}
?>
