<?php

namespace SenseiTarzan\SymplyPlugin\Manager\Component;

use pocketmine\utils\RegistryTrait;

class SymplyShapelessRecipeType
{
	use RegistryTrait;

	public function __construct(
		private readonly string $type
	)
	{
	}

	public static function register(string $name, SymplyShapelessRecipeType $type) : void{
		self::_registryRegister($name, $type);
	}

	public static function exist(string $name): bool
	{

		$reflection = new \ReflectionClass(self::class);
		$members = $reflection->getStaticPropertyValue("members");
		return isset($members[mb_strtoupper($name)]);
	}

	/**
	 * @return SymplyShapelessRecipeType[]
	 * @phpstan-return array<string, SymplyShapelessRecipeType>
	 */
	public static function getAll() : array{
		//phpstan doesn't support generic traits yet :(
		/** @var SymplyShapelessRecipeType[] $result */
		$result = self::_registryGetAll();
		return $result;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	protected static function setup(): void
	{
		// TODO: Implement setup() method.
	}
}