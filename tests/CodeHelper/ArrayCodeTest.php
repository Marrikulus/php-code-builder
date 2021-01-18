<?php declare(strict_types=1);

namespace CodeHelper;

final class ArrayCodeTest extends \PHPUnit\Framework\TestCase
{
	public function testSimpleAssocArray(): void
	{
		$array = new \Stefna\PhpCodeBuilder\CodeHelper\ArrayCode([
			'test1' => 2,
			'test2' => "string",
			'test3' => true,
		]);

		$this->assertSame("[
	'test1' => 2,
	'test2' => 'string',
	'test3' => true,
]", trim($array->getSource()));
	}

	public function testCustomIndentLevel(): void
	{
		$array = new \Stefna\PhpCodeBuilder\CodeHelper\ArrayCode([
			'test1' => 2,
			'test2' => "string",
			'test3' => true,
		]);

		$this->assertSame([
			'[',
			[
				"'test1' => 2,",
				"'test2' => 'string',",
				"'test3' => true,",
			],
			']',
		], $array->getSourceArray(1));
	}

	public function testNestedAssocArray(): void
	{
		$array = new \Stefna\PhpCodeBuilder\CodeHelper\ArrayCode([
			'test1' => 2,
			'test2' => "string",
			'test3' => true,
			'test4' => [
				'sub1' => 'test',
			],
		]);

		$this->assertSame("[
	'test1' => 2,
	'test2' => 'string',
	'test3' => true,
	'test4' => [
		'sub1' => 'test',
	],
]", trim($array->getSource()));
	}

	public function testSimpleArray(): void
	{
		$array = new \Stefna\PhpCodeBuilder\CodeHelper\ArrayCode([
			'string',
			1,
			false,
			[
				'assoc' => true,
			],
		]);

		$this->assertSame("[
	'string',
	1,
	false,
	[
		'assoc' => true,
	],
]", trim($array->getSource()));
	}
}