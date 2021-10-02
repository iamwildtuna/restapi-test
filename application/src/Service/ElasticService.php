<?php

namespace App\Service;

use Elasticsearch\ClientBuilder;
use Faker\Factory as Faker;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ElasticService
{
    /**
     * @var ClientBuilder
     */
    private $elasticClient;

    /**
     * @param array $hosts
     * @param string $login
     * @param string $basic_password
     */
    public function __construct(array $hosts, string $login, string $password)
    {
        $this->elasticClient = ClientBuilder::create()
            ->setHosts($hosts)
            ->setBasicAuthentication($login, $password)
            ->build();
    }

    /**
     * @param string $index
     * @param array $params
     * @return array
     */
    public function createIndex(string $index, array $params)
    {
        $paramsCreate = [
            'index' => $index,
            'body' => $params
        ];

//        $response = $this->elasticClient->indices()->putMapping($paramsEdit);
        return $this->elasticClient->indices()->create($paramsCreate);
    }

    /**
     * @param string $index
     * @return array
     */
    public function closeIndex(string $index)
    {
        $params = [
            'index' => $index,
        ];
        return $this->elasticClient->indices()->close($params);
    }

    /**
     * @param string $index
     * @return array
     */
    public function openIndex(string $index)
    {
        $params = [
            'index' => $index,
        ];
        return $this->elasticClient->indices()->open($params);
    }

    /**
     * @param string $index
     * @param string $id
     * @return array
     */
    public function getById(string $index, string $id)
    {
        $params = [
            'index' => $index,
            'id' => $id
        ];
        return $this->elasticClient->get($params);
    }

    /**
     * @param string $index
     * @return array
     */
    public function deleteIndex(string $index)
    {
        $params = ['index' => $index];
        return $response = $this->elasticClient->indices()->delete($params);
    }

    /**
     * @param array $params
     * @return array
     */
    public function search(array $params)
    {
        return $this->elasticClient->search($params);
    }

    /**
     * @param array $params
     * @return array
     */
    public function addOrUpdate(array $params)
    {
        return $this->elasticClient->index($params);
    }

    public function seeder(): void
    {
        $faker = Faker::create();
        $actions = ['createDraft', 'signDocument', 'login', 'logout', 'createBankRequest'];

        for ($i=0; $i<100; $i++) {
            $params = [
                'index' => 'action',
                'id'    => $faker->uuid,
                'body'  => [
                    'user_id' => $faker->numberBetween(1,10),
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'date' => $faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d H:i:s'),
                    'action' => $faker->randomElement($actions),
                    'description' => $faker->realText()
                ]
            ];
            $this->elasticClient->index($params);
        }
    }

    /**
     * Document will be stored or updated when exist
     * @param array $params
     * @return array
     */
    public function storeOrUpdate(array $params)
    {
        return $this->elasticClient->index($params);
    }

    /**
     * Delete elements from index by different parameters
     * @param array $params
     * @return array
     */
    public function remove(array $params)
    {
        return $this->elasticClient->delete($params);
    }
}