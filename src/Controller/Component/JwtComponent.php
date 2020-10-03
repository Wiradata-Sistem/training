<?php
declare(strict_types=1);

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWK;
use Jose\Component\Signature\Algorithm\HS256;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use Jose\Component\Signature\JWSLoader;

/**
 * Jwt component
 */
class JwtComponent extends Component
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function GetToken($payload)
    {
        $serializer = new CompactSerializer(); // The serializer
        // We serialize the signature at index 0 (we only have one signature).
        return $serializer->serialize($this->getBuilder($payload), 0); 
    }

    public function Claim($token){
        $jwsVerifier = new JWSVerifier($this->getAlghoritm());
        $jwk = $this->getKey();
        $serializerManager = new JWSSerializerManager([new CompactSerializer()]);
        $jws = $serializerManager->unserialize($token);
            if ($jwsVerifier->verifyWithKey($jws, $jwk, 0)) {
        $jwsLoader = new JWSLoader($serializerManager, $jwsVerifier, null);
            $jws = $jwsLoader->loadAndVerifyWithKey($token, $jwk, $signature, null);
            $payload = (array) json_decode($jws->getPayload());
            if ($payload['exp'] >= time()) return [true, $payload];
            return [false, 'token has been expired'];
        }
        return [false, 'token no verified'];
    }
    

    private function getAlghoritm()
    {
        return new AlgorithmManager([new HS256()]);
    }

    private function getKey()
    {
        $defaultKey = 'dzI6nbW4OcNF-AtfxGAmuyz7IpHRudBI0WgGjZWgaRJt6prBn3DARXgUR8NVwKhfL43QBIU2Un3AvCGCHRgY4TbEqhOi8-i98xxmCggNjde4oaW6wkJ2NgM3Ss9SOX9zS3lcVzdCMdum-RwVJ301kbin4UtGztuzJBeg5oVN00MGxjC2xWwyI0tgXVs-zJs5WlafCuGfX1HrVkIf5bvpE0MQCSjdJpSeVao6-RSTYDajZf7T88a2eVjeW31mMAg-jzAWfUrii61T_bYPJFOXW8kkRWoa1InLRdG6bKB9wQs9-VdXZP60Q4Yuj_WZ-lO7qV9AEFrUkkjpaDgZT86w2g1V3j5T';
        $salt = base64_encode(env('SECURITY_SALT', '20a154bd44cf73a3ef2dc4caf4d8922e561deb6d12c4a946379b05d0cfe0deea'));
        return new JWK([
            'kty' => 'oct',
            'k' => str_replace('=', '-', substr($salt.$defaultKey, 0, 348)),
        ]);
    }

    private function getBuilder($payload)
    {
        // We instantiate our JWS Builder.
        $jwsBuilder = new JWSBuilder($this->getAlghoritm());
        $jws = $jwsBuilder->create() // We want to create a new JWS
            ->withPayload($payload) // We set the payload
            ->addSignature($this->getKey(), ['alg' => 'HS256']) // signature with a simple protected header
            ->build(); // We build it
        return $jws;
    }


}
