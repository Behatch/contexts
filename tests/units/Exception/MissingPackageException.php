<?php

namespace Behatch\Tests\Units\Exception;

class MissingPackageException extends \atoum
{
	public function test_exception_meesage()
	{
		$exception = $this->newTestedInstance('foo', 'bar');

		$this->string($exception->getMessage())
			->isEqualTo('Please install foo composer package in order to use bar method.');
	}
}
