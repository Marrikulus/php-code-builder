<?php declare(strict_types=1);

namespace Stefna\PhpCodeBuilder;

use Stefna\PhpCodeBuilder\ValueObject\Type;

/**
 * Class that represents the source code for a phpdoc comment in php
 *
 * @author Fredrik Wallgren <fredrik.wallgren@gmail.com>
 * @author Andreas Sundqvist <andreas@stefna.is>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class PhpDocComment
{
	private ?PhpDocElement $var = null;
	private ?PhpDocElement $author = null;
	private ?PhpDocElement $return = null;

	private null|PhpVariable|PhpFunction|PhpTrait $parent = null;

	/** @var PhpDocElement[] */
	private array $params = [];
	/** @var PhpDocElement[] */
	private array $throws = [];
	/** @var PhpDocElement[] */
	private array $methods = [];

	/**
	 * Create single line var docblock
	 */
	public static function var(Type $type): static
	{
		$doc = new static();
		$doc->setVar(PhpDocElementFactory::getVar($type));

		return $doc;
	}

	public function __construct(
		private string $description = '',
	) {}

	public function getParent(): PhpTrait|PhpVariable|PhpFunction|null
	{
		return $this->parent;
	}

	public function setParent(PhpTrait|PhpVariable|PhpFunction|null $parent): void
	{
		$this->parent = $parent;
	}

	public function addMethod(PhpDocElement $method): static
	{
		$this->methods[] = $method;
		return $this;
	}

	public function setVar(PhpDocElement $var): static
	{
		$this->var = $var;
		return $this;
	}

	public function setAuthor(PhpDocElement $author): static
	{
		$this->author = $author;
		return $this;
	}

	public function setLicence(PhpDocElement $licence): static
	{
		$this->methods[] = $licence;
		return $this;
	}

	public function setReturn(PhpDocElement $return): static
	{
		$this->return = $return;
		return $this;
	}

	public function hasParamWithName(string $name): bool
	{
		$name = ltrim($name, '$');
		foreach ($this->params as $param) {
			if (ltrim($param->getVariableName(), '$') === $name) {
				return true;
			}
		}
		return false;
	}

	public function removeParamWithName(string $name): static
	{
		$name = ltrim($name, '$');
		foreach ($this->params as $key => $param) {
			if (ltrim($param->getVariableName(), '$') === $name) {
				unset($this->params[$key]);
				break;
			}
		}
		return $this;
	}

	public function addParam(PhpDocElement $param): static
	{
		$this->params[$param->getHashCode()] = $param;
		return $this;
	}

	public function setParams(PhpDocElement ...$params): static
	{
		$this->params = $params;
		return $this;
	}

	public function addThrows(PhpDocElement $throws): static
	{
		$this->throws[] = $throws;
		return $this;
	}

	public function setDescription(string $description): static
	{
		$this->description = $description;
		return $this;
	}

	public function getMethods(): array
	{
		return $this->methods;
	}

	public function getAuthor(): ?PhpDocElement
	{
		return $this->author;
	}

	public function getDescription(): string
	{
		return $this->description;
	}

	public function getParams(): array
	{
		return $this->params;
	}

	public function getReturn(): ?PhpDocElement
	{
		return $this->return;
	}

	public function getThrows(): array
	{
		return $this->throws;
	}

	public function getVar(): ?PhpDocElement
	{
		return $this->var;
	}

	public function removeVar()
	{
		$this->var = null;
	}

	public function addField(PhpDocElement $deprecated): static
	{
		$this->methods[] = $deprecated;
		return $this;
	}
}
