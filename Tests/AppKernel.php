<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * AppKernel.
 *  
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\HttpKernel\KernelInterface::registerBundles()
     */
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),

            new ASF\CoreBundle\ASFCoreBundle(),
            new ASF\ProductBundle\ASFProductBundle(),
        );

        return $bundles;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\HttpKernel\Kernel::getRootDir()
     */
    public function getRootDir()
    {
        return __DIR__;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\HttpKernel\Kernel::getCacheDir()
     */
    public function getCacheDir()
    {
        return __DIR__.'/Fixtures/var/cache/'.$this->getEnvironment();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\HttpKernel\Kernel::getLogDir()
     */
    public function getLogDir()
    {
        return __DIR__.'/Fixtures/var/logs';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\HttpKernel\KernelInterface::registerContainerConfiguration()
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $platform = 'windows';
        } else {
            $platform = 'unix';
        }

        $loader->load($this->getRootDir().'/config/config_'.$platform.'.yml');
    }
}
