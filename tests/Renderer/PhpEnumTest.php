<?php declare(strict_types=1);

namespace Stefna\PhpCodeBuilder\Tests\Renderer;

use PHPUnit\Framework\TestCase;
use Stefna\PhpCodeBuilder\PhpEnum;
use Stefna\PhpCodeBuilder\Renderer\Php74Renderer;
use Stefna\PhpCodeBuilder\Renderer\Php7Renderer;
use Stefna\PhpCodeBuilder\Renderer\Php81Renderer;
use Stefna\PhpCodeBuilder\ValueObject\EnumBackedCase;
use Stefna\PhpCodeBuilder\ValueObject\EnumCase;
use Stefna\PhpCodeBuilder\ValueObject\Type;

class PhpEnumTest extends TestCase
{
	use AssertResultTrait;

	/**
	 * @dataProvider enumTypes
	 */
	public function testPhp7(PhpEnum $enum, string $expectedFile): void
	{
		$renderer = new Php74Renderer();

		$this->assertSourceResult($renderer->render($enum), 'PhpEnumTest.' . __FUNCTION__ . '.' . $expectedFile);
	}

	/**
	 * @dataProvider enumTypes
	 */
	public function testPhp81(PhpEnum $enum, string $expectedFile): void
	{
		$renderer = new Php81Renderer();

		$this->assertSourceResult($renderer->render($enum), 'PhpEnumTest.' . __FUNCTION__ . '.' . $expectedFile);
	}

	/**
	 * @return array<string, array{PhpEnum, string}>
	 */
	public static function enumTypes(): array
	{
		return [
			'simple enum' => [
				new PhpEnum(
					'Test',
					cases: [
						new EnumCase('Up'),
						new EnumCase('Down'),
					],
				),
				'simple',
			],
			'backed enum' => [
				new PhpEnum(
					'Test',
					Type::fromString('string'),
					cases: [
						new EnumBackedCase('Up', 'U'),
						new EnumBackedCase('Down', 'N'),
					],
				),
				'backed',
			],
		];
	}
}
