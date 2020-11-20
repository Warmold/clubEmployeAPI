<?php

namespace App\Converter;

use App\Serializer\Normalizer\CamelKeysNormalizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class RequestParameterConverter.
 */
class RequestParameterConverter implements ParamConverterInterface
{
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var CamelKeysNormalizer
     */
    private CamelKeysNormalizer $normalizer;

    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @var string
     */
    private string $validationErrorsArgument = 'validationErrors';

    /**
     * RequestParameterConverter constructor.
     *
     * @param SerializerInterface     $serializer
     * @param CamelKeysNormalizer     $normalizer
     * @param ValidatorInterface      $validator
     */
    public function __construct(
        SerializerInterface $serializer,
        CamelKeysNormalizer $normalizer,
        ValidatorInterface $validator
    ) {
        $this->serializer    = $serializer;
        $this->normalizer    = $normalizer;
        $this->validator     = $validator;
    }

    /**
     * @param Request        $request
     * @param ParamConverter $configuration
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        if (!\in_array($request->getMethod(), ['POST', 'PUT'])
            || (false === strpos($request->headers->get('Content-Type'), 'application/json'))) {
            return false;
        }

        $options      = (array) $configuration->getOptions();
        $arrayContext = [];

        if (isset($options['deserializationContext']) && \is_array($options['deserializationContext'])) {
            $arrayContext = $options['deserializationContext'];
        }

        $content = $this->normalizer->normalize(\json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR));

        try {
            $object = $this->serializer->deserialize(
                \json_encode($content, JSON_THROW_ON_ERROR),
                $configuration->getClass(),
                $request->getContentType(),
                $this->getContext($arrayContext)
            );
        } catch (\Exception $e) {
            return $this->throwException(
                new UnsupportedMediaTypeHttpException($e->getMessage(), $e),
                $configuration
            );
        }

        $request->attributes->set($configuration->getName(), $object);

        if (!isset($options['validate']) || $options['validate']) {
            $validatorOptions = $this->getValidatorOptions($options);

            $request->attributes->set(
                $this->validationErrorsArgument,
                $this->validator->validate($object, null, \array_merge($validatorOptions['groups'] ?? ['Default']))
            );
        }

        return true;
    }

    /**
     * @param ParamConverter $configuration
     *
     * @return bool
     */
    public function supports(ParamConverter $configuration): bool
    {
        return null !== $configuration->getClass() && 'app.request_params' === $configuration->getConverter();
    }

    /**
     * @param \Exception     $exception
     * @param ParamConverter $configuration
     *
     * @return bool
     *
     * @throws \Exception
     */
    private function throwException(\Exception $exception, ParamConverter $configuration): bool
    {
        if ($configuration->isOptional()) {
            return false;
        }

        throw $exception;
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function getContext(array $options): array
    {
        $context = [];

        foreach ($options as $key => $value) {
            if ('enableMaxDepth' === $key) {
                $context['enable_max_depth'] = true;
            } elseif ('serializeNull' === $key) {
                $context['skip_null_values'] = !$options['serializeNull'];
            } else {
                $context[$key] = $value;
            }
        }

        return $context;
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function getValidatorOptions(array $options): array
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'groups'   => null,
            'traverse' => false,
            'deep'     => false,
        ]);

        return $resolver->resolve($options['validator'] ?? []);
    }
}
