<?php


namespace App\Controller;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;


abstract class ApiController extends AbstractController
{
    private ?SerializerInterface $serializer = null;

    private ?FilesystemAdapter $cache = null;

    public function __construct()
    {
        $this->serializer = $this->getSerializer();
        $this->cache = $this->getCache();
    }

    public function getSerializer(): SerializerInterface
    {
        if ($this->serializer !== null) {
            return $this->serializer;
        }

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer($classMetadataFactory)];

        return new Serializer($normalizers, $encoders);
    }

    public function getCache(): FilesystemAdapter
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        return new FilesystemAdapter();
    }

    public function makeResponse($result, $group, $status = 200): JsonResponse
    {
        $error = '';
        if ($status === 404) {
            $error = 'Resource doesn\'t exist';
        }

        if ($status === 422) {
            $error = 'Unprocessable Entity';
        }

        if ($error !== '') {
            return new JsonResponse(
                json_encode([
                    "error" => $error
                ]),
                $status,
                [],
                'json'
            );
        }

        if (! is_string($result)) {
            $result = $this->getSerializer()->serialize($result, 'json',  ['groups' => $group]);
        }

        return new JsonResponse(
            $result,
            $status,
            [],
            'json'
        );
    }
}