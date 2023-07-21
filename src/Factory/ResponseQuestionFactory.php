<?php

namespace App\Factory;

use App\Entity\ResponseQuestion;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<ResponseQuestion>
 *
 * @method        ResponseQuestion|Proxy           create(array|callable $attributes = [])
 * @method static ResponseQuestion|Proxy           createOne(array $attributes = [])
 * @method static ResponseQuestion|Proxy           find(object|array|mixed $criteria)
 * @method static ResponseQuestion|Proxy           findOrCreate(array $attributes)
 * @method static ResponseQuestion|Proxy           first(string $sortedField = 'id')
 * @method static ResponseQuestion|Proxy           last(string $sortedField = 'id')
 * @method static ResponseQuestion|Proxy           random(array $attributes = [])
 * @method static ResponseQuestion|Proxy           randomOrCreate(array $attributes = [])
 * @method static EntityRepository|RepositoryProxy repository()
 * @method static ResponseQuestion[]|Proxy[]       all()
 * @method static ResponseQuestion[]|Proxy[]       createMany(int $number, array|callable $attributes = [])
 * @method static ResponseQuestion[]|Proxy[]       createSequence(iterable|callable $sequence)
 * @method static ResponseQuestion[]|Proxy[]       findBy(array $attributes)
 * @method static ResponseQuestion[]|Proxy[]       randomRange(int $min, int $max, array $attributes = [])
 * @method static ResponseQuestion[]|Proxy[]       randomSet(int $number, array $attributes = [])
 */
final class ResponseQuestionFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'questionText' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(ResponseQuestion $responseQuestion): void {})
        ;
    }

    protected static function getClass(): string
    {
        return ResponseQuestion::class;
    }
}
