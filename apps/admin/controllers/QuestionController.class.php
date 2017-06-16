<?php

class QuestionController extends nomvcBaseControllerTwo
{
	protected function init()
	{
        parent::init();
    }
	
	public function run()
	{
        $generator = new OutputGenerator($this->context, $this);

        $fh = fopen('php://input', 'r');
		$postData = '';
        $line = fgets($fh);
	    echo $line;

        //Пишем логи
        $f = fopen('/tmp/getRexona.log', 'a');
	    fputs($f, $line);
	    fclose($f);

        //$test = "HELLO";

		return $generator->prepare('question', array('line' => $line))->run();
	}
}


