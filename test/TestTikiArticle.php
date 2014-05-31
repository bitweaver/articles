<?php
require_once '../../kernel/setup_inc.php';
require_once(ARTICLES_PKG_PATH.'BitArticle.php');

class TestBitArticle extends Test
{
    public $test;
    public $id;
    public $count;

    public function TestBitArticle()
    {
        $this->test = new BitArticle();
        Assert::equalsTrue($this->test != NULL, 'Error during initialisation');
    }

    public function testGetItems()
    {
	$filter = array();
        $list = $this->test->getList($filter);
        $this->count = count($list);
        Assert::equalsTrue(is_array($list));
    }

    public function testStoreItem()
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

    public function testIsValidItem()
    {
        Assert::equalsTrue($this->test->isValid());
    }

    public function testNullItem()
    {
	$this->id = $this->test->mArticleId;
        $this->test = NULL;
        Assert::equals($this->test, NULL);
    }

    public function testLoadItem()
    {
        $this->test = new BitArticle($this->id);
        Assert::equals($this->test->load(), 37);
    }

    public function testUrlItem()
    {
        Assert::equalsTrue($this->test->getDisplayUrl() != "");
    }

    public function testExpungeItem()
    {
        Assert::equalsTrue($this->test->expunge());
    }

    public function testCountItems()
    {
	$filter = array();
        $count = count($this->test->getList($filter));
        Assert::equals($this->count, $count);
    }

}
