<?php

namespace Sh;

class FieldDecimal extends FieldFloat {
	
	protected $dataType = 'decimal';
	protected $mask				= 'decimal';
	protected $validationType	= 'number';
		
}