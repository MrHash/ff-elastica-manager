<?php
namespace Example;

use Elastica\Type;
use FF\ElasticaManager\Configuration;

class ShopConfiguration extends Configuration
{
	const NAME  = 'shop';
	const ALIAS = 'shop_active';

	public function getName()
	{
		return self::NAME;
	}

	public function getAlias()
	{
		return self::ALIAS;
	}

	public function getTypes()
	{
		return array('book', 'dvd');
	}

	public function getConfig()
	{
		return array(
			'number_of_shards'   => 4,
			'number_of_replicas' => 1,
			'analysis'           => array(
				'analyzer' => array(
					'shop' => array(
						'type'      => 'custom',
						'tokenizer' => 'standard',
						'filter'    => array('lowercase', 'standard', 'asciifolding', 'shop_ngrams')
					),
				),
				'filter'   => array(
					'shop_ngrams' => array(
						'type'     => 'edgeNGram',
						'min_gram' => 2,
						'max_gram' => 10,
						'side'     => 'front'
					)
				)
			)
		);
	}

	public function getMappingParams(Type $type)
	{
		return array(
			'_all' => array(
				'enabled' => false
			)
		);
	}

	public function getMappingProperties(Type $type)
	{
		$array = array(
			'name'  => array(
				'type'   => 'multi_field',
				'fields' => array(
					'partial_name' => array(
						'search_analyzer' => 'standard',
						'index_analyzer'  => 'shop',
						'type'            => 'string',
					),
					'full_name'    => array(
						'type' => 'string',
					)
				)
			),
			'image' => array(
				'type'  => 'string',
				'index' => 'no'
			)
		);

		switch ($type->getName()) {
			case 'book':
				$array += array(
					'author' => array('type' => 'string'),
				);
				break;
			case 'dvd':
				$array += array(
					'released' => array('type' => 'date'),
				);
				break;
		}
		return $array;
	}
}
