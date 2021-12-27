<?php

interface baseRepo
{
	function selectAll();
	function selectOneById($id);
	function selectByFilter($filterArray);
	function insert(baseEntity $object);
	function update(baseEntity $object);
	function delete($id);
}

?>
