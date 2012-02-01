<?php

// vendor/*
require_once dirname(__DIR__) . '/vendor/Doctrine.Common/lib/Doctrine/Common/ClassLoader.php';
$commonLoader = new \Doctrine\Common\ClassLoader('Doctrine\Common', dirname(__DIR__) . '/vendor/Doctrine.Common/lib');
$commonLoader->register();
require_once dirname(__DIR__) . '/vendor/Ray.Aop/src.php';

// Definition class with annotation
require_once __DIR__ . '/Definition/Basic.php';
require_once __DIR__ . '/Definition/Named.php';
require_once __DIR__ . '/Definition/InvalidNamed.php';
require_once __DIR__ . '/Definition/Instance.php';
require_once __DIR__ . '/Definition/Construct.php';
require_once __DIR__ . '/Definition/Implemeted.php';
require_once __DIR__ . '/Definition/LifeCycle.php';
require_once __DIR__ . '/Definition/Provided.php';
require_once __DIR__ . '/Definition/Multi.php';
require_once __DIR__ . '/Definition/MockDefinitionClass.php';
require_once __DIR__ . '/Definition/MockDefinitionChildClass.php';
require_once __DIR__ . '/Definition/MockDefinitionChildOverrideClass.php';
require_once __DIR__ . '/Definition/MockDefinitionMultiplePostConstructClass.php';
require_once __DIR__ . '/Definition/ClassHint.php';
require_once __DIR__ . '/Definition/AbstractBasic.php';
// Modules
require_once __DIR__ . '/Modules/AnnotateModule.php';
require_once __DIR__ . '/Modules/InvalidAnnotateModule.php';
require_once __DIR__ . '/Modules/DbProvider.php';
require_once __DIR__ . '/Modules/BasicModule.php';
require_once __DIR__ . '/Modules/InstanceModule.php';
require_once __DIR__ . '/Modules/MultiModule.php';
require_once __DIR__ . '/Modules/ProviderModule.php';
require_once __DIR__ . '/Modules/InstanceModule.php';
require_once __DIR__ . '/Modules/ReaderProvider.php';
require_once __DIR__ . '/Modules/SingletonModule.php';
require_once __DIR__ . '/Modules/PrototypeModule.php';
require_once __DIR__ . '/Modules/ClosureModule.php';
require_once __DIR__ . '/Modules/MultiModule.php';
require_once __DIR__ . '/Modules/AopModule.php';
require_once __DIR__ . '/Modules/AopMatcherModule.php';
require_once __DIR__ . '/Modules/AopMisMatchModule.php';
require_once __DIR__ . '/Modules/InvalidProviderModule.php';
require_once __DIR__ . '/Modules/InvalidToModule.php';
require_once __DIR__ . '/Modules/ArrayInstance.php';
// Mock class without annotation
require_once __DIR__ . '/MockParentClass.php';
require_once __DIR__ . '/MockChildClass.php';
require_once __DIR__ . '/MockOtherClass.php';
require_once __DIR__ . '/Mock/DbInterface.php';
require_once __DIR__ . '/Mock/Db.php';
require_once __DIR__ . '/Mock/AdminDb.php';
require_once __DIR__ . '/Mock/UserDb.php';
require_once __DIR__ . '/Mock/RndDb.php';
require_once __DIR__ . '/Mock/UserInterface.php';
require_once __DIR__ . '/Mock/User.php';
require_once __DIR__ . '/Mock/LogInterface.php';
require_once __DIR__ . '/Mock/Log.php';
require_once __DIR__ . '/Mock/ReaderInterface.php';
require_once __DIR__ . '/Mock/Reader.php';
require_once __DIR__ . '/Mock/Construct.php';
require_once __DIR__ . '/Mock/AbstractDb.php';
// Ray.Aop
require_once __DIR__ . '/Aop/BillingService.php';
require_once __DIR__ . '/Aop/RealBillingService.php';
require_once __DIR__ . '/Aop/SalesTax.php';