<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\ElasticService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Faker\Factory as Faker;

/**
 * @Route("/elastic")
 */
class ElasticController extends AbstractController
{
    /**
     * @var ElasticService
     */
    private ElasticService $service;

    /**
     * ElasticController constructor.
     * @param ElasticService $service
     */
    public function __construct(ElasticService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("/init")
     */
    public function init()
    {
        $params = [
            "settings"=> [
                "analysis"=> [
                    "analyzer"=> [
                        "my_analyzer"=> [
                            "tokenizer"=> "my_tokenizer"
                        ]
                    ],
                    "tokenizer"=> [
                        "my_tokenizer"=> [
                            "type"=> "ngram",
                            "min_gram" => 3,
                            "max_gram" => 3,
                            "token_chars" => [
                                "letter",
                                "digit"
                            ]
                        ]
                    ]
                ]
            ],
            'mappings' => [
                '_source' => [
                    'enabled' => true
                ],
                'properties' => [
                    'user_id' => [
                        'type' => 'integer'
                    ],
                    'first_name' => [
                        'type' => 'text',
                        "analyzer" => "my_analyzer"
                    ],
                    'last_name' => [
                        'type' => 'text'
                    ],
                ]
            ]
        ];
        $answer = $this->service->createIndex('action', $params);
        dd($answer);
    }

    /**
     * @Route("/show")
     * @return mixed
     */
    public function getAll()
    {
        $params = [
            'index' => 'action',
            'body'  => [
                'size' => 100,
                'query' => [
                    'match_all' => new \stdClass(),
                ]
            ]
        ];
        $response = $this->service->search($params);
        dd($response);
    }

    /**
     * @Route("/seeder")
     * @return Response
     */
    public function seeder(): Response
    {
        $this->service->seeder();
        return new Response('Данные импортированы', Response::HTTP_OK);
    }

    /**
     * @Route("/add")
     */
    public function addDocument()
    {
        $faker = Faker::create();
        $actions = ['createDraft', 'signDocument', 'login', 'logout', 'createBankRequest'];
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
        $response = $this->service->storeOrUpdate($params);
        dd($response);
    }

    /**
     * @Route("/remove")
     */
    public function removeDocument()
    {
        $params = [
            'index' => 'action',
            'id'    => 'id', // set desirable document id
        ];
        $response = $this->service->remove($params);
        dd($response);
    }

    /**
     * @Route("/fulltext")
     */
    public function fullTextsearch()
    {
        $params = [
            'index' => 'action',
            'body'  => [
                'size' => 100,
                'query' => [
                    'match_phrase' => ["first_name" => "elie"]
                ]
            ]
        ];
        $response = $this->service->search($params);
        dd($response);
    }
}