<?php declare(strict_types=1);

namespace Stefna\PhpCodeBuilder\ValueObject;

final class Type
{
	private const ALIAS_MAP = [
		'boolean' => 'bool',
		'double' => 'float',
		'integer' => 'int',
		'number' => 'float',
	];

	private const INVALID_RETURN_TYPES = [
		'mixed',
		'resource',
		'static',
		'object',
	];

	public static function empty(): self
	{
		return new self('');
	}

	public static function fromString(string $type): self
	{
		if (!trim($type)) {
			throw new \InvalidArgumentException('No valid type hint found in string');
		}

		if (strpos($type, '|')) {
			$self = null;
			$types = explode('|', $type);
			foreach ($types as $typePart) {
				if ($typePart !== 'null') {
					$self = new self($typePart);
					break;
				}
			}
			if (!$self) {
				throw new \InvalidArgumentException('No valid type hint found in string');
			}
			foreach ($types as $typePart) {
				$self->addUnion($typePart);
			}
			return $self;
		}
		if (strpos($type, '?') === 0) {
			return new self(substr($type, 1), true);
		}
		return new self($type);
	}

	/** @var bool */
	private $nullable;
	/** @var string */
	private $type;
	/** @var string[] */
	private $types = [];

	public function __construct(string $type, bool $nullable = false)
	{
		$this->type = $type;
		$this->nullable = $nullable;
	}

	public function setType(string $type): void
	{
		$this->type = $type;
	}

	public function addUnion(string $type): void
	{
		if ($type === 'null') {
			$this->nullable = true;
			return;
		}
		if (!count($this->types)) {
			$this->types[] = $this->type;
		}
		if (!in_array($type, $this->types, true)) {
			$this->types[] = $type;
		}
	}

	public function getTypeHint(): ?string
	{
		if (count($this->types) > 1) {
			return null;
		}
		$type = self::ALIAS_MAP[$this->type] ?? $this->type;

		if (in_array($type, self::INVALID_RETURN_TYPES, true)) {
			return null;
		}
		if ($this->isArray()) {
			return 'array';
		}

		return ($this->nullable ? '?' : '') . ($this->isTypeNamespaced() ? '\\' : '') . $type;
	}

	public function needDockBlockTypeHint(): bool
	{
		return $this->getTypeHint() === null || $this->isArray();
	}

	public function getDocBlockTypeHint(): ?string
	{
		if (count($this->types)) {
			$types = $this->types;
			if ($this->nullable) {
				$types[] = 'null';
			}
			return implode('|', $types);
		}

		$type = self::ALIAS_MAP[$this->type] ?? $this->type;
		return $type . ($this->nullable ? '|null' : '');
	}

	public function isNullable(): bool
	{
		return $this->nullable;
	}

	public function isUnion(): bool
	{
		return count($this->types) > 1;
	}

	public function isArray(): bool
	{
		$type = self::ALIAS_MAP[$this->type] ?? $this->type;
		return (substr($type, -2) === '[]' || strpos($type, 'array<') === 0);
	}

	public function getArrayType(): ?string
	{
		if (!$this->isArray()) {
			return null;
		}
		$type = self::ALIAS_MAP[$this->type] ?? $this->type;
		if (strpos($type, 'array<') !== false) {
			preg_match('/array\<.*,(\s+)?(.*)\>/', $type, $match);
			return $match[2] ?? null;
		}

		return str_replace('[]', '', $type);
	}

	public function isTypeNamespaced(): bool
	{
		return strpos($this->type, '\\') !== false;
	}

	public function is(string $type): bool
	{
		return (self::ALIAS_MAP[$this->type] ?? $this->type) === $type;
	}
}
