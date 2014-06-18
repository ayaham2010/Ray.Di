<?php
/**
 * This file is part of the Ray package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace Ray\Di;

use Ray\Aop\Bind;
use Aura\Di\ConfigInterface;
use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;

final class CompilationLogger implements CompilationLoggerInterface, \Serializable
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var DependencyFactory[]
     */
    private $dependencyContainer = [];

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var \SplObjectStorage
     */
    private $objectStorage;

    /**
     * @var int
     */
    private $storageCnt = 0;

    /**
     * @var string
     */
    private $log = '';

    /**
     * @param LoggerInterface $logger
     *
     * @Inject
     * @Named("logger")
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->objectStorage = new \SplObjectStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(ConfigInterface $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function log($class, array $params, array $setters, $instance, Bind $bind)
    {
        if ($instance instanceof DependencyProvider) {
            $this->buildProvider($instance);

            return;
        }
        $this->logger->log($class, $params, $setters, $instance, $bind);
        $this->build($class, $instance, $params, $setters);
    }

    /**
     * {@inheritdoc}
     */
    public function newInstance($ref)
    {
        if (! isset($this->dependencyContainer[$ref])) {
            error_log($this->log);
            throw new Exception\Compile($ref);
        }

        return $this->dependencyContainer[$ref]->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectHash($object)
    {
        if ($this->objectStorage->contains($object)) {
            return $this->objectStorage[$object];
        }
        $this->storageCnt++;
        $hash = (string) $this->storageCnt;
        $this->objectStorage[$object] = $hash;
        // object hash logging for debug
        $shortHash = function ($data, $algo = 'CRC32') {
            return strtr(rtrim(base64_encode(pack('H*', $algo($data))), '='), '+/', '-_');
        };
        $log = sprintf(
            'ray/di.install ref:%s class:%s hash:%s',
            $hash,
            get_class($object),
            $shortHash(spl_object_hash($object))
        );
        $this->errorLog($log);

        return $hash;
    }

    /**
     * @return array
     */
    public function setClassMap(array $classMap, $class)
    {
        if (isset($classMap[$class])) {
            return $classMap;
        }
        $container = $this->dependencyContainer;
        $factory = array_pop($container);
        /** @var $factory DependencyFactory */
        $classMap[$class] = (string) $factory;
        $log = sprintf(
            'ray/di.map     ref:%s class:%s',
            $factory,
            $class
        );
        $this->errorLog($log);
        return $classMap;
    }

    /**
     * @param string $class
     * @param object $instance
     * @param array  $params
     * @param array  $setters
     */
    private function build($class, $instance, array $params, array $setters)
    {
        $params = $this->makeParamRef($params);
        foreach ($setters as &$methodPrams) {
            $methodPrams = $this->makeParamRef($methodPrams);
        }

        $dependencyFactory = new DependencyFactory($instance, $params, $setters, $this);
        list(,,$definition) = $this->config->fetch($class);
        // @PostConstruct
        $postConstructMethod = $definition['PostConstruct'];
        $dependencyFactory->setPostConstruct($postConstructMethod);
        // aop ?
        if (isset($instance->rayAopBind)) {
            $interceptors = $this->buildInterceptor($instance);
            $dependencyFactory->setInterceptors($interceptors);
        }
        $index = (string) $dependencyFactory;
        $this->dependencyContainer[$index] = $dependencyFactory;
        $diLog = $dependencyFactory->getDependencyLog();
        if ($diLog) {
            $this->errorLog('ray/di.depends ' . $diLog);
        }
        $aopLog = $dependencyFactory->getInterceptorLog();
        if ($aopLog) {
            $this->errorLog('ray/di.aspect  ' . $aopLog);
        }
    }

    /**
     * @param object $instance
     *
     * @return array
     */
    private function buildInterceptor($instance)
    {
        $boundInterceptors = (array) $instance->rayAopBind; // 'methodName' => methodInterceptors[]
        foreach ($boundInterceptors as &$methodInterceptors) {
            foreach ($methodInterceptors as &$methodInterceptor) {
                /** @var $methodInterceptor \Ray\Aop\MethodInterceptor */
                $methodInterceptor = $this->getRef($methodInterceptor);
            }
        }

        return $boundInterceptors;
    }

    /**
     * @param DependencyProvider $dependencyProvider
     */
    private function buildProvider(DependencyProvider $dependencyProvider)
    {
        $instanceHash = $this->getObjectHash($dependencyProvider->instance);
        $providerHash = $this->getObjectHash($dependencyProvider->provider);
        $dependencyReference = new DependencyReference($providerHash, $this, get_class($dependencyProvider));
        $this->dependencyContainer[$instanceHash] = $dependencyReference;
    }
    /**
     * @param array $params
     *
     * @return array
     */
    private function makeParamRef(array $params)
    {
        foreach ($params as &$param) {
            if (is_object($param)) {
                $param = $this->getRef($param);
            }
        }

        return $params;
    }

    /**
     * @param object $instance
     *
     * @return DependencyReference
     */
    private function getRef($instance)
    {
        $hash = $this->getObjectHash($instance);
        $type = is_object($instance) ? get_class($instance) : gettype($instance);
        return new DependencyReference($hash, $this, $type);
    }

    /**
     * @param string $log
     *
     * Uncomment for error_log to log
     */
    private function errorLog($log)
    {
        error_log($log);
        $this->log .= $log . PHP_EOL;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->logger;
    }

    public function serialize()
    {
        $serialized = serialize(
            [
                $this->dependencyContainer
            ]
        );

        return $serialized;
    }

    public function unserialize($serialized)
    {
        list(
            $this->dependencyContainer
        ) = unserialize($serialized);
    }
}
