<?php declare(strict_types=1);

namespace Stefna\PhpCodeBuilder\ValueObject;

final class Identifier
{
	private static $instances = [];

	/** @var string */
	private $name;
	/** @var string */
	private $namespace;
	/** @var string|null */
	private $alias;

	public static function fromString($identifier): self
	{
		$namespace = '';
		if (strpos($identifier, '\\') !== false) {
			$ns = explode('\\', $identifier);
			$identifier = array_pop($ns);
			$namespace  = implode('\\', $ns);
		}
		if (isset(self::$instances[$identifier . $namespace])) {
			return self::$instances[$identifier . $namespace];
		}

		return self::$instances[$identifier . $namespace] = new self($identifier, $namespace);
	}

	public static function simple(string $name): self
	{
		if (isset(self::$instances[$name])) {
			return self::$instances[$name];
		}

		return self::$instances[$name] = new self($name, '');
	}

	public static function fromUnknown($identifier): self
	{
		if ($identifier instanceof Identifier) {
			return $identifier;
		}
		if (is_string($identifier)) {
			return self::fromString($identifier);
		}

		return new self(random_bytes('6'), '');
	}

	private function __construct(string $name, string $namespace)
	{
		$this->name = $name;
		$this->namespace = $namespace;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getNamespace(): string
	{
		return $this->namespace;
	}

	public function getFqcn(): string
	{
		return $this->namespace . '\\' . $this->name;
	}

	public function toString(): string
	{
		return $this->alias ?? $this->name;
	}

	public function setAlias(string $alias): void
	{
		$this->alias = $alias;
	}

	public function getAlias(): ?string
	{
		return $this->alias;
	}

	public function equal(Identifier $identifier): bool
	{
		return $this->namespace === $identifier->namespace && $this->name === $identifier->name;
	}
}