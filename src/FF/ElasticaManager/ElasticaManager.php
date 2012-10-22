<?php
namespace FF\ElasticaManager;

use Elastica_Client;

class ElasticaManager
{
	/** @var Elastica_Client */
	protected $client;

	/** @var IndexConfiguration[] */
	protected $configurations;

	/** @var IndexDataProvider[] */
	protected $providers;

	/** @var IndexManager[] */
	protected $indexManagers;

	public function __construct(Elastica_Client $client)
	{
		$this->client = $client;
	}

	/**
	 * @return Elastica_Client
	 */
	public function getClient()
	{
		return $this->client;
	}

	public function addConfiguration(IndexConfiguration $configuration)
	{
		$this->configurations[$configuration->getName()] = $configuration;
	}

	/**
	 * @param $configurationName
	 * @return IndexConfiguration
	 */
	public function getConfiguration($configurationName)
	{
		return $this->configurations[$configurationName];
	}

	/**
	 * @param $configurationName
	 * @param $indexName
	 * @return IndexManager
	 */
	public function getIndexManager($configurationName, $indexName = null)
	{
		$configuration = $this->getConfiguration($configurationName);
		$indexName     = $indexName ? : $configuration->getName();
		if (isset($this->indexManagers[$indexName])) {
			return $this->indexManagers[$indexName];
		}

		return $this->indexManagers[$indexName] = new IndexManager($this->client, $configuration, $indexName);
	}
}