<?php declare(strict_types=1);

namespace Stefna\PhpCodeBuilder\Tests\Renderer;

use PHPUnit\Framework\TestCase;
use Stefna\PhpCodeBuilder\PhpConstant;
use Stefna\PhpCodeBuilder\PhpMethod;
use Stefna\PhpCodeBuilder\PhpTrait;
use Stefna\PhpCodeBuilder\Renderer\Php7Renderer;
use Stefna\PhpCodeBuilder\ValueObject\Identifier;
use Stefna\PhpCodeBuilder\ValueObject\Type;

final class PhpTraitTest extends TestCase
{
	use AssertResultTrait;

	public function testTrait()
	{
		$trait = new PhpTrait(Identifier::fromString(Test\TestTrait::class));
		$trait->addMethod(PhpMethod::protected('testTraitMethod', [], [], Type::fromString('void')));
		$trait->addMethod(PhpMethod::public('testAbstractTraitMethod', [], [], Type::fromString('int'))->setAbstract());

		$trait->addConstant(PhpConstant::private('traitConstant'));

		$renderer = new Php7Renderer();

		$this->assertSourceResult($renderer->renderTrait($trait), 'PhpTraitTest.' . __FUNCTION__);
	}
}