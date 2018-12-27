<?php

namespace Behatch\Exception;

final class MissingPackageException extends \RuntimeException
{
	public function __construct($package, $method)
	{
		parent::__construct("Please install $package composer package in order to use $method method.");
	}

}