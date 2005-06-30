<?php
require_once('../../bit_setup_inc.php');
require_once(ARTICLES_PKG_PATH.'BitArticle.php');

class TestBitArticle extends Test {
    
    var $test;
    var $id;
    var $count;
    
    function TestBitArticle()
    {
        $this->test = new BitArticle();
        Assert::equalsTrue($this->test != NULL, 'Error during initialisation');
    }

    function testGetItems()
    {
	$filter = array();
        $list = $this->test->getList($filter);
        $this->count = count($list);
        Assert::equalsTrue(is_array($list));
    }

    function testStoreItem()
    {
	$newItemHash = array(
		"title" => "Test Title",
		"description" => "Test Description",
		"data" => "Test Text",
		"topic_id" => NULL,
		"topic_name" => NULL,
		"size" => NULL,
		"use_image" => NULL,
		"image_name" => NULL,
		"image_type" => NULL,
		"image_size" => NULL,
		"image_x" => NULL,
		"image_y" => NULL,
		"image_data" => "",
		"publish_date" => NULL,
		"expire_date" => NULL,
		"hash" => NULL,
		"type" => NULL,
		"isfloat" => NULL,
	);
        Assert::equalsTrue($this->test->store($newItemHash));
    }
    
    function testIsValidItem()
    {
        Assert::equalsTrue($this->test->isValid());
    }
    
    function testNullItem()
    {
	$this->id = $this->test->mArticleId;
        $this->test = NULL;
        Assert::equals($this->test, NULL);
    }
    
    function testLoadItem()
    {
        $this->test = new BitArticle($this->id);
        Assert::equals($this->test->load(), 37);
    }

    function testUrlItem()
    {
        Assert::equalsTrue($this->test->getDisplayUrl() != "");
    }

    function testExpungeItem()
    {
        Assert::equalsTrue($this->test->expunge());
    }

    function testCountItems()
    {
	$filter = array();
        $count = count($this->test->getList($filter));
        Assert::equals($this->count, $count);
    }

}
?>
