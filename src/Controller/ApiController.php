<?php


namespace App\Controller;


use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

abstract class ApiController extends AbstractController
{
    public function getSerializer()
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer($classMetadataFactory)];

        return new Serializer($normalizers, $encoders);
    }

    public function makeResponse($result, $group, $status = 200)
    {
        if ($result === null) {
            return new JsonResponse(
                json_encode([
                    "error" => "Resource doesn't exist"
                ]),
                404,
                [],
                'json'
            );
        }

        return new JsonResponse(
            $this->getSerializer()->serialize($result, 'json',  ['groups' => $group]),
            $status,
            [],
            'json'
        );
    }
}